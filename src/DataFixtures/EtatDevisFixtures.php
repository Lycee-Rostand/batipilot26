<?php

namespace App\DataFixtures;

use App\Entity\EtatDevis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** Statuts des devis (PROPOSITION, à valider avec l'équipe). */
class EtatDevisFixtures extends Fixture
{
    /** clé de référence => nom */
    private const VALEURS = [
        'brouillon'  => 'Brouillon',
        'en_attente' => 'En attente',
        'signe'      => 'Signé',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::VALEURS as $cle => $nom) {
            $objet = (new EtatDevis())
                ->setNom($nom)
                ->setArchive(0);

            $manager->persist($objet);
            $this->addReference('etat_devis_' . $cle, $objet);
        }

        $manager->flush();
    }
}
