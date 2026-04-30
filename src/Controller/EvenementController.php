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

class EvenementController extends AbstractController
{
    // Page d'accueil : 6 prochains événements
    #[Route('/', name: 'app_accueil', methods: ['GET'])]
    public function accueil(EvenementRepository $repo): Response
    {
        $evenements = $repo->findProchains(6);
        return $this->render('evenement/accueil.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    // Liste de tous les événements
    #[Route('/evenements', name: 'app_evenements_liste', methods: ['GET'])]
    public function liste(EvenementRepository $repo): Response
    {
        $evenements = $repo->findAll();
        return $this->render('evenement/liste.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    // Détail d'un événement
    #[Route('/evenements/{id}', name: 'app_evenements_detail', methods: ['GET'])]
    public function detail(Evenement $evenement): Response
    {
        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    // Créer un événement
    #[Route('/evenements/nouveau', name: 'app_evenements_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $evenement->setImageName($newFilename);
            }

            $evenement->setDateCreation();
            $em->persist($evenement);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_evenements_liste');
        }

        return $this->render('evenement/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Modifier un événement
    #[Route('/evenements/{id}/modifier', name: 'app_evenements_modifier', methods: ['GET', 'POST'])]
    public function modifier(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $evenement->setImageName($newFilename);
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