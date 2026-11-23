<?php

namespace App\Controller;

use App\Repository\PrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prestation')]
final class PrestationController extends AbstractController
{
    #[Route('', name: 'app_prestation_index', methods: ['GET'])]
    public function index(PrestationRepository $prestationRepository): Response
    {
        // Regroupe les prestations par poste (0 = prestations sans poste)
        $groupes = [];
        foreach ($prestationRepository->findPourListe() as $prestation) {
            $poste = $prestation->getPoste();
            $cle = $poste ? $poste->getId() : 0;
            $groupes[$cle] ??= ['poste' => $poste, 'prestations' => []];
            $groupes[$cle]['prestations'][] = $prestation;
        }

        return $this->render('prestation/index.html.twig', [
            'groupes' => $groupes,
        ]);
    }
}