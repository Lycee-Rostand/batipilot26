<?php

namespace App\Controller;

use App\Entity\ChantierTache;
use App\Form\ChantierTache1Type;
use App\Repository\ChantierTacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chantier/tache')]
final class ChantierTacheController extends AbstractController
{
    #[Route('/list',name: 'app_chantier_tache_index', methods: ['GET'])]
    public function index(ChantierTacheRepository $chantierTacheRepository): Response
    {
        return $this->render('chantier_tache/index.html.twig', [
            'chantier_taches' => $chantierTacheRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chantier_tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chantierTache = new ChantierTache();
        $form = $this->createForm(ChantierTache1Type::class, $chantierTache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chantierTache);
            $entityManager->flush();

            return $this->redirectToRoute('app_chantier_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier_tache/new.html.twig', [
            'chantier_tache' => $chantierTache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chantier_tache_show', methods: ['GET'])]
    public function show(ChantierTache $chantierTache): Response
    {
        return $this->render('chantier_tache/show.html.twig', [
            'chantier_tache' => $chantierTache,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chantier_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChantierTache $chantierTache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChantierTache1Type::class, $chantierTache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chantier_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier_tache/edit.html.twig', [
            'chantier_tache' => $chantierTache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chantier_tache_delete', methods: ['POST'])]
    public function delete(Request $request, ChantierTache $chantierTache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chantierTache->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chantierTache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chantier_tache_index', [], Response::HTTP_SEE_OTHER);
    }
}
