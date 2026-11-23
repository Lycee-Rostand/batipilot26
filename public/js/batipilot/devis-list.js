/* ============================================================================
   BATIPILOT - route « devis » : liste des devis (maquette de la professeure)
   Sections Brouillons / En attente client / Signés, 5 lignes par page.
   ============================================================================ */
(function () {
  'use strict';

  const { $, esc, badge } = BP;
  const D = BP.devis;
  const pages = { draft: 1, pending: 1, signed: 1 };
  const closed = new Set();
  let query = '';
  let state = D.load();

  const matches = r => !query || r.join(' ').toLowerCase().includes(query);

  function sectionHTML(s) {
    const rows = state.data[s.key].filter(matches);
    const max = Math.max(1, Math.ceil(rows.length / D.PAGE_SIZE));
    pages[s.key] = Math.max(1, Math.min(pages[s.key], max));
    const page = pages[s.key];
    const open = !closed.has(s.key);
    const body = rows.slice((page - 1) * D.PAGE_SIZE, page * D.PAGE_SIZE).map(r =>
      '<tr>' +
        '<td class="bp-table__ref" data-label="Référence">' + esc(r[0]) + '</td>' +
        '<td class="bp-table__muted" data-label="Date">' + esc(r[1]) + '</td>' +
        '<td data-label="Client">' + esc(r[2]) + '</td>' +
        '<td data-label="Ville">' + esc(r[3]) + '</td>' +
        '<td data-label="Version"><span class="bp-tag">' + esc(r[4]) + '</span></td>' +
        '<td class="is-end"><button type="button" class="bp-link" data-action="' + s.key + '" data-ref="' + esc(r[0]) + '">' + s.action + ' →</button></td>' +
      '</tr>').join('') || '<tr><td colspan="6" class="bp-table__empty">' + (query ? 'Aucun résultat pour cette recherche.' : 'Aucun devis') + '</td></tr>';
    let btns = '<button type="button" class="bp-page-btn" data-page="' + s.key + '" data-to="' + (page - 1) + '"' + (page === 1 ? ' disabled' : '') + ' aria-label="Page précédente">‹</button>';
    for (let i = 1; i <= max; i++) btns += '<button type="button" class="bp-page-btn' + (i === page ? ' is-current' : '') + '" data-page="' + s.key + '" data-to="' + i + '">' + i + '</button>';
    btns += '<button type="button" class="bp-page-btn" data-page="' + s.key + '" data-to="' + (page + 1) + '"' + (page === max ? ' disabled' : '') + ' aria-label="Page suivante">›</button>';
    const info = rows.length ? ((page - 1) * D.PAGE_SIZE + 1) + '–' + Math.min(page * D.PAGE_SIZE, rows.length) + ' sur ' + rows.length : 'Aucun devis';
    return '<section class="bp-section' + (open ? ' is-open' : '') + '">' +
      '<button type="button" class="bp-section__toggle" data-toggle="' + s.key + '" aria-expanded="' + open + '">' +
        '<h2 class="bp-section__title">' + s.title + '</h2>' + badge(String(rows.length), s.badge) +
        '<i class="fa-solid fa-chevron-down bp-section__chevron" aria-hidden="true"></i></button>' +
      '<div class="bp-section__body"><p class="bp-section__desc">' + s.desc + '</p>' +
        '<div class="bp-table-wrap"><table class="bp-table bp-table--cards"><thead><tr><th>Référence</th><th>Date</th><th>Nom et prénom du client</th><th>Ville</th><th>Dernière version</th><th class="is-end">Action</th></tr></thead><tbody>' + body + '</tbody></table></div>' +
        '<div class="bp-pagination"><span>' + info + '</span><div class="bp-pagination__pages">' + btns + '</div></div>' +
      '</div></section>';
  }

  $('#devis-sections').addEventListener('click', e => {
    const t = e.target.closest('[data-toggle]');
    if (t) { const k = t.dataset.toggle; closed.has(k) ? closed.delete(k) : closed.add(k); render(); return; }
    const p = e.target.closest('[data-page]');
    if (p && !p.disabled) { pages[p.dataset.page] = Number(p.dataset.to); render(); return; }
    const a = e.target.closest('[data-action]');
    if (!a) return;
    if (a.dataset.action === 'draft') location.href = BP.url('devis_edition') + '?ref=' + encodeURIComponent(a.dataset.ref);
    else BP.toast('Consultation des versions non disponible dans cette maquette', 'info');
  });
  $('#devis-search').addEventListener('input', e => { query = e.target.value.trim().toLowerCase(); Object.keys(pages).forEach(k => { pages[k] = 1; }); render(); });
})();
