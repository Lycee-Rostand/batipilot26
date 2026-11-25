<?php

namespace App\DataFixtures;

use App\Entity\Tva;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TvaFixtures extends Fixture
{
    /** clé de référence => [nom, taux] */
    private const TVAS = [
        '20'  => ['Taux normal', '20.00'],
        '10'  => ['Taux intermédiaire', '10.00'],
        '5_5' => ['Taux réduit', '5.50'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TVAS as $cle => [$nom, $taux]) {
            $tva = (new Tva())
                ->setNom($nom)
                ->setTaux($taux)
                ->setDataValiditeDebut(new \DateTime('2026-01-01'))
                ->setDataValiditeFin(new \DateTime('2026-12-31'))
                ->setArchive(0);

            $manager->persist($tva);
            $this->addReference('tva_' . $cle, $tva);
        }

        $manager->flush();
    }
}
