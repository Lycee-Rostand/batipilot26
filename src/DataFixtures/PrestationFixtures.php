<?php

namespace App\DataFixtures;

use App\Entity\Poste;
use App\Entity\Prestation;
use App\Entity\Tva;
use App\Entity\Unite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Catalogue des prestations (tableur chiffrages_ouvrages_2026), tarifs valables du 01/01/2026 au 31/12/2026.
 * Montants en HT (prix TTC du tableur / (1 + TVA)), null quand le tableur n'a pas de prix exploitable.
 */
class PrestationFixtures extends Fixture implements DependentFixtureInterface
{
    /** clé du poste => TVA appliquée à ses prestations */
    private const TVA_PAR_POSTE = [
        'structure' => 'tva_10',
        'plancher' => 'tva_10',
        'couverture' => 'tva_10',
        'escalier' => 'tva_10',
        'platrerie_isolation' => 'tva_5_5',
        'platrerie_cloison' => 'tva_10',
        'sols_murs' => 'tva_10',
        'electricite' => 'tva_10',
        'plomberie' => 'tva_10',
    ];

    /** clé du poste => [code, description, montant HT, clé de l'unité] */
    private const PRESTATIONS = [
        'structure' => [
            ['CH1', 'Tous les bois de charpente utilisés sont des bois du nord secs, traités insecticide, anti-termite, fongicide', null, null],
            ['CH2', 'Transformation de la fermette existante en entrait-porteur comprenant :', null, null],
            ['CH3', 'La fourniture et pose de poteaux métalliques encastrés dans les cloisons comprenant : 1 piètement support anti-poinçonnement, un poteau métallique 40x80, 1 platine support recevant les poutres', null, null],
            ['CH4', 'La création de poutres section 150x225 reposant sur les murs porteurs et sur les poteaux métalliques encastrés dans les cloisons au rez-de-chaussée', null, null],
            ['CH4 BIS', 'La création de poutres section 150x225 et/ou de poutres lamellés-collés reposant sur les murs porteurs et sur les poteaux métalliques encastrés dans les cloisons au rez-de-chaussée', null, null],
            ['CH5', 'La création de poutres section 150x225 reposant sur les murs porteurs et sur les poteaux métalliques encastrés dans les cloisons au 1er étage', null, null],
            ['CH6', 'La création de poutres transversales section 150x225 et/ou de poutres lamellés-collés reposant sur les murs porteurs ou sur les poutres porteuses', null, null],
            ['CH7', 'Le plancher en dalles OSB 3 hydrofuges de 18mm d\'épaisseur sera embrevé et vissé sur un solivage de section 32x175 qui repose sur les poutres transversales', null, null],
            ['CH8', 'Le plancher en dalles OSB 3 hydrofuges de 18mm d\'épaisseur sera surbaissé, embrevé et vissé sur un solivage de section 38x225, fixé dans des étriers métalliques qui reposent sur les poutres transversales', null, null],
            ['CH9', 'Chaque fermette sera renforcée par 2 potelets, 2 arbalétriers et 1 faux entrait', null, null],
            ['CH9bis PHENIX', 'Chaque fermette métallique sera renforcée par 2 potelets, 2 arbalétriers et 1 double faux entrait', null, null],
            ['CH10 PHENIX', 'Création de x fermes supplémentaires constituées de 2 potelets, 2 arbalétriers et 1 double faux entrait', null, null],
            ['CH11', 'Le plancher sera autoporteur et désolidarisé des fermettes existantes', null, null],
            ['CH12', 'Le plancher sera autoporteur et désolidarisé des fermettes existantes', null, null],
            ['CH13', 'Ouverture et création d\'une trémie d\'escalier compris renforts et chevêtre', null, null],
            ['CH14', 'Ouverture et création d\'une trémie d\'escalier y compris création d\'une mezzanine avec renforts et chevêtre', null, null],
            ['CH15', 'Déplacement du groupe de ventilation mécanique y compris modification des gaines si nécessaire', null, null],
            ['CH16', 'Dévoiement ou modification du câblage électrique si nécessaire à l\'exécution des travaux', null, null],
            ['CH17', 'Mise en sac d’une partie de la laine de roche/verre soufflée existante pour pouvoir effectuer les travaux et repose entre les solives avant pose du plancher', null, null],
            ['', 'charpente < 10m2', '409.09', 'm2'],
            ['', 'charpente 10-20m2', '386.36', 'm2'],
            ['', 'charpente 21-30m2', '363.64', 'm2'],
            ['', 'charpente 31-35m2', '340.91', 'm2'],
            ['', 'charpente 36-40m2', '318.18', 'm2'],
            ['', 'charpente 41-50m2', '295.45', 'm2'],
            ['', 'charpente 51-60m2', '272.73', 'm2'],
            ['', 'charpente 61-70m2', '250.00', 'm2'],
            ['', 'charpente >70m2', '227.27', 'm2'],
            ['', 'poutre lamibois pour chevêtre VELUX (2 poutres)', '90.91', null],
            ['', 'plus value PHENIX / m2', '27.27', 'm2'],
            ['', 'Dévoiement ou modification du câblage électrique si nécessaire à l\'exécution des travaux', null, null],
            ['', 'mise en sac de la laine de roche/verre soufflée existante et repartition entre solives et derrière des rederessement', '727.27', null],
        ],
        'plancher' => [
            ['PL1', 'Fourniture et pose de lambourdes 38x55 traitées, calées et vissées sur le solivage existant', '82.73', null],
            ['PL2', 'Fourniture et pose de lambourdes 38x55 traitées, calées et scellées sur la terre battue', '121.82', 'm2'],
            ['PL3', 'Création d\'un plancher porteur constitué de :', null, null],
            ['PL4', 'Solivage porteur section 32x175, espacement 0,30m reposant sur les fermes ou sur les murs', '121.82', null],
            ['PL5', 'Solivage porteur section 38x225, espacement 0,30m reposant sur les fermes ou sur les murs', '121.82', null],
            ['PL5BIS', 'Solivage porteur section 63x175, espacement 0,30m reposant sur les fermes ou sur les murs', null, null],
            ['PL6', 'Fourniture et pose d\'un tramiband (performance acoustique) de 3mm déroulé sur chaque lambourde', '8.18', 'm2'],
            ['PL7', 'Fourniture et pose d\'un tramiband (performance acoustique) de 3mm déroulé sur chaque solive', '8.18', 'm2'],
            ['PL8', 'Fourniture et pose d\'un plancher en dalles OSB3, ép. 18mm embrevé et vissé sur les lambourdes', null, null],
            ['PL9', 'Fourniture et pose d\'un plancher en dalles OSB3, ép.18mm embrevé et vissé sur le solivage porteur créé', null, null],
            ['PL10', 'Fourniture et pose d\'un plancher en dalles OSB3, ép.18mm embrevé et vissé sur le solivage existant', null, null],
            ['PL11', 'Ouverture d\'une trémie d\'escalier compris renforts et chevêtre', '1136.36', null],
            ['PL12', 'Ouverture d\'une mezzanine y compris renforts et chevêtre', null, null],
            ['PL13', 'Fourniture et pose de lambourdes 38x50 traitées, calées et fixées sur la dalle béton existante', '94.55', 'm2'],
            ['PL14', 'Ouverture d\'une trémie d\'escalier dans la dalle béton et évacuation des gravats', null, 'unite'],
            ['PL15', 'Modification des fermes existantes comprenant : reprise de faux entraits et renfort par jambes de force', '90.91', 'unite'],
            ['PL16', 'Fourniture et pose de poteau métallique encastré dans les cloisons du RDC, composé d\'un piétement support anti-poinçonnement, d\'un poteau métallique 40x80, d\'une platine support de poutre porteuse. Axé sous les fermes, y compris rebouchage et enduit de finition prêt à peindre', '90.91', 'unite'],
            ['PL17', 'Dévoiement ou modification du câblage électrique si nécessaire à l\'exécution des travaux', null, 'forfait'],
            ['PL18', 'Bois de charpente traité en fourniture et pose', '90.91', 'm3'],
            ['PL19', 'Fourniture et pose d\'un plancher en dalles OSB3, ép. 18mm, embrevé et vissé', '90.91', 'm2'],
        ],
        'couverture' => [
            ['COUV1', 'Modification de pannes intermediaires pour la création de chevêtre', '227.27', null],
            ['COUV2', 'Création de chevêtre pour pose de velux', '45.45', null],
            ['VEL1', 'Fourniture et pose d\'1 velux 55x78 GGL Tout Confort rotation Whitefinish', '909.09', null],
            ['VEL2', 'Fourniture et pose d\'1 velux 55x78 GGL Confort rotation Whitefinish', '772.73', null],
            ['VEL3', 'Fourniture et pose d\'1 velux 55x78 GGU Confort rotation Everfinish', '909.09', null],
            ['VEL4', 'Fourniture et pose d\'1 velux 55x98 GGL Tout Confort rotation Whitefinish', '909.09', null],
            ['VEL5', 'Fourniture et pose d\'1 velux 55x98 GGL Confort rotation Whitefinish', '772.73', null],
            ['VEL6', 'Fourniture et pose d\'1 velux 55x98 GGU Confort rotation Everfinish', '909.09', null],
            ['VEL7', 'Fourniture et pose d\'1 velux 78x98 GGL Tout Confort rotation Whitefinish', '909.09', null],
            ['VEL8', 'Fourniture et pose d\'1 velux 78x98 GGL Confort rotation Whitefinish', '909.09', null],
            ['VEL9', 'Fourniture et pose d\'1 velux 78x98 GPL Confort projection Whitefinish', '909.09', null],
            ['VEL10', 'Fourniture et pose d\'1 velux 78x98 GPU Confort projection Everfinish', '1363.64', null],
            ['VEL11', 'Fourniture et pose d\'1 velux 78x98 GGU Confort rotation Everfinish', '909.09', null],
            ['VEL12', 'Fourniture et pose d\'1 velux 78x118 GGL Tout Confort rotation Whitefinish', '909.09', null],
            ['VEL13', 'Fourniture et pose d\'1 velux 78x118 GGL Confort rotation Whitefinish', '909.09', null],
            ['VEL14', 'Fourniture et pose d\'1 velux 78x118 GPU Confort projection Everfinish', '1363.64', null],
            ['VEL15', 'Fourniture et pose d\'1 velux 78x118 GGU Confort rotation Everfinish', '909.09', null],
            ['VEL16', 'Fourniture et pose d\'1 velux 114x118 GGL Tout Confort rotation Whitefinish', '1363.64', null],
            ['VEL17', 'Fourniture et pose d\'1 velux 114x118 GGL Confort rotation Whitefinish', '909.09', null],
            ['VEL18', 'Fourniture et pose d\'1 velux 114x118 GPL Confort projection Whitefinish', '1363.64', null],
            ['VEL19', 'Fourniture et pose d\'1 velux 114x118 GPL Tout Confort projection Whitefinish', '1363.64', null],
            ['VEL20', 'Fourniture et pose d\'1 velux 114x118 GGU Confort rotation Everfinish', '1363.64', null],
            ['VEL21', 'Fourniture et pose d\'1 velux 134x98 GGL Tout Confort rotation Whitefinish', '1363.64', null],
            ['VEL22', 'Fourniture et pose d\'1 velux 134x98 GGL confort rotation Whitefinish', '1363.64', null],
            ['VEL23', 'Fourniture et pose d\'1 velux 134x98 GPL Confort projection Whitefinish', '1363.64', null],
            ['VEL24', 'Fourniture et pose d\'1 velux 134x98 GGU Confort rotation Everfinish', '1363.64', null],
            ['VEL25', 'Fourniture et pose d\'un store d\'occultation totale DKL 55x78', '90.91', null],
            ['VEL26', 'Fourniture et pose d\'un store d\'occultation totale DKL 55x98', '90.91', null],
            ['VEL27', 'Fourniture et pose d\'un store d\'occultation totale DKL 78x98', '90.91', null],
            ['VEL28', 'Fourniture et pose d\'un store d\'occultation totale DKL 78x118', '90.91', null],
            ['VEL29', 'Fourniture et pose d\'un store d\'occultation totale DKL 114x118', '90.91', null],
            ['VEL30', 'Fourniture et pose d\'un store d\'occultation totale DKL 134x98', '171.82', null],
            ['VEL31', 'Fourniture et pose d\'un volet roulant électrique motorisé SML 55x78 y compris boitier d\'alimentation KUX 110', '873.64', null],
            ['VEL32', 'Fourniture et pose d\'un volet roulant électrique motorisé SML 55x98 y compris boitier d\'alimentation KUX 110', '901.82', null],
            ['VEL33', 'Fourniture et pose d\'un volet roulant électrique motorisé SML 78x98 y compris boitier d\'alimentation KUX 110', '935.45', null],
            ['VEL34', 'Fourniture et pose d\'un volet roulant électrique motorisé SML 78x118 y compris boitier d\'alimentation KUX 110', '961.82', null],
            ['VEL35', 'Fourniture et pose d\'un volet roulant électrique motorisé SML 114x118 y compris boitier d\'alimentation KUX 110', '1050.00', null],
            ['VEL36', 'Fourniture et pose d\'un volet roulant électrique motorisé SML 134x98 y compris boitier d\'alimentation KUX 110', '1060.91', null],
            ['VR', 'pose VR VELUX', '227.27', null],
        ],
        'escalier' => [
            ['ESC1', 'Fourniture et pose d\'un escalier droit en bois exotique sur mesure avec contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '2727.27', null],
            ['ESC2', 'Fourniture et pose d\'un escalier droit en bois exotique sur mesure sans contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '2272.73', null],
            ['ESC3', 'Fourniture et pose d\'un escalier 1/4 tournant en bois exotique sur mesure encloisonné avec contremarches et main courante sur écuyer', '3181.82', null],
            ['ESC4', 'Fourniture et pose d\'un escalier 1/4 tournant en bois exotique sur mesure avec contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '3181.82', null],
            ['ESC5', 'Fourniture et pose d\'un escalier 1/4 tournant en bois exotique sur mesure sans contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '3181.82', null],
            ['ESC6', 'Fourniture et pose d\'un escalier en U en bois exotique sur mesure encloisonné avec contremarches et main courante sur écuyer', '3636.36', null],
            ['ESC7', 'Fourniture et pose d\'un escalier en U en bois exotique sur mesure encloisonné sans contremarches et main courante sur écuyer', '3636.36', null],
            ['ESC8', 'Fourniture et pose d\'un escalier en U en bois exotique sur mesure encloisonné avec contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '4090.91', null],
            ['ESC9', 'Fourniture et pose d\'un escalier en U en bois exotique sur mesure encloisonné sans contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '3636.36', null],
            ['ESC10', 'Fourniture et pose d\'un escalier en U en bois exotique sur mesure sans contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '3636.36', null],
            ['ESC11', 'Fourniture et pose d\'un escalier en U en bois exotique sur mesure avec contremarches y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées', '3636.36', null],
            ['ESC12', 'Caractéristiques : marches ép. 35mm, limon sur mur ép. 30mm, limon sur façade ép. 50mm, contremarches ép. 15mm, poteau 70x70, main courante 75x50 avec arrondis et gorges, balustres 48x30 chanfreinés et grain d\'orge toute hauteur', null, null],
            ['ESC13', 'Fourniture et pose d\'un escalier 1/4 tournant en bois exotique sur mesure sans contremarches y compris garde-corps rampant intérieur à lisses inox et main courante ronde 50mm en bois, rampe non démontable', '3636.36', null],
            ['ESC14', 'Fourniture et pose d\'un escalier 1/4 tournant en bois exotique sur mesure avec contremarches y compris garde-corps rampant intérieur à lisses inox et main courante ronde 50mm en bois, rampe non démontable', '3636.36', null],
            ['ESC15', 'Caractéristiques : marches ép. 35mm, limon sur mur ép. 30mm, limon sur façade ép. 50mm, contremarches ép. 15mm, poteau 70x70, main courante ronde diam. 50mm, lisses inox poli grain 200 diam. 20mm', null, null],
            ['ESC16', 'Fourniture et pose d\'un garde-corps d\'étage en bois exotique à lisses inox horizontales et soubassement en verre STADIP sur pince à verre, main courante ronde 50mm en bois', '363.64', 'ml'],
            ['ESC17', 'Fourniture et pose d\'un garde-corps d\'étage en bois exotique à lisses inox horizontales et soubassement en bois, main courante ronde 50mm en bois', '363.64', null],
            ['ESC18', 'Fourniture et pose d\'un garde-corps d\'étage en bois exotique à balustres verticaux', '227.27', 'ml'],
            ['ESC21', 'Fourniture et pose d\'un escalier escamotable bois 120x60', '636.36', null],
            ['ESC19', 'Fournitures et pose d\'un garde corps d\'étage en bois exotique à balustres verticaux. 2-4m', '227.27', 'ml'],
            ['ESC20', 'Fournitures et pose d\'un garde corps d\'étage en bois exotique à balustres verticaux. 4-6m', '227.27', 'ml'],
        ],
        'platrerie_isolation' => [
            ['ISO1', 'Solivage haut constitué de solivettes 32x175, espacement de 0,90m', '1895.73', 'm3'],
            ['ISO1 bis', 'Solivage haut constitué de solivettes 38x225 espacement de 0,90m', null, null],
            ['ISO2', 'Isolation du plafond et rampants par un TRISO-TOITURE composé d\'un Triso-Hybrid’ et d\'un Boost’R Hybrid’R(m2.K/W) = 6,35', '94.79', 'm2'],
            ['ISO3', 'Isolation du plafond et rampants par un TRISO-SUPER12+ + laine de verre ép. 100mmR(m2.K/W) = 6,90', '94.79', 'm2'],
            ['ISO4', 'Isolation du plafond et rampants par une laine de verre RT PLUS, ép. 240mm avec pare-vapeur étanche à l\'air R(m2.K/W) = 6,85', '94.79', 'm2'],
            ['ISO4 bis', 'Isolation du plafond et rampants par une laine de verre KI FIT 035, ép. 120mm, 2 couches croiséesR(m2.K/W) = 6,80', null, null],
            ['ISO5', 'Isolation des redressements par une laine de verre RT PLUS, ép. 240mm avec pare-vapeur étanche à l\'airR(m2.K/W) = 6,85', '94.79', 'm2'],
            ['ISO6', 'Isolation des rampants par une laine de verre RT PLUS, ép. 240mm avec pare-vapeur étanche à l\'airR(m2.K/W) = 6,85', '94.79', 'm2'],
            ['ISO6 bis', 'Isolation des lucarnes et habillage par une plaque de plâtre de 13mm', null, null],
            ['ISO7', 'Fourniture et pose d\'un adhésif aluminium pour assembler la laine de verre', null, null],
            ['IS07 bis', 'Fourniture et pose d\'une membrane pare vapeur et étanche à l\'air', null, null],
            ['ISO8', 'Isolation des pignons par un doublage 1/2 STIL comprenant 120mm de laine de verre RT PLUS avec pare-vapeur étanche à l\'air et d\'une plaque de plâtre de 13mmR(m2.K/W) = 3,75', '94.79', 'm2'],
            ['ISO9', 'Habillage du plafond et des rampants par une plaque de plâtre de 13mm vissée sur ossature métallique', null, null],
            ['ISO9 bis', 'Habillage des ouvrages par une plaque de plâtre de 13mm vissée sur ossature métallique', null, null],
            ['ISO10', 'Habillage des rampants par une plaque de plâtre de 13mm vissée sur ossature métallique', null, null],
            ['ISO11', 'Habillage des redressements par une plaque de plâtre de 13mm vissée sur ossature métallique', null, null],
            ['ISO12', 'Isolation coté grenier par une cloison isolante SAA 150 comprenant une laine de verre ép. 120mm + plaque de plâtreR(m2.K/W) = 3,75', '94.79', 'm2'],
            ['ISO13', 'Isolation du plafond dessus de garage par 300mm de laine de verre et habillage plafond STIL plaque de plâtre 13 mm visséeR(m2.K/W) = 7,50', '94.79', 'm2'],
            ['ISO14', 'Sur la totalité des habillages plaque de plâtre, pose de bandes calicots et 2 passes d\'enduit de finition (ponçage non compris)', null, null],
            ['ISO15', 'Isolation par 300mm de laine de verre déroulée derrière les redressementsR(m2.K/W) = 7,50', '23.70', 'm2'],
            ['ISO16', 'Isolation par 200mm de laine de verre déroulée derrière les redressementsR(m2.K/W) = 5,00', '23.70', 'm2'],
            ['ISO17', 'Isolation par 100mm de laine de verre déroulée derrière les redressementsR(m2.K/W) = 2,5', '9.48', 'm2'],
            ['ISO18', 'Isolation par soufflage mécanique de 335mm de laine de rocheR(m2.K/W) = 7,50', '47.39', 'm2'],
        ],
        'platrerie_cloison' => [
            ['CLO1', 'Les cloisons de distributions intérieures sont réalisées en ossature PLACOSTIL 72/48 avec laine de verre acoustique 45mm et parement plaque de plâtre 13mm', '90.91', 'm2'],
            ['CLO2', 'Habillage de conduit de cheminée en cloison 1/2 STIL avec laine de roche et plaque de plâtre 13mm', '45.45', null],
            ['CLO3', 'Habillage des murs intérieurs par une plaque de plâtre 13mm collée au mortier adhésif', '45.45', 'm2'],
            ['CLO4', 'Habillage des murs intérieurs par une plaque de plâtre 13mm visée sur ossature métallique', '45.45', 'm2'],
            ['CLO5', 'Habillage des lucarnes par une plaque de plâtre 13mm y compris isolation', '454.55', 'unite'],
            ['CLO6', 'Habillage des pieds de ferme par une plaque de plâtre 13mm fixée sur ossature métallique', '181.82', null],
            ['CLO7', 'Dans la salle de bain est prévu des plaques de plâtre 13mm hydrofuges autour de la douche/baignoire', null, null],
            ['CLO8', 'Sur la totalité des habillages plaque de plâtre, pose de bandes calicots et 2 passes d\'enduit de finition (ponçage non compris)', null, null],
            ['CLO9', 'Est prévu pour les pièces sèches des bloc-portes isoplanes 2,04x0,73m (largeur modifiable en cours de chantier pour adaptation aux contraintes techniques)', '181.82', null],
            ['CLO10', 'Est prévu pour les pièces humides des bloc-portes isoplanes 2,04x0,63m (largeur modifiable en cours de chantier pour adaptation aux contraintes techniques)', '181.82', 'unite'],
            ['CLO11', 'Est prévu pour les pièces sèches des bloc-portes post formés 2,04x0,73m', '181.82', 'unite'],
            ['CLO12', 'Est prévu pour les pièces humides des bloc-portes post formés 2,04x0,63m', '181.82', 'unite'],
            ['CLO13', 'Fourniture et pose d\'un châssis intégré SCRIGNO y compris habillage, porte prépeinte, poignée de tirage ou serrure pour les pièces humides', '909.09', 'unite'],
            ['CLO14', 'Fourniture et pose d\'un châssis coulissant posé en applique avec sa porte prépeinte', '454.55', 'unite'],
            ['CLO15', 'Fourniture et pose d\'un bloc-porte isolant 2,04x073m pour accès grenier', '454.55', 'unite'],
            ['CLO16', 'Fourniture et pose de champlats sapin au pourtour des bloc-portes', '27.27', null],
            ['CLO17', 'Fourniture et pose de trappe de visite isolante 60x60 cm pour accès au redressement', '136.36', 'unite'],
            ['CLO18', 'Fourniture et pose de trappe de visite isolante 60x60 cm pour accès au plafond', '136.36', 'unite'],
            ['CLO19', 'Ponçage des poutres apparentes', '318.18', 'unite'],
            ['CLO20', 'Démontage du conduit de cheminée y compris évacuation des gravats', '1818.18', 'unite'],
            ['CLO21', 'Aménagement de zone de rangement et fourniture et pose de placards coulissants type STRASS de la marque COULIDOOR, y compris plancher et isolation', '909.09', 'unite'],
            ['CLO22', 'Ouverture d\'un passage de porte en maçonnerie y compris linteau et évacuation des gravats', '818.18', 'unite'],
            ['CLO23', 'Fourniture et pose d\'une plinthe sapin 10x70mm', '9.09', 'ml'],
            ['CLO24', 'Forfait nettoyage chantier', '363.64', 'unite'],
            ['CLO25', 'Forfait benne à gravats', '1363.64', 'unite'],
        ],
        'sols_murs' => [
            ['PEINT 01', 'Sur mur et plafond PLACO : rebouchage, 1 couche d\'impression et 2 couches de finition', '45.45', 'm2'],
            ['PEINT 02', 'Sur mur et plafond EXISTANT : rebouchage, 1 couche d\'impression et 2 couches de finition', '45.45', 'm2'],
            ['PEINT 03', 'Sur boiserie : impression et rebouchage, 2 couches de peinture Glycéro', '45.45', 'm2'],
            ['PEINT 04', 'Sur escalier : égrenage, ponçage et 3 couches de vitrificateur', '818.18', 'unite'],
            ['SOL01', 'Dépose du sol existant', '9.09', 'm2'],
            ['SOL02', 'Fourniture et application d\'un primaire d\'accrochage IBOPRIM sur chape ciment avant ragréage', '45.45', 'm2'],
            ['SOL03', 'Fourniture et application d\'un ragréage de sol autolissant', '45.45', 'm2'],
            ['SOL04', 'Fourniture et pose d\'une natte DITRA Schlüter collée', '45.45', 'm2'],
            ['SOL05', 'Fourniture et pose d\'un isolant polyuréthane TMS 56mm avec bande périphérique R=2,60', '45.45', 'm2'],
            ['SOL06', 'Fourniture et pose de chape liquide ciment', '45.45', 'm2'],
            ['SOL07', 'Fourniture et pose de chape liquide ciment sur isolant TMS 56mm fournis posé', '90.91', 'm2'],
            ['SOL08', 'Fourniture et pose d\'un revêtement de sol vinyle y compris sous couche (achat : 35€/m2)', '90.91', 'm2'],
            ['SOL08 bis', 'Fourniture et pose d\'un revêtement de sol stratifié y compris sous couche (achat : 25€/m2)', '90.91', 'm2'],
            ['SOL08 ter', 'Fourniture et pose de plinthes stratifiées assorties', '18.18', 'ml'],
            ['SOL09', 'Fourniture et pose de plinthes vinyles assorties', '18.18', 'ml'],
            ['SOL10', 'Fourniture et pose de carrelage (max 45x45) + joints compris (achat du carrelage : 30€/m2)', '90.91', 'm2'],
            ['SOL11', 'Fourniture et pose de plinthes carrelage assorties', '18.18', null],
            ['FAI01', 'Fourniture et pose d\'un habillage de baignoire de type WEDI', '181.82', 'unite'],
            ['FAI02', 'Fourniture et pose d\'un retour d\'habillage de baignoire de type WEDI', '90.91', 'unite'],
            ['FAI03', 'Fourniture et pose d\'un habillage de douche de type WEDI', '90.91', 'unite'],
            ['FAI04', 'Fourniture et pose de faïence collée + joints compris, (achat : 30€/m2)', '90.91', 'm2'],
        ],
        'electricite' => [
            ['EL0', 'Fourniture et mise en œuvre suivant les normes en vigueur comprenant :', null, null],
            ['EL0 BIS', 'les appareillages sont de la marque LEGRAND gamme DOOXIE', null, null],
            ['EL01', '1 télérupteur 3 points d\'appel', '181.82', null],
            ['EL02', 'spot unité', '90.91', null],
            ['EL03', 'spot salle de bain unité', '90.91', null],
            ['EL04', '1 va et vient', '90.91', null],
            ['EL05', '1 simple allumage', '90.91', null],
            ['EL06', 'prise de courant 16A', '90.91', null],
            ['EL07', '1 ligne convecteur', '90.91', null],
            ['EL08', '1 ligne ballon d\'eau chaude', '90.91', null],
            ['EL09', '1 tableau de distribution pour l’étage avec disjoncteur 30 Ma', '454.55', null],
            ['EL10', '1 ligne d\'alimentation générale', '454.55', null],
            ['EL11', 'prise de courant 20A', '90.91', null],
            ['EL12', 'prise de courant 32A', '90.91', null],
            ['EL13', 'prise RJ45', '90.91', null],
            ['EL14', 'VMC 3 bouches', '909.09', null],
            ['EL15', 'VMC HYGRO B 3 bouches', '1363.64', null],
            ['EL16', 'bouche sup. VMC HYGRO B.', '90.91', null],
            ['EL17', 'forfait fil combles', '227.27', null],
            ['EL18', 'forfait dessus de garage (juste fils)', '90.91', null],
            ['EL19', 'sèche serviette ACOVA', '454.55', null],
            ['EL20', 'convecteur électrique rayonnant', '454.55', null],
        ],
        'plomberie' => [
            ['PLB0', 'Fourniture et mise en œuvre des travaux de plomberie comprenant :', null, null],
            ['PLB01', 'Alimentation en cuivre ou PER eau chaude et froide reprise sur l\'installation existante avec 2 vannes d\'arrêt', '909.09', 'forfait'],
            ['PLB02', 'Le raccordement pour l\'évacuation des eaux usées en PVC diam 40,60,100 suivant l\'installation reprise sur les canalisations existantes y compris ventilation de chute', '454.55', 'forfait'],
            ['PLB03', 'La distribution des éléments suivants :', null, null],
            ['PLB04', 'Forfait pose d\'appareil', '454.55', null],
            ['PLB05', 'Fourniture et pose de cuivre apparent en sous-sol', '27.27', 'ml'],
            ['PLB06', 'Mise en attente EC/EF et PVC pour une vasque', '90.91', 'forfait'],
            ['PLB07', 'Fourniture et pose d\'une attente d\'un robinet thermostatique', '27.27', 'forfait'],
            ['PLB08', 'Fourniture et pose d\'une cabine de douche LEDA', '909.09', null],
            ['PLB9', 'Fourniture et pose d\'une baignoire acrylique 170x75 blanche, modèle Corvette 3 ou similaire, y compris robinetterie par mitigeur (tablier de baignoire non compris)', null, null],
            ['PLB10', 'Fourniture et pose d\'un receveur de douche 80x80 blanc surélevé, modèle Lotus ou similaire, y compris robinetterie par mitigeur et colonne de douche', '909.09', null],
            ['PLB11', 'Fourniture et pose d\'un receveur de douche 90x90 blanc surélevé, modèle Luna ou similaire, y compris robinetterie par mitigeur et colonne de douche', '909.09', null],
            ['PLB12', 'Fourniture et pose d\'un receveur de douche 80x100 blanc surélevé, modèle Luna ou similaire, y compris robinetterie par mitigeur et colonne de douche', '909.09', null],
            ['PLB13', 'Fourniture et pose d\'un receveur de douche 80x120 blanc surélevé, modèle Luna ou similaire, y compris robinetterie par mitigeur et colonne de douche', '90.91', null],
            ['PLB14', 'Fourniture et pose d\'un bâti support WC suspendu universel', '454.55', null],
            ['PLB15', 'Fourniture et pose d\'un WC suspendu', '454.55', null],
            ['PLB16', 'Fourniture et pose d\'un WC avec réservoir 3/6l, modèle pack WC EOLIA blanc ou similaire', '454.55', null],
            ['PLB17', 'Fourniture et pose d\'un WC Watermatic', '1083.64', null],
            ['PLB18', 'Fourniture et pose d\'un ballon EC horizontal 100l', '909.09', null],
            ['PLB19', 'Fourniture et pose d\'un ballon EC 100l', '909.09', null],
            ['PLB20', 'Fourniture et pose d\'un ballon EC 200l', '909.09', null],
            ['PLB21', 'Fourniture et pose d\'un radiateur acier chauffage central', '454.55', null],
            ['PLB09', 'Fourniture et pose d\'une baignoire acrylique Corvette 3', '909.09', null],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PRESTATIONS as $cleePoste => $lignes) {
            $poste = $this->getReference('poste_' . $cleePoste, Poste::class);
            $tva = $this->getReference(self::TVA_PAR_POSTE[$cleePoste], Tva::class);

            foreach ($lignes as $i => [$code, $description, $montant, $cleeUnite]) {
                $prestation = (new Prestation())
                    ->setOrdre($i + 1)
                    ->setCode($code)
                    ->setDescription($description)
                    ->setMontantUnitaire($montant)
                    ->setDateDebutValidite(new \DateTime('2026-01-01'))
                    ->setDateFinValidite(new \DateTime('2026-12-31'))
                    ->setArchive(false)
                    ->setPoste($poste)
                    ->setTva($tva)
                    ->setUnite($cleeUnite ? $this->getReference('unite_' . $cleeUnite, Unite::class) : null);

                $manager->persist($prestation);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PosteFixtures::class, TvaFixtures::class, UniteFixtures::class];
    }
}
