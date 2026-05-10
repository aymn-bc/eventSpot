<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Enum\StatutInscription;
use App\Form\EvenementType;
use App\Form\InscriptionType;
use App\Repository\EvenementRepository;
use App\Service\EvenementManager;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

class EvenementController extends AbstractController
{
    public function __construct(
        private EvenementManager $evenementManager
    ) {}

    // Page d'accueil : 6 prochains événements
    #[Route('/', name: 'app_accueil', methods: ['GET'])]
    public function accueil(EvenementRepository $repo, Request $request): Response
    {
        $session = $request->getSession();
        $eventsId = $session->get('eventsId', []);
        $latestEvents = $repo->findFiveById($eventsId);
        $evenements = $repo->findUpcoming(6);
        $evenementsParCategorie = $this->evenementManager->getEvenementsParCategorie();

        return $this->render('evenement/accueil.html.twig', [
            'evenements' => $evenements,
            'latestEvents' => $latestEvents,
            'evenementsParCategorie' => $evenementsParCategorie,
        ]);
    }

        // Liste de tous les événements
    #[Route('/evenements', name: 'app_evenements_liste', methods: ['GET'])]
    public function liste(EvenementRepository $repo, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $eventsId = $session->get('eventsId', []);
        $latestEvents = $repo->findFiveById($eventsId);

        $query = $repo->createQueryBuilder('e')
            ->orderBy('e.dateDebut', 'ASC')
            ->getQuery();

        $evenements = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('evenement/liste.html.twig', [
            'evenements' => $evenements,
            'latestEvents' => $latestEvents,
        ]);
    }
    // Créer un événement (AVANT detail !)
    #[IsGranted('ROLE_ORGANISATEUR')]
    #[Route('/evenements/nouveau', name: 'app_evenements_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = $fileUploader->upload($imageFile);
                $evenement->setImageName($newFilename);
            }

            $evenement->setDateCreation();
            $evenement->setOrganisateur($this->getUser());
            $em->persist($evenement);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_evenements_liste');
        }

        return $this->render('evenement/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Détail d'un événement
    #[Route('/evenements/{id}', name: 'app_evenements_detail', methods: ['GET'])]
    public function detail(Evenement $evenement, Request $request): Response
    {
        $session = $request->getSession();
        $eventId = $evenement->getId();
        $eventsId = $session->get('eventsId', []);

        $key = array_search($eventId, $eventsId);
        if ($key !== false) {
            unset($eventsId[$key]);
        }
        $session->set('eventsId', [$eventId, ...$eventsId]);

        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
            'nbInscrits' => $this->evenementManager->getNbInscrits($evenement),
            'placesRestantes' => $this->evenementManager->getPlacesRestantes($evenement),
            'estInscrit' => $this->getUser() ? $this->evenementManager->estInscrit($this->getUser(), $evenement) : false,
        ]);
    }

    // Modifier un événement
    #[IsGranted('ROLE_ORGANISATEUR')]
    #[Route('/evenements/{id}/modifier', name: 'app_evenements_modifier', methods: ['GET', 'POST'])]
    public function modifier(Request $request, Evenement $evenement, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $oldImageName = $evenement->getImageName();
                if ($oldImageName) {
                    $fileUploader->remove($oldImageName);
                }
                $evenement->setImageName($fileUploader->upload($imageFile));
            }

            if (!$evenement->getOrganisateur()) {
                $evenement->setOrganisateur($this->getUser());
            }

            $em->flush();

            $this->addFlash('success', 'Événement modifié avec succès !');
            return $this->redirectToRoute('app_evenements_liste');
        }

        return $this->render('evenement/modifier.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }

    // Supprimer un événement (CSRF)
    #[IsGranted('ROLE_ORGANISATEUR')]
    #[Route('/evenements/{id}/supprimer', name: 'app_evenements_supprimer', methods: ['POST'])]
    public function supprimer(Request $request, Evenement $evenement, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $imageName = $evenement->getImageName();
            if ($imageName){
                $fileUploader->remove($imageName);
            }
            
            $em->remove($evenement);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_evenements_liste');
    }

    // S'inscrire à un événement
    #[IsGranted('ROLE_USER')]
    #[Route('/evenements/{id}/inscription', name: 'app_evenements_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request, Evenement $evenement, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->evenementManager->estInscrit($this->getUser(), $evenement)) {
                $this->addFlash('danger', 'Vous êtes déjà inscrit à cet événement.');
                return $this->redirectToRoute('app_evenements_detail', ['id' => $evenement->getId()]);
            }

            $inscription->setEvenement($evenement);
            $inscription->setParticipant($this->getUser());
            $inscription->setDateInscription();
            $inscription->setStatus(StatutInscription::CONFIRMEE);
            $em->persist($inscription);
            $em->flush();

            // Envoi email de confirmation
            $user = $this->getUser();
            $email = (new Email())
                ->from('noreply@eventspot.com')
                ->to($user->getUserIdentifier())
                ->subject('🎫 Inscription confirmée : ' . $evenement->getTitre())
                ->html(
                    $this->renderView('emails/confirmation_inscription.html.twig', [
                        'titre' => $evenement->getTitre(),
                        'dateDebut' => $evenement->getDateDebut()->format('d/m/Y H:i'),
                        'lieu' => $evenement->getLieu(),
                        'participant' => $user->getUserIdentifier(),
                    ])
                );

            $mailer->send($email);

            $this->addFlash('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_evenements_detail', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/inscription.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }
}