/* ============================================================================
   BATIPILOT - route « chantiers » (liste des chantiers, maquette de la professeure)
   Trois sections repliables (en cours / à venir / terminés), 5 lignes par
   page, recherche commune.
   ============================================================================ */
(function () {
  'use strict';

  const { $, esc, dateFR, badge } = BP;
  const C = BP.chantiers;
  // Gabarit employé : la fiche s'ouvre dans l'espace employé (consultation)
  const SUIVI_URL = BP.readOnly ? BP.url('employe_chantier') : BP.url('chantier_suivi');
  const pages = { current: 1, upcoming: 1, done: 1 };
  const closed = new Set();
  let query = '';

  const matches = s => !query || [s.reference, s.client?.name, s.house?.city, s.general?.quoteRef].join(' ').toLowerCase().includes(query);

  function rowHTML(s) {
    return '<tr>' +
      '<td class="bp-table__ref" data-label="Référence">' + esc(s.reference) + '</td>' +
      '<td data-label="Client">' + esc(s.client?.name || '-') + '</td>' +
      '<td data-label="Ville">' + esc(s.house?.city || '-') + '</td>' +
      '<td data-label="Devis">' + C.quoteLink(s.general?.quoteRef) + '</td>' +
      '<td class="bp-table__muted" data-label="Démarrage">' + dateFR(s.general?.start) + '</td>' +
      '<td class="is-end"><div class="bp-actions"><a class="bp-link" href="' + SUIVI_URL + '?id=' + encodeURIComponent(s.id) + '">Ouvrir</a>' +
        (BP.readOnly ? '' : '<a class="bp-link" href="' + BP.url('rentabilite') + '?id=' + encodeURIComponent(s.id) + '">Rentabilité</a>') + '</div></td>' +
    '</tr>';
  }

  function paginationHTML(key, page, max, count) {
    const size = C.PAGE_SIZE;
    const info = count ? ((page - 1) * size + 1) + '–' + Math.min(page * size, count) + ' sur ' + count + ' chantiers' : 'Aucun chantier';
    let btns = '<button type="button" class="bp-page-btn" data-page="' + key + '" data-to="' + (page - 1) + '"' + (page === 1 ? ' disabled' : '') + '>‹ Précédent</button>';
    for (let i = 1; i <= max; i++) btns += '<button type="button" class="bp-page-btn' + (i === page ? ' is-current' : '') + '" data-page="' + key + '" data-to="' + i + '"' + (i === page ? ' aria-current="page"' : '') + '>' + i + '</button>';
    btns += '<button type="button" class="bp-page-btn" data-page="' + key + '" data-to="' + (page + 1) + '"' + (page === max ? ' disabled' : '') + '>Suivant ›</button>';
    return '<div class="bp-pagination"><span>' + info + '</span><div class="bp-pagination__pages">' + btns + '</div></div>';
  }

  function sectionHTML(key, sites) {
    const meta = C.STATUSES[key];
    const list = sites.filter(s => s.general?.status === key && matches(s));
    const max = Math.max(1, Math.ceil(list.length / C.PAGE_SIZE));
    pages[key] = Math.max(1, Math.min(pages[key], max));
    const page = pages[key];
    const rows = list.slice((page - 1) * C.PAGE_SIZE, page * C.PAGE_SIZE).map(rowHTML).join('') ||
      '<tr><td colspan="6" class="bp-table__empty">' + (query ? 'Aucun résultat pour cette recherche.' : 'Aucun chantier dans cette catégorie.') + '</td></tr>';
    const open = !closed.has(key);
    return '<section class="bp-section' + (open ? ' is-open' : '') + '">' +
      '<button type="button" class="bp-section__toggle" data-toggle="' + key + '" aria-expanded="' + open + '">' +
        '<h2 class="bp-section__title">' + meta.section + '</h2>' + badge(String(list.length), meta.badge) +
        '<i class="fa-solid fa-chevron-down bp-section__chevron" aria-hidden="true"></i></button>' +
      '<div class="bp-section__body"><p class="bp-section__desc">' + meta.desc + '</p>' +
        '<div class="bp-table-wrap"><table class="bp-table bp-table--cards"><thead><tr>' +
          '<th>Référence chantier</th><th>Nom et prénom du client</th><th>Ville</th><th>Référence devis</th><th>Date de démarrage</th><th class="is-end">Action</th>' +
        '</tr></thead><tbody>' + rows + '</tbody></table></div>' +
        paginationHTML(key, page, max, list.length) +
      '</div></section>';
  }

  function render() {
    const { sites } = C.load();
    $('#chantiers-total').textContent = sites.length + ' chantiers · ' + sites.filter(s => s.general?.status === 'current').length + ' en cours';
    $('#chantiers-sections').innerHTML = C.STATUS_ORDER.map(k => sectionHTML(k, sites)).join('');
  }

  $('#chantiers-sections').addEventListener('click', e => {
    const t = e.target.closest('[data-toggle]');
    if (t) { const k = t.dataset.toggle; closed.has(k) ? closed.delete(k) : closed.add(k); render(); return; }
    const p = e.target.closest('[data-page]');
    if (p && !p.disabled) { pages[p.dataset.page] = Number(p.dataset.to); render(); }
  });
  $('#chantiers-search').addEventListener('input', e => {
    query = e.target.value.trim().toLowerCase();
    Object.keys(pages).forEach(k => { pages[k] = 1; });
    render();
  });

  render();
})();
