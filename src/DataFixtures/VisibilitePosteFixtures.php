<?php

namespace App\DataFixtures;

use App\Entity\Poste;
use App\Entity\Visibilite;
use App\Entity\VisibilitePoste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VisibilitePosteFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * clé de référence du poste (sans "poste_") => [ [clé de référence visibilité (sans "visibilite_"), archive], ... ]
     */
    private const VISIBILITE_PAR_POSTE = [
        'structure' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'plancher' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'couverture' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'escalier' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'platrerie_isolation' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'platrerie_cloison' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'sols_murs' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'electricite' => [
            ['devis', 0],
            ['chantier', 0],
        ],
        'plomberie' => [
            ['devis', 0],
            ['chantier', 0],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::VISIBILITE_PAR_POSTE as $clePoste => $lignes) {
            $poste = $this->getReference('poste_' . $clePoste, Poste::class);

            foreach ($lignes as [$cleVisibilite, $archive]) {
                $visibilite = $this->getReference('visibilite_' . $cleVisibilite, Visibilite::class);

                $visibilitePoste = (new VisibilitePoste())
                    ->setPoste($poste)
                    ->setVisibilite($visibilite)
                    ->setArchive($archive);

                $manager->persist($visibilitePoste);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PosteFixtures::class,
            VisibiliteFixtures::class,
        ];
    }
}
