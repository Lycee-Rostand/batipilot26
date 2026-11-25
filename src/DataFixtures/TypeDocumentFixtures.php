<?php

namespace App\DataFixtures;

use App\Entity\TypeDocument;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** Types de documents (PROPOSITION, à valider). Attention : nom limité à 20 caractères. */
class TypeDocumentFixtures extends Fixture
{
    /** clé de référence => nom */
    private const VALEURS = [
        'carte_identite'     => "Carte d'identité",
        'permis_conduire'    => 'Permis de conduire',
        'attestation_assur'  => "Attestation d'assurance",
        'justif_domicile'    => 'Justificatif de domicile',
        'avis_imposition'    => "Avis d'imposition",
        'titre_propriete'    => 'Titre de propriété',
        'rib'                => 'RIB',
        'autre'              => 'Autre',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::VALEURS as $cle => $nom) {
            $objet = (new TypeDocument())
                ->setNom($nom)
                ->setArchive(0);

            $manager->persist($objet);
            $this->addReference('type_document_' . $cle, $objet);
        }

        $manager->flush();
    }
}
