<?php

namespace App\DataFixtures;

use App\Entity\TarifStructure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Tranches "charpente" de la feuille STRUCTURE chiffrage 2026.
 * Prix au m² en HT (prix TTC du tableur / 1,10).
 */
class TarifStructureFixtures extends Fixture
{
    /** [surface min, surface max, montant HT au m²] */
    private const TRANCHES = [
        ['0.00',  '9.99',    '409.09'],
        ['10.00', '20.99',   '386.36'],
        ['21.00', '30.99',   '363.64'],
        ['31.00', '35.99',   '340.91'],
        ['36.00', '40.99',   '318.18'],
        ['41.00', '50.99',   '295.45'],
        ['51.00', '60.99',   '272.73'],
        ['61.00', '70.99',   '250.00'],
        ['71.00', '9999.99', '227.27'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TRANCHES as $i => [$min, $max, $montant]) {
            $tarif = (new TarifStructure())
                ->setSurfaceMin($min)
                ->setSurfaceMax($max)
                ->setMontantHT($montant);

            $manager->persist($tarif);
            $this->addReference('tarif_structure_' . ($i + 1), $tarif);
        }

        $manager->flush();
    }
}
