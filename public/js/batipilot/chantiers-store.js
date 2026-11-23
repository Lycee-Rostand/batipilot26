/* ============================================================================
   BATIPILOT - Chantiers : données de la MAQUETTE
   ----------------------------------------------------------------------------
   Reprend la maquette de la professeure (suivi_chantiers_devis_lies.html) :
   statuts, postes, points de contrôle et lien avec les devis.
   Les chantiers sont enregistrés dans le localStorage du navigateur ; dans
   l'application finale (Symfony), ils viendront de la base MariaDB.
   Expose `BP.chantiers`.
   ============================================================================ */
(function () {
  'use strict';

  const STORAGE_KEY = 'batipilot_chantiers_v4';
  // Devis enregistrés par la page Devis (même clé que la maquette de la prof)
  const QUOTES_KEY = 'wizard_chiffrage_v3';
  const PAGE_SIZE = 5;
  const TODAY = new Date().toISOString().slice(0, 10);

  const STATUSES = {
    current:  { label: 'En cours', badge: 'accent',  section: 'Chantiers en cours',  desc: 'Travaux en cours de réalisation' },
    upcoming: { label: 'À venir',  badge: 'info',    section: 'Chantiers à venir',   desc: 'Travaux programmés' },
    done:     { label: 'Terminé',  badge: 'success', section: 'Chantiers terminés',  desc: 'Travaux achevés' }
  };
  const STATUS_ORDER = ['current', 'upcoming', 'done'];

  /* Types de réponse d'un point de contrôle */
  const TASK_TYPES = { boolean: 'Réponse oui / non', date: 'Date', text: 'Observation', number: 'Nombre' };

  /* Postes et points de contrôle de la maquette : [code, libellé, type] */
  const CATALOG = {
    'Structure': [
      ['CH1', 'Vérifier les bois de charpente (bois du nord secs et traités)', 'boolean'],
      ['CH2', 'Valider la transformation des fermettes en entrait-porteur', 'boolean'],
      ['CH3', 'Réception des plans d’exécution et validation technique', 'date'],
      ['CH4', 'Commande Dispano effectuée', 'boolean'],
      ['CH5', 'Date de commande des bois de charpente', 'date'],
      ['CH6', 'Date de livraison des matériaux de charpente', 'date'],
      ['CH7', 'Confirmer la disponibilité de l’équipe de charpentiers', 'boolean'],
      ['CH8', 'Contrôle des dimensions et niveaux avant intervention', 'boolean'],
      ['CH9', 'Contrôle des renforts et assemblages', 'boolean'],
      ['CH10', 'Date prévisionnelle de pose de la structure', 'date'],
      ['CH11', 'Date de pose effective de la structure', 'date'],
      ['CH12', 'Vérifier les réservations pour réseaux', 'boolean'],
      ['CH13', 'Contrôle de l’état des bois existants', 'text'],
      ['CH14', 'Réception du chantier de charpente', 'boolean'],
      ['CH15', 'Observations sur la structure', 'text'],
      ['CH16', 'Dévoiement ou modification du câblage électrique si nécessaire', 'boolean'],
      ['CH17', 'Dépose, stockage puis repose de l’isolant soufflé existant', 'boolean']
    ],
    'Plancher': [
      ['ISO1', 'Solivage haut constitué de solivettes 32×175 ou 38×225, espacement de 0,90 m', 'boolean'],
      ['ISO2', 'Isolation du plafond et rampants par TRISO-TOITURE R ≥ 6,35', 'boolean'],
      ['ISO3', 'Isolation du plafond et rampants par TRISO-SUPER12+ et laine de verre 100 mm R ≥ 6,90', 'boolean'],
      ['ISO4', 'Isolation du plafond et rampants par laine de verre 240 mm avec pare-vapeur aluminium R ≥ 6,85', 'boolean'],
      ['ISO5', 'Isolation des redressements par laine de verre 240 mm avec pare-vapeur aluminium R ≥ 6,85', 'boolean'],
      ['PLA6', 'Date de livraison des matériaux de plancher', 'date'],
      ['PLA7', 'Vérifier la planéité et les fixations', 'boolean'],
      ['PLA8', 'Observations de réception du plancher', 'text']
    ],
    'Couverture': [
      ['COU1', 'Commande Velux effectuée', 'date'],
      ['COU2', 'Réception / livraison des Velux', 'date'],
      ['COU3', 'Commande des éléments de couverture effectuée', 'boolean'],
      ['COU4', 'Date de livraison de la couverture', 'date'],
      ['COU5', 'Confirmer l’intervention du couvreur', 'date'],
      ['COU6', 'Vérifier les dimensions et emplacements des Velux', 'boolean'],
      ['COU7', 'Contrôle de l’étanchéité des raccords et abergements', 'boolean'],
      ['COU8', 'Contrôle des gouttières et évacuations', 'boolean'],
      ['COU9', 'Réception des travaux de couverture', 'date'],
      ['COU10', 'Réserves ou observations couverture', 'text']
    ],
    'Escalier bois': [
      ['ESC1', 'Escalier droit bois exotique avec contremarches et garde-corps', 'boolean'],
      ['ESC2', 'Escalier droit bois exotique sans contremarches et avec garde-corps', 'boolean'],
      ['ESC3', 'Escalier 1/4 tournant bois exotique encloisonné avec contremarches', 'boolean'],
      ['ESC4', 'Prise de cotes définitives', 'date'],
      ['ESC5', 'Commande de l’escalier validée', 'boolean'],
      ['ESC6', 'Date de livraison de l’escalier', 'date'],
      ['ESC7', 'Date de pose de l’escalier', 'date'],
      ['ESC8', 'Contrôle des fixations et garde-corps', 'boolean']
    ],
    'Plâtrerie - Isolation': [
      ['PI1', 'Commande des isolants et membranes', 'boolean'],
      ['PI2', 'Livraison des isolants', 'date'],
      ['PI3', 'Vérifier les épaisseurs et résistances thermiques', 'boolean'],
      ['PI4', 'Pose du pare-vapeur contrôlée', 'boolean'],
      ['PI5', 'Traitement des jonctions et de l’étanchéité à l’air', 'boolean'],
      ['PI6', 'Date de fin de pose de l’isolation', 'date'],
      ['PI7', 'Observations isolation', 'text']
    ],
    'Plâtrerie - Cloison': [
      ['PC1', 'Implantation des cloisons validée', 'boolean'],
      ['PC2', 'Commande rails, montants et plaques', 'boolean'],
      ['PC3', 'Livraison des matériaux de cloison', 'date'],
      ['PC4', 'Passage des réseaux avant fermeture', 'boolean'],
      ['PC5', 'Pose des plaques terminée', 'date'],
      ['PC6', 'Bandes et joints contrôlés', 'boolean'],
      ['PC7', 'Réserves plâtrerie', 'text']
    ],
    'Sols - Murs': [
      ['SM1', 'Choix des revêtements de sol validé', 'text'],
      ['SM2', 'Commande des revêtements effectuée', 'boolean'],
      ['SM3', 'Livraison des revêtements', 'date'],
      ['SM4', 'Contrôle du support et de la planéité', 'boolean'],
      ['SM5', 'Date de pose des revêtements', 'date'],
      ['SM6', 'Finitions, plinthes et joints contrôlés', 'boolean'],
      ['SM7', 'Réserves sols et murs', 'text']
    ],
    'Électricité': [
      ['EL1', 'Plan des prises, interrupteurs et éclairages validé', 'boolean'],
      ['EL2', 'Commande du matériel électrique', 'boolean'],
      ['EL3', 'Date de passage des gaines et câbles', 'date'],
      ['EL4', 'Coordination avant fermeture des cloisons', 'boolean'],
      ['EL5', 'Essais des circuits réalisés', 'boolean'],
      ['EL6', 'Date de mise en service', 'date'],
      ['EL7', 'Observations électricité', 'text']
    ],
    'Plomberie': [
      ['PL1', 'Plan des arrivées et évacuations validé', 'boolean'],
      ['PL2', 'Commande des équipements sanitaires', 'boolean'],
      ['PL3', 'Livraison des équipements sanitaires', 'date'],
      ['PL4', 'Passage des réseaux avant fermeture des cloisons', 'boolean'],
      ['PL5', 'Essais d’étanchéité effectués', 'boolean'],
      ['PL6', 'Pose des équipements terminée', 'date'],
      ['PL7', 'Réserves plomberie', 'text']
    ]
  };
  const POSTES = Object.keys(CATALOG);
  const STEPS = ['Général'].concat(POSTES, ['Récapitulatif']);

  /* --- Stockage ----------------------------------------------------------- */
  function load() {
    try {
      const raw = JSON.parse(localStorage.getItem(STORAGE_KEY));
      // Les anciens chantiers d'exemple (demo: true) ne sont plus affichés
      const real = list => (Array.isArray(list) ? list : []).filter(s => !s.demo);
      if (raw) return { sites: real(raw.sites), archive: real(raw.archive) };
    } catch (e) { /* données illisibles */ }
    return { sites: [], archive: [] };
  }
  function persist(state) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify({ sites: state.sites, archive: state.archive })); return true; }
    catch (e) { BP.toast('Sauvegarde locale indisponible', 'error'); return false; }
  }
  function findSite(id) {
    const { sites, archive } = load();
    return sites.find(s => s.id === id) || archive.find(s => s.id === id) || null;
  }
  function isArchived(id) { return load().archive.some(s => s.id === id); }
  function newSite() {
    return { id: BP.uid('CH'), reference: 'CH-' + new Date().getFullYear() + '-' + String(Date.now()).slice(-5), client: {}, house: {}, general: { status: 'upcoming' }, tasks: {}, custom: {} };
  }
  function saveSite(site) {
    const state = load();
    const copy = JSON.parse(JSON.stringify(site));
    const i = state.sites.findIndex(s => s.id === site.id);
    if (i >= 0) state.sites[i] = copy; else state.sites.unshift(copy);
    return persist(state);
  }
  /* Archivage réservé aux chantiers terminés */
  function archiveSite(id) {
    const state = load();
    const i = state.sites.findIndex(s => s.id === id);
    if (i < 0 || state.sites[i].general?.status !== 'done') return false;
    state.archive.unshift(state.sites.splice(i, 1)[0]);
    return persist(state);
  }
  function restoreSite(id) {
    const state = load();
    const i = state.archive.findIndex(s => s.id === id);
    if (i < 0) return false;
    state.sites.unshift(state.archive.splice(i, 1)[0]);
    return persist(state);
  }

  /* --- Devis (lus dans le module Devis) ------------------------------------ */
  function availableQuotes() {
    try {
      const d = JSON.parse(localStorage.getItem(QUOTES_KEY) || '{}');
      return Object.entries(d.records || {}).map(([ref, q]) => ({ ref, q }));
    } catch (e) { return []; }
  }
  function applyQuote(site, ref) {
    site.general.quoteRef = ref;
    const found = availableQuotes().find(x => x.ref === ref);
    if (!found) return;
    const p = found.q.client1 || {}, h = found.q.house || {};
    site.client = { name: p.name || '', phone: p.phone || '', email: p.email || '', civility: p.civility || '' };
    site.house = {
      number: h.number || '', street: h.street || '', zip: h.zip || '', city: h.city || '',
      floor: h.groundEstimated || h.floor || '', living: h.livingEstimated || h.living || '', phenix: !!h.phenix
    };
  }
  function quoteLink(ref) {
    if (!ref) return '-';
    if (BP.readOnly) return '<span class="bp-tag">' + BP.esc(ref) + '</span>';   // employé : pas d'accès aux devis
    return '<a class="bp-link" href="' + BP.url('devis_edition') + '?ref=' + encodeURIComponent(ref) + '" title="Ouvrir le devis">' + BP.esc(ref) + '</a>';
  }

  /* --- Points de contrôle ------------------------------------------------- */
  function tasksFor(site, poste) {
    const base = (CATALOG[poste] || []).map(([id, label, type]) => ({ id, label, type, custom: false }));
    const custom = (site.custom?.[poste] || []).map(t => Object.assign({ custom: true }, t));
    return base.concat(custom);
  }
  function progress(site, poste) {
    const all = tasksFor(site, poste);
    const done = all.filter(t => site.tasks?.[t.id]?.done).length;
    return { done, total: all.length, percent: all.length ? Math.round(100 * done / all.length) : 0 };
  }
  function siteProgress(site) {
    let done = 0, total = 0;
    POSTES.forEach(p => { const r = progress(site, p); done += r.done; total += r.total; });
    return { done, total, percent: total ? Math.round(100 * done / total) : 0 };
  }
  /* Retard : chantier en cours dont la fin prévue est dépassée */
  function isLate(site) {
    return site.general?.status === 'current' && !!site.general?.end && site.general.end < TODAY;
  }

  BP.chantiers = {
    PAGE_SIZE, TODAY, STATUSES, STATUS_ORDER, TASK_TYPES, CATALOG, POSTES, STEPS,
    load, persist, findSite, isArchived, newSite, saveSite, archiveSite, restoreSite,
    availableQuotes, applyQuote, quoteLink, tasksFor, progress, siteProgress, isLate
  };
})();
