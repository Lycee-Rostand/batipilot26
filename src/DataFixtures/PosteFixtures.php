<?php

namespace App\DataFixtures;

use App\Entity\Poste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PosteFixtures extends Fixture
{
    /** clé de référence => [libellé, taux de TVA] (l'ordre du tableau = l'ordre d'affichage) */
    private const POSTES = [
        'structure'           => ['Structure', 10.0],
        'plancher'            => ['Plancher', 10.0],
        'couverture'          => ['Couverture', 10.0],
        'escalier'            => ['Escalier', 10.0],
        'platrerie_isolation' => ['Plâtrerie - Isolation', 5.5],
        'platrerie_cloison'   => ['Plâtrerie - Cloison', 10.0],
        'sols_murs'           => ['Sols et murs', 10.0],
        'electricite'         => ['Électricité', 10.0],
        'plomberie'           => ['Plomberie', 10.0],
    ];

    public function load(ObjectManager $manager): void
    {
        $ordre = 1;
        foreach (self::POSTES as $cle => [$libelle, $tva]) {
            $poste = (new Poste())
                ->setLibelle($libelle)
                ->setTva($tva)
                ->setOrdre($ordre++)
                ->setArchive(false);
            // visibilite laissée vide en attendant de savoir quelles valeurs mettre

            $manager->persist($poste);
            $this->addReference('poste_' . $cle, $poste);
        }

        $manager->flush();
    }
}
