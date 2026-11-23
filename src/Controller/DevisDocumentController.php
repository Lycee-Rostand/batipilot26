<?php

namespace App\Controller;

use App\Entity\DevisDocument;
use App\Form\DevisDocumentType;
use App\Repository\DevisDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/devis/document')]
final class DevisDocumentController extends AbstractController
{
    #[Route(name: 'app_devis_document_index', methods: ['GET'])]
    public function index(DevisDocumentRepository $devisDocumentRepository): Response
    {
        return $this->render('devis_document/index.html.twig', [
            'devis_documents' => $devisDocumentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_devis_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devisDocument = new DevisDocument();
        $form = $this->createForm(DevisDocumentType::class, $devisDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devisDocument);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis_document/new.html.twig', [
            'devis_document' => $devisDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_document_show', methods: ['GET'])]
    public function show(DevisDocument $devisDocument): Response
    {
        return $this->render('devis_document/show.html.twig', [
            'devis_document' => $devisDocument,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DevisDocument $devisDocument, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisDocumentType::class, $devisDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis_document/edit.html.twig', [
            'devis_document' => $devisDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_document_delete', methods: ['POST'])]
    public function delete(Request $request, DevisDocument $devisDocument, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devisDocument->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($devisDocument);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
