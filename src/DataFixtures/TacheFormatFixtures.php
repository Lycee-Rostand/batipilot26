<?php

namespace App\DataFixtures;

use App\Entity\TacheFormat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** Formats de valeur d'une tâche (correspondent aux champs valBoolean, valEntier... de ChantierTache). */
class TacheFormatFixtures extends Fixture
{
    /** clé de référence => nom */
    private const FORMATS = [
        'booleen'    => 'Booléen',
        'entier'     => 'Entier',
        'decimal'    => 'Décimal',
        'texte'      => 'Texte',
        'date'       => 'Date',
        'date_heure' => 'Date et heure',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::FORMATS as $cle => $nom) {
            $format = (new TacheFormat())->setNom($nom);

            $manager->persist($format);
            $this->addReference('tache_format_' . $cle, $format);
        }

        $manager->flush();
    }
}
