<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Form\EvenementType;
use App\Form\InscriptionType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EvenementController extends AbstractController
{
    // Page d'accueil : 6 prochains événements
    #[Route('/', name: 'app_accueil', methods: ['GET'])]
    public function accueil(EvenementRepository $repo, Request $request): Response
    {
        $session = $request->getSession();
        $eventsId = $session->get('eventsId', []);
        $latestEvents = $repo->findFiveById($eventsId);
        
        $evenements = $repo->findProchains(6);
        return $this->render('evenement/accueil.html.twig', [
            'evenements' => $evenements,
            'latestEvents' => $latestEvents,
        ]);
    }

    // Liste de tous les événements
    #[Route('/evenements', name: 'app_evenements_liste', methods: ['GET'])]
    public function liste(EvenementRepository $repo, Request $request): Response
    {
        $session = $request->getSession();
        $eventsId = $session->get('eventsId', []); 
        
        $latestEvents = $repo->findFiveById($eventsId);
        $evenements = $repo->findAll();
        return $this->render('evenement/liste.html.twig', [
            'evenements' => $evenements,
            'latestEvents' => $latestEvents
        ]);
    }

    // Créer un événement (AVANT detail !)
    #[IsGranted('ROLE_ORGANISATEUR')]
    #[Route('/evenements/nouveau', name: 'app_evenements_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $evenement->setImageName($newFilename);
            }

            $evenement->setDateCreation();
            $evenement->addOrganisateur($this->getUser());
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
        if ($key){
            unset($eventsId[$key]);
        }
        $session->set('eventsId', [$eventId, ...$eventsId]);
        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    // Modifier un événement
    #[IsGranted('ROLE_ORGANISATEUR')]
    #[Route('/evenements/{id}/modifier', name: 'app_evenements_modifier', methods: ['GET', 'POST'])]
    public function modifier(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $evenement->setImageName($newFilename);
            }

            // Ensure the current user is among the organizers if not already
            if (!$evenement->getOrganisateur()->contains($this->getUser())) {
                $evenement->addOrganisateur($this->getUser());
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
    public function supprimer(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $em->remove($evenement);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_evenements_liste');
    }

    // S'inscrire à un événement
    #[IsGranted('ROLE_USER')]
    #[Route('/evenements/{id}/inscription', name: 'app_evenements_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inscription->setEvenement($evenement);
            $em->persist($inscription);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('app_evenements_detail', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/inscription.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }
}