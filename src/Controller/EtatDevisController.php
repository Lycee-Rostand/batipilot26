<?php

namespace App\Controller;

use App\Entity\EtatDevis;
use App\Form\EtatDevisType;
use App\Repository\EtatDevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etat/devis')]
final class EtatDevisController extends AbstractController
{
    #[Route(name: 'app_etat_devis_index', methods: ['GET'])]
    public function index(EtatDevisRepository $etatDevisRepository): Response
    {
        return $this->render('etat_devis/index.html.twig', [
            'etat_devis' => $etatDevisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etat_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etatDevi = new EtatDevis();
        $form = $this->createForm(EtatDevisType::class, $etatDevi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etatDevi);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etat_devis/new.html.twig', [
            'etat_devi' => $etatDevi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etat_devis_show', methods: ['GET'])]
    public function show(EtatDevis $etatDevi): Response
    {
        return $this->render('etat_devis/show.html.twig', [
            'etat_devi' => $etatDevi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etat_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EtatDevis $etatDevi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtatDevisType::class, $etatDevi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etat_devis/edit.html.twig', [
            'etat_devi' => $etatDevi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etat_devis_delete', methods: ['POST'])]
    public function delete(Request $request, EtatDevis $etatDevi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etatDevi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etatDevi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etat_devis_index', [], Response::HTTP_SEE_OTHER);
    }
}
