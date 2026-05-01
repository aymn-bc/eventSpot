<?php

namespace App\Controller;

use App\Entity\TagEvenemement;
use App\Form\TagEvenementType;
use App\Repository\TagEvenemementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TagEvenementController extends AbstractController
{
    // Liste des tags
    #[Route('/tags', name: 'app_tags_liste', methods: ['GET'])]
    public function liste(TagEvenemementRepository $repo): Response
    {
        $tags = $repo->findAll();
        return $this->render('tag_evenement/liste.html.twig', [
            'tags' => $tags,
        ]);
    }

    // Créer un tag
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/tags/nouveau', name: 'app_tags_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new TagEvenemement();
        $form = $this->createForm(TagEvenementType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'Tag créé avec succès !');
            return $this->redirectToRoute('app_tags_liste');
        }

        return $this->render('tag_evenement/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un tag (CSRF)
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/tags/{id}/supprimer', name: 'app_tags_supprimer', methods: ['POST'])]
    public function supprimer(Request $request, TagEvenemement $tag, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
            $em->remove($tag);
            $em->flush();
            $this->addFlash('success', 'Tag supprimé avec succès !');
        }

        return $this->redirectToRoute('app_tags_liste');
    }
}