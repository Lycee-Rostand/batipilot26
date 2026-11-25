<?php

namespace App\DataFixtures;

use App\Entity\Poste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PosteFixtures extends Fixture
{
    /** clé de référence => [libellé, taux de TVA] (l'ordre du tableau = l'ordre d'affichage) */
    private const POSTES = [
        'structure'           => 'Structure',
        'plancher'            => 'Plancher',
        'couverture'          => 'Couverture',
        'escalier'            => 'Escalier',
        'platrerie_isolation' => 'Plâtrerie - Isolation',
        'platrerie_cloison'   => 'Plâtrerie - Cloison',
        'sols_murs'           => 'Sols et murs',
        'electricite'         => 'Électricité',
        'plomberie'           => 'Plomberie',
    ];

    public function load(ObjectManager $manager): void
    {
        $ordre = 1;
        foreach (self::POSTES as $cle => $libelle) {
            $poste = (new Poste())
                ->setLibelle($libelle)
                ->setOrdre($ordre++);
            // visibilite laissée vide en attendant de savoir quelles valeurs mettre

            $manager->persist($poste);
            $this->addReference('poste_' . $cle, $poste);
        }

        $manager->flush();
    }
}
