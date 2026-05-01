<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LieuController extends AbstractController
{
    // Liste des lieux
    #[Route('/lieux', name: 'app_lieux_liste', methods: ['GET'])]
    public function liste(LieuRepository $repo): Response
    {
        $lieux = $repo->findAll();
        return $this->render('lieu/liste.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    // Créer un lieu
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/lieux/nouveau', name: 'app_lieux_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu);
            $em->flush();

            $this->addFlash('success', 'Lieu créé avec succès !');
            return $this->redirectToRoute('app_lieux_liste');
        }

        return $this->render('lieu/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}