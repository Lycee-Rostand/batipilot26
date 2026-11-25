<?php

namespace App\DataFixtures;

use App\Entity\Unite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UniteFixtures extends Fixture
{
    /** clé de référence => [libellé, description] */
    private const UNITES = [
        'm2'      => ['m²', 'Mètre carré'],
        'm3'      => ['m³', 'Mètre cube'],
        'ml'      => ['ml', 'Mètre linéaire'],
        'unite'   => ['unité', "À l'unité"],
        'forfait' => ['forfait', 'Prix forfaitaire'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::UNITES as $cle => [$libelle, $description]) {
            $unite = (new Unite())
                ->setLibelle($libelle)
                ->setDescription($description);

            $manager->persist($unite);
            $this->addReference('unite_' . $cle, $unite);
        }

        $manager->flush();
    }
}
