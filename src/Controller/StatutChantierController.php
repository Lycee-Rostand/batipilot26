<?php

namespace App\Controller;

use App\Entity\StatutChantier;
use App\Form\StatutChantierType;
use App\Repository\StatutChantierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/statut/chantier')]
final class StatutChantierController extends AbstractController
{
    #[Route(name: 'app_statut_chantier_index', methods: ['GET'])]
    public function index(StatutChantierRepository $statutChantierRepository): Response
    {
        return $this->render('statut_chantier/index.html.twig', [
            'statut_chantiers' => $statutChantierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_statut_chantier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $statutChantier = new StatutChantier();
        $form = $this->createForm(StatutChantierType::class, $statutChantier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($statutChantier);
            $entityManager->flush();

            return $this->redirectToRoute('app_statut_chantier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('statut_chantier/new.html.twig', [
            'statut_chantier' => $statutChantier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_statut_chantier_show', methods: ['GET'])]
    public function show(StatutChantier $statutChantier): Response
    {
        return $this->render('statut_chantier/show.html.twig', [
            'statut_chantier' => $statutChantier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_statut_chantier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StatutChantier $statutChantier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StatutChantierType::class, $statutChantier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_statut_chantier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('statut_chantier/edit.html.twig', [
            'statut_chantier' => $statutChantier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_statut_chantier_delete', methods: ['POST'])]
    public function delete(Request $request, StatutChantier $statutChantier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$statutChantier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($statutChantier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_statut_chantier_index', [], Response::HTTP_SEE_OTHER);
    }
}
