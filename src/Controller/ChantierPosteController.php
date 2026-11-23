<?php

namespace App\Controller;

use App\Entity\ChantierPoste;
use App\Form\ChantierPosteType;
use App\Repository\ChantierPosteRepository;
use App\Repository\ChantierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chantier/poste')]
final class ChantierPosteController extends AbstractController
{
    #[Route(name: 'app_chantier_poste_index', methods: ['GET'])]
    public function index(ChantierPosteRepository $chantierPosteRepository): Response
    {
        return $this->render('chantier_poste/index.html.twig', [
            'chantier_postes' => $chantierPosteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chantier_poste_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ChantierRepository $chantierRepository
    ): Response {
        $chantierPoste = new ChantierPoste();

        $chantierId = $request->query->getInt('chantier');
        $chantier = $chantierId > 0 ? $chantierRepository->find($chantierId) : null;
        $lockChantier = false;

        if ($chantier !== null) {
            $chantierPoste->setChantier($chantier);
            $lockChantier = true;
        }

        $form = $this->createForm(ChantierPosteType::class, $chantierPoste, [
            'lock_chantier' => $lockChantier,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chantierPoste);
            $entityManager->flush();

            $targetChantierId = $chantierPoste->getChantier()?->getId();
            if ($targetChantierId !== null) {
                return $this->redirectToRoute('app_chantier_show', [
                    'id' => $targetChantierId,
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_chantier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier_poste/new.html.twig', [
            'chantier_poste' => $chantierPoste,
            'chantier' => $chantier,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_chantier_poste_show', methods: ['GET'])]
    public function show(ChantierPoste $chantierPoste): Response
    {
        return $this->render('chantier_poste/show.html.twig', [
            'chantier_poste' => $chantierPoste,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_chantier_poste_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ChantierPoste $chantierPoste,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ChantierPosteType::class, $chantierPoste, [
            'lock_chantier' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $targetChantierId = $chantierPoste->getChantier()?->getId();
            if ($targetChantierId !== null) {
                return $this->redirectToRoute('app_chantier_show', [
                    'id' => $targetChantierId,
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_chantier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier_poste/edit.html.twig', [
            'chantier_poste' => $chantierPoste,
            'chantier' => $chantierPoste->getChantier(),
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_chantier_poste_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        ChantierPoste $chantierPoste,
        EntityManagerInterface $entityManager
    ): Response {
        $targetChantierId = $chantierPoste->getChantier()?->getId();

        if ($this->isCsrfTokenValid('delete'.$chantierPoste->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chantierPoste);
            $entityManager->flush();
        }

        if ($targetChantierId !== null) {
            return $this->redirectToRoute('app_chantier_show', [
                'id' => $targetChantierId,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_chantier_index', [], Response::HTTP_SEE_OTHER);
    }
}