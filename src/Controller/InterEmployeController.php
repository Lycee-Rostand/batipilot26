<?php

namespace App\Controller;

use App\Entity\InterEmploye;
use App\Form\InterEmployeType;
use App\Repository\InterEmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inter/employe')]
final class InterEmployeController extends AbstractController
{
    #[Route(name: 'app_inter_employe_index', methods: ['GET'])]
    public function index(InterEmployeRepository $interEmployeRepository): Response
    {
        return $this->render('inter_employe/index.html.twig', [
            'inter_employes' => $interEmployeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inter_employe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $interEmploye = new InterEmploye();
        $form = $this->createForm(InterEmployeType::class, $interEmploye);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($interEmploye);
            $entityManager->flush();

            return $this->redirectToRoute('app_inter_employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inter_employe/new.html.twig', [
            'inter_employe' => $interEmploye,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inter_employe_show', methods: ['GET'])]
    public function show(InterEmploye $interEmploye): Response
    {
        return $this->render('inter_employe/show.html.twig', [
            'inter_employe' => $interEmploye,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inter_employe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InterEmploye $interEmploye, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InterEmployeType::class, $interEmploye);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inter_employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inter_employe/edit.html.twig', [
            'inter_employe' => $interEmploye,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inter_employe_delete', methods: ['POST'])]
    public function delete(Request $request, InterEmploye $interEmploye, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$interEmploye->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($interEmploye);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inter_employe_index', [], Response::HTTP_SEE_OTHER);
    }
}
