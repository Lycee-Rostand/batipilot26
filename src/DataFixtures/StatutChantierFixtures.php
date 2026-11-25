<?php

namespace App\DataFixtures;

use App\Entity\StatutChantier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** Statuts des chantiers (PROPOSITION, à valider avec l'équipe). */
class StatutChantierFixtures extends Fixture
{
    /** clé de référence => nom */
    private const VALEURS = [
        'a_planifier' => 'À planifier',
        'en_cours'    => 'En cours',
        'en_pause'    => 'En pause',
        'termine'     => 'Terminé',
        'archive'     => 'Archivé',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::VALEURS as $cle => $nom) {
            $objet = (new StatutChantier())
                ->setNom($nom)
                ->setArchive(0);

            $manager->persist($objet);
            $this->addReference('statut_chantier_' . $cle, $objet);
        }

        $manager->flush();
    }
}
