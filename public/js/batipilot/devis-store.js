/* ============================================================================
   BATIPILOT - Devis : données de la MAQUETTE (maquette de la professeure)
   ----------------------------------------------------------------------------
   Stockage localStorage « wizard_chiffrage_v3 » (même format que la maquette :
   { data: {draft, pending, signed}, records, archived }). Le module Chantiers
   lit les devis enregistrés ici pour remplir « Devis d'origine ».
   Montants indicatifs, hors taxes. Expose `BP.devis`.
   ============================================================================ */
(function () {
  'use strict';

  const STORE = 'wizard_chiffrage_v3';
  const PAGE_SIZE = 5;

  const SECTIONS = [
    { key: 'draft',   title: 'Brouillons',       desc: 'Devis en cours de préparation · version 0', action: 'Reprendre',         badge: 'neutral' },
    { key: 'pending', title: 'En attente client', desc: 'Devis envoyés',                             action: 'Voir les versions', badge: 'info' },
    { key: 'signed',  title: 'Signés',           desc: 'Devis acceptés',                            action: 'Ouvrir',            badge: 'success' }
  ];
  const EMPTY = { draft: [], pending: [], signed: [] };

  const STEPS = ['Client', 'Maison', 'Structure', 'Plancher', 'Couverture', 'Escalier bois', 'Plâtrerie-isolation', 'Plâtrerie-cloison', 'Sols-murs', 'Électricité', 'Plomberie', 'Récapitulatif'];

  /* Structure : CH1 à CH17 (CH3 à CH15 absents du fichier source de la maquette) */
  const CH = [
    ['CH1', 'Tous les bois de charpente utilisés sont des bois du nord secs, traités insecticide, anti-termite, fongicide.'],
    ['CH2', 'Transformation de la fermette existante en entrait-porteur comprenant : adaptation de la structure existante, renforts et assemblages nécessaires à la réalisation des travaux.'],
    ...Array.from({ length: 13 }, (_, i) => ['CH' + (i + 3), 'Intitulé d’origine non présent dans le fichier fourni - à compléter.']),
    ['CH16', 'Dévoiement ou modification du câblage électrique si nécessaire à l’exécution des travaux.'],
    ['CH17', 'Mise en sac d’une partie de la laine de roche/verre soufflée existante pour pouvoir effectuer les travaux et repose entre les solives avant pose du plancher.']
  ];

  /* Prestations par poste : [code, description, unité, prix unitaire indicatif] */
  const CATALOG = {
    plancher: [
      ['ISO1', 'Solivage haut constitué de solivettes 32x175 ou 38x225, espacement de 0,90 m.', 'm³ bois', 2000],
      ['ISO2', 'Isolation du plafond et rampants par un TRISO-TOITURE R(m² K/W) ≥ 6,35', 'm²', 100],
      ['ISO3', 'Isolation du plafond et rampants par un TRISO-SUPER12+ + laine de verre ép. 100 mm 035 R(m² K/W) ≥ 6,90', 'm²', 100],
      ['ISO4', 'Isolation du plafond et rampants par 240 mm de laine de verre avec pare-vapeur aluminium R(m² K/W) ≥ 6,85', 'm²', 100],
      ['ISO5', 'Isolation des redressements par 240 mm de laine de verre avec pare-vapeur aluminium R(m² K/W) ≥ 6,85', 'm²', 100]
    ],
    couverture: [
      ['COUV1', 'Révision ponctuelle de couverture et remplacement de tuiles endommagées', 'm²', 65],
      ['COUV2', 'Pose d’écran sous-toiture HPV', 'm²', 28],
      ['COUV3', 'Reprise des raccords et étanchéités de toiture', 'forfait', 650]
    ],
    escalier: [
      ['ESC1', 'Fourniture et pose d’un escalier droit en bois exotique sur mesure avec contremarches, y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées.', 'forfait', 3000],
      ['ESC2', 'Fourniture et pose d’un escalier droit en bois exotique sur mesure sans contremarches, y compris garde-corps rampant intérieur à balustres droits à chanfrein et poteaux à têtes façonnées.', 'forfait', 2500],
      ['ESC3', 'Fourniture et pose d’un escalier 1/4 tournant en bois exotique sur mesure encloisonné avec contremarches et main courante sur écuyer.', 'forfait', 3500]
    ],
    platrerieIsolation: [
      ['PI1', 'Isolation des rampants en laine minérale avec pare-vapeur et finition plaques de plâtre', 'm²', 85],
      ['PI2', 'Isolation de combles perdus par soufflage', 'm²', 35],
      ['PI3', 'Habillage de pieds-droits et sous-pentes en plaques de plâtre', 'm²', 58]
    ],
    platrerieCloison: [
      ['PC1', 'Cloison distributive en plaques de plâtre sur ossature métallique avec isolant', 'm²', 68],
      ['PC2', 'Doublage de mur sur ossature métallique et plaque de plâtre', 'm²', 58],
      ['PC3', 'Bloc-porte intérieur alvéolaire posé', 'unité', 350]
    ],
    solsMurs: [
      ['SM1', 'Fourniture et pose de sol stratifié avec sous-couche', 'm²', 55],
      ['SM2', 'Fourniture et pose de carrelage intérieur', 'm²', 90],
      ['SM3', 'Préparation et peinture des murs et plafonds (deux couches)', 'm²', 38]
    ],
    electricite: [
      ['ELEC1', 'Création d’un point lumineux avec commande', 'unité', 145],
      ['ELEC2', 'Fourniture et pose d’une prise de courant 16 A', 'unité', 105],
      ['ELEC3', 'Adaptation d’un tableau électrique existant', 'forfait', 650]
    ],
    plomberie: [
      ['PLO1', 'Création d’une alimentation et évacuation pour lavabo', 'unité', 390],
      ['PLO2', 'Fourniture et pose d’un WC complet', 'unité', 580],
      ['PLO3', 'Fourniture et pose d’un receveur de douche et raccordements', 'unité', 950]
    ]
  };
  /* Étape de l'assistant → poste du catalogue */
  const MAPPING = {
    3: ['plancher', 'Plancher / Isolation'], 4: ['couverture', 'Couverture'], 5: ['escalier', 'Escalier bois'],
    6: ['platrerieIsolation', 'Plâtrerie - Isolation'], 7: ['platrerieCloison', 'Plâtrerie - Cloison'],
    8: ['solsMurs', 'Sols - Murs'], 9: ['electricite', 'Électricité'], 10: ['plomberie', 'Plomberie']
  };

  const num = n => Math.max(0, Number(n) || 0);
  const money = n => Number(n || 0).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });

  /* Lignes de liste : seulement les devis réellement enregistrés (les anciens exemples sont ignorés) */
  function onlyRecorded(stored) {
    const data = JSON.parse(JSON.stringify(EMPTY));
    const records = stored.records || {};
    Object.keys(data).forEach(k => { data[k] = (stored.data?.[k] || []).filter(r => records[r[0]]); });
    return data;
  }

  function load() {
    let stored = {};
    try { stored = JSON.parse(localStorage.getItem(STORE) || '{}'); } catch (e) { /* ignore */ }
    return {
      data: onlyRecorded(stored),
      records: stored.records || {},
      archived: stored.archived || []
    };
  }
  function persist(state) {
    try { localStorage.setItem(STORE, JSON.stringify(state)); return true; }
    catch (e) { BP.toast('Sauvegarde locale indisponible', 'error'); return false; }
  }

  /* Ligne de liste correspondant à une référence */
  function findRow(state, ref) {
    for (const s of SECTIONS) {
      const row = state.data[s.key].find(r => r[0] === ref);
      if (row) return { section: s.key, row };
    }
    return null;
  }

  /* --- Calculs (hors taxes) ------------------------------------------------ */
  function structureTotal(q) {
    const s = q.structure || {}, a = num(q.house?.livingEstimated);
    let total = a * (num(s.rate) + (q.house?.phenix ? num(s.phenixRate) : 0));
    for (const c of ['CH16', 'CH17']) if (s.items?.[c]?.on) total += num(s.items[c].amount);
    return total;
  }
  function postTotal(q, key) {
    return CATALOG[key].reduce((sum, [code, , , price]) => {
      const v = q.posts?.[key]?.[code];
      return sum + (v?.on ? num(v.qty) * num(v.rate ?? price) : 0);
    }, 0);
  }
  function grandTotal(q) { return structureTotal(q) + Object.keys(CATALOG).reduce((a, key) => a + postTotal(q, key), 0); }

  BP.devis = { PAGE_SIZE, SECTIONS, STEPS, CH, CATALOG, MAPPING, num, money, load, persist, findRow, structureTotal, postTotal, grandTotal };
})();
