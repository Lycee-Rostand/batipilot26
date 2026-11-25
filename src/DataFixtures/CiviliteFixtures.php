<?php

namespace App\DataFixtures;

use App\Entity\Civilite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CiviliteFixtures extends Fixture
{
    /** clé de référence => [nom court, nom long] */
    private const CIVILITES = [
        'm'   => ['M.', 'Monsieur'],
        'mme' => ['Mme', 'Madame'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CIVILITES as $cle => [$nomCourt, $nomLong]) {
            $civilite = (new Civilite())
                ->setNomCourt($nomCourt)
                ->setNomLong($nomLong);

            $manager->persist($civilite);
            $this->addReference('civilite_' . $cle, $civilite);
        }

        $manager->flush();
    }
}
