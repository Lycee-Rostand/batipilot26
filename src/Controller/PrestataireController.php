<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Form\PrestataireType;
use App\Repository\PrestataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prestataire')]
final class PrestataireController extends AbstractController
{
    #[Route(name: 'app_prestataire_index', methods: ['GET'])]
    public function index(PrestataireRepository $prestataireRepository): Response
    {
        return $this->render('prestataire/index.html.twig', [
            'prestataires' => $prestataireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prestataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestataire = new Prestataire();
        $form = $this->createForm(PrestataireType::class, $prestataire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prestataire);
            $entityManager->flush();

            return $this->redirectToRoute('app_prestataire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prestataire/new.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prestataire_show', methods: ['GET'])]
    public function show(Prestataire $prestataire): Response
    {
        return $this->render('prestataire/show.html.twig', [
            'prestataire' => $prestataire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prestataire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prestataire $prestataire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrestataireType::class, $prestataire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prestataire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prestataire/edit.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prestataire_delete', methods: ['POST'])]
    public function delete(Request $request, Prestataire $prestataire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prestataire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prestataire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prestataire_index', [], Response::HTTP_SEE_OTHER);
    }

        #[Route('/{id}/archive', name: 'app_prestataire_archive', methods: ['POST'])]
    public function archive(Request $request, Prestataire $prestataire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('archive'.$prestataire->getId(), $request->request->get('_token'))) {
            $prestataire->setIsArchived(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prestataire_index', [], Response::HTTP_SEE_OTHER);
    }
}