<?php

namespace App\Controller;

use App\Entity\Chantier;
use App\Entity\ChantierTache;
use App\Entity\Client;
use App\Form\ChantierType;
use App\Repository\StatutChantierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chantier')]
final class ChantierController extends AbstractController
{
    #[Route(name: 'app_chantier_index', methods: ['GET'])]
    public function index(StatutChantierRepository $statutChantierRepository): Response
    {
        return $this->render('chantier/index.html.twig', [
            'statuts' => $statutChantierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chantier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chantier = new Chantier();$form = $this->createForm(ChantierType::class, $chantier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chantier);
            $entityManager->flush();

            return $this->redirectToRoute('app_chantier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier/new.html.twig', [
            'chantier' => $chantier,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_chantier_show', methods: ['GET'])]
    public function show(Chantier $chantier): Response
    {
        return $this->render('chantier/show.html.twig', [
            'chantier' => $chantier,
        ]);
    }

    #[Route('/{id<\d+>}/suivi', name: 'app_chantier_suivi', methods: ['GET', 'POST'])]
    public function suivi(Chantier $chantier, Request $request, EntityManagerInterface $entityManager): Response
    {
        $tachesParPoste = [];

        foreach ($chantier->getChantierTaches() as $chantierTache) {
            $tache = $chantierTache->getTache();
            if ($tache === null) {
                continue;
            }

            $poste = $tache->getPosteId();
            if ($poste !== null && !$poste->isVisible('SUI')) {
                continue;
            }

            $posteKey = $poste !== null ? (string) $poste->getId() : 'sans-poste';

            if (!isset($tachesParPoste[$posteKey])) {
                $tachesParPoste[$posteKey] = [
                    'poste' => $poste,
                    'ordre' => ($poste !== null && $poste->getOrdre() !== null) ? $poste->getOrdre() : PHP_INT_MAX,
                    'items' => [],
                    'doneCount' => 0,
                    'totalCount' => 0,
                ];
            }

            $tachesParPoste[$posteKey]['items'][] = $chantierTache;
            $tachesParPoste[$posteKey]['totalCount']++;

            if ($this->isChantierTacheDone($chantierTache)) {
                $tachesParPoste[$posteKey]['doneCount']++;
            }
        }

        usort($tachesParPoste, static function (array $a, array $b): int {
            return $a['ordre'] <=> $b['ordre'];
        });

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('suivi_chantier_' . $chantier->getId(), (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }

            $submittedValues = $request->request->all('taches');

            foreach ($chantier->getChantierTaches() as $chantierTache) {
                $ctId = (string) $chantierTache->getId();
                if (!array_key_exists($ctId, $submittedValues) || !is_array($submittedValues[$ctId])) {
                    continue;
                }

                $input = $submittedValues[$ctId];
                $formatNom = strtolower(trim((string) ($chantierTache->getTache()?->getTacheFormat()?->getNom() ?? '')));

                if (str_contains($formatNom, 'bool') || str_contains($formatNom, 'oui') || str_contains($formatNom, 'case')) {
                    $boolVal = isset($input['bool']) && in_array($input['bool'], ['1', 'true', 'on', true, 1], true);
                    $chantierTache->setValBoolean($boolVal);
                } elseif (str_contains($formatNom, 'datetime') || str_contains($formatNom, 'heure')) {
                    $raw = trim((string) ($input['datetime'] ?? ''));
                    if ($raw !== '') {
                        $dt = $this->parseDateTime($raw);
                        if ($dt !== null) {
                            $chantierTache->setValDateHeure($dt);
                        }
                    } else {
                        $chantierTache->setValDateHeure(null);
                    }
                } elseif (str_contains($formatNom, 'date')) {
                    $raw = trim((string) ($input['date'] ?? ''));
                    if ($raw !== '') {
                        $dt = \DateTime::createFromFormat('Y-m-d', $raw);
                        if ($dt !== false) {
                            $dt->setTime(0, 0, 0);
                            $chantierTache->setValDate($dt);
                        }
                    } else {
                        $chantierTache->setValDate(null);
                    }
                } elseif (str_contains($formatNom, 'entier') || str_contains($formatNom, 'int')) {
                    $raw = trim((string) ($input['int'] ?? ''));
                    $chantierTache->setValEntier($raw !== '' ? (int) $raw : null);
                } elseif (str_contains($formatNom, 'float') || str_contains($formatNom, 'décimal') || str_contains($formatNom, 'decimal') || str_contains($formatNom, 'montant')) {
                    $raw = str_replace(',', '.', trim((string) ($input['float'] ?? '')));
                    $chantierTache->setValFloat($raw !== '' ? (float) $raw : null);
                } else {
                    $raw = trim((string) ($input['texte'] ?? ''));
                    $chantierTache->setValTexte($raw !== '' ? $raw : null);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le suivi du chantier a bien été enregistré.');

            return $this->redirectToRoute('app_chantier_suivi', ['id' => $chantier->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier/suivi.html.twig', [
            'chantier' => $chantier,
            'tachesParPoste' => $tachesParPoste,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_chantier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chantier $chantier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChantierType::class, $chantier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chantier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chantier/edit.html.twig', [
            'chantier' => $chantier,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_chantier_delete', methods: ['POST'])]
    public function delete(Request $request, Chantier $chantier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chantier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chantier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chantier_index', [], Response::HTTP_SEE_OTHER);
    }

    private function parseDateTime(string $raw): ?\DateTime
    {
        foreach (['Y-m-d\TH:i:s', 'Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            $dt = \DateTime::createFromFormat($format, $raw);
            if ($dt !== false) {
                return $dt;
            }
        }

        try {
            return new \DateTime($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    private function isChantierTacheDone(ChantierTache $ct): bool
    {
        if ($ct->isValBoolean() === true) {
            return true;
        }

        return $ct->getValDate() !== null
            || $ct->getValDateHeure() !== null
            || $ct->getValEntier() !== null
            || $ct->getValFloat() !== null
            || ($ct->getValTexte() !== null && trim($ct->getValTexte()) !== '');
    }
}