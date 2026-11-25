<?php

namespace App\DataFixtures;

use App\Entity\TypeCharpente;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeCharpenteFixtures extends Fixture
{
    /** clé de référence => nom */
    private const TYPES = [
        'fermette'       => 'Fermettes',
        'traditionnelle' => 'Charpente traditionnelle',
        'metallique'     => 'Charpente métallique (Phénix)',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TYPES as $cle => $nom) {
            $type = (new TypeCharpente())->setNom($nom);

            $manager->persist($type);
            $this->addReference('type_charpente_' . $cle, $type);
        }

        $manager->flush();
    }
}
