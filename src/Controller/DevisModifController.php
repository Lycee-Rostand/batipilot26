<?php

namespace App\Controller;

use App\Entity\DevisModif;
use App\Form\DevisModifType;
use App\Repository\DevisModifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/devisModif')]
final class DevisModifController extends AbstractController
{
    #[Route(name: 'app_devis_modif_index', methods: ['GET'])]
    public function index(DevisModifRepository $devisModifRepository): Response
    {
        return $this->render('devis_modif/index.html.twig', [
            'devis_modifs' => $devisModifRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_devis_modif_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devisModif = new DevisModif();
        $form = $this->createForm(DevisModifType::class, $devisModif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devisModif);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_modif_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis_modif/new.html.twig', [
            'devis_modif' => $devisModif,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_modif_show', methods: ['GET'])]
    public function show(DevisModif $devisModif): Response
    {
        return $this->render('devis_modif/show.html.twig', [
            'devis_modif' => $devisModif,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_modif_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DevisModif $devisModif, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisModifType::class, $devisModif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_modif_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis_modif/edit.html.twig', [
            'devis_modif' => $devisModif,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_modif_delete', methods: ['POST'])]
    public function delete(Request $request, DevisModif $devisModif, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devisModif->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($devisModif);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_modif_index', [], Response::HTTP_SEE_OTHER);
    }
}
