<?php

namespace App\Controller;

use App\Entity\TypeCharpente;
use App\Form\TypeCharpenteType;
use App\Repository\TypeCharpenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type/charpente')]
final class TypeCharpenteController extends AbstractController
{
    #[Route(name: 'app_type_charpente_index', methods: ['GET'])]
    public function index(TypeCharpenteRepository $typeCharpenteRepository): Response
    {
        return $this->render('type_charpente/index.html.twig', [
            'type_charpentes' => $typeCharpenteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_charpente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeCharpente = new TypeCharpente();
        $form = $this->createForm(TypeCharpenteType::class, $typeCharpente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeCharpente);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_charpente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_charpente/new.html.twig', [
            'type_charpente' => $typeCharpente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_charpente_show', methods: ['GET'])]
    public function show(TypeCharpente $typeCharpente): Response
    {
        return $this->render('type_charpente/show.html.twig', [
            'type_charpente' => $typeCharpente,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_charpente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeCharpente $typeCharpente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeCharpenteType::class, $typeCharpente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_charpente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_charpente/edit.html.twig', [
            'type_charpente' => $typeCharpente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_charpente_delete', methods: ['POST'])]
    public function delete(Request $request, TypeCharpente $typeCharpente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeCharpente->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typeCharpente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_charpente_index', [], Response::HTTP_SEE_OTHER);
    }
}
