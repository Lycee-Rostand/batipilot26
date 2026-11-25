<?php

namespace App\DataFixtures;

use App\Entity\Visibilite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** Visibilite des poste (PROPOSITION, à valider avec l'équipe). */
class VisibiliteFixtures extends Fixture
{
    /** clé de référence => libelle */
    private const VALEURS = [
        'chantier'  => ['c', 'Chantier'],
        'devis' => ['d', 'Devis'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::VALEURS as $cle => [$code, $libelle]) {
            $objet = (new Visibilite())
                ->setCode($code)
                ->setLibelle($libelle);

            $manager->persist($objet);
            $this->addReference('visibilite_' . $cle, $objet);
        }

        $manager->flush();
    }
}
