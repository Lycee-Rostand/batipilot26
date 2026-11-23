/* ============================================================================
   BATIPILOT - route « accueil » : tableau de bord de gestion
   - Chantiers : vraies données du module Chantiers (BP.chantiers).
   - Devis brouillons / en attente : vraies données du module Devis (BP.devis).
   - Projets, commandes, livraisons, factures, ventes : pas encore de données
     (affiché « - » / « Aucune donnée ») jusqu'au branchement sur la base.
   ============================================================================ */
(function () {
  'use strict';

  const { $, esc, dateFR, badge } = BP;
  const C = BP.chantiers;

  const year = new Date().getFullYear();

  /* --- Chantiers (vraies données) ----------------------------------------- */
  const { sites, archive } = C.load();
  const by = st => sites.filter(s => s.general?.status === st);
  const current = by('current');
  const late = sites.filter(C.isLate);
  const devis = BP.devis.load().data;

  $('#kpi-year').textContent = year;
  $('#kpi-current').textContent = current.length;
  $('#kpi-current-meta').innerHTML = (late.length ? badge(late.length + ' en retard', 'danger') + ' · ' : '') + by('upcoming').length + ' à venir';
  $('#kpi-done').textContent = by('done').length + archive.length;
  $('#kpi-done-meta').textContent = 'dont ' + archive.length + ' archivé(s)';

  /* --- À traiter --------------------------------------------------------- */
  const stat = (href, icon, label, value, meta, tone) =>
    '<a class="bp-stat' + (tone ? ' bp-stat--' + tone : '') + '" href="' + href + '">' +
      '<span class="bp-stat__icon"><i class="' + icon + '" aria-hidden="true"></i></span>' +
      '<span class="bp-stat__body"><span class="bp-stat__value">' + value + '</span><span class="bp-stat__label">' + label + '</span>' +
      (meta ? '<span class="bp-stat__meta">' + meta + '</span>' : '') + '</span></a>';
  $('#todo-stats').innerHTML =
    stat(BP.url('chantiers'), 'fa-solid fa-triangle-exclamation', 'Chantiers en retard', late.length, '', late.length ? 'danger' : '') +
    stat(BP.url('devis'), 'fa-regular fa-pen-to-square', 'Devis en brouillon', devis.draft.length, 'en préparation', '') +
    stat(BP.url('devis'), 'fa-regular fa-file-lines', 'Devis en attente client', devis.pending.length, 'envoyés', devis.pending.length ? 'warning' : '') +
    stat(BP.url('projets'), 'fa-regular fa-folder-open', 'Projets en attente', '-', 'de devis', '') +
    stat(BP.url('commandes'), 'fa-solid fa-cart-shopping', 'Commandes en attente', '-', '', '') +
    stat(BP.url('commandes'), 'fa-solid fa-truck', 'Livraisons en retard', '-', '', '') +
    stat(BP.url('factures'), 'fa-solid fa-receipt', 'Factures impayées', '-', '', '');

  /* --- Chantiers en cours ------------------------------------------------- */
  $('#home-sites').innerHTML = current.slice(0, 6).map(s => {
    const p = C.siteProgress(s);
    const id = encodeURIComponent(s.id);
    return '<tr>' +
      '<td class="bp-table__ref" data-label="Chantier">' + esc(s.reference) + '<span class="bp-table__sub">' + esc(s.client?.name || '') + ' - ' + esc(s.house?.city || '') + '</span></td>' +
      '<td class="bp-table__muted" data-label="Démarrage">' + dateFR(s.general?.start) + '</td>' +
      '<td class="bp-table__muted" data-label="Fin prévue">' + dateFR(s.general?.end) + '</td>' +
      '<td data-label="Avancement"><div class="bp-meter"><div class="bp-progress bp-progress--success"><div class="bp-progress__bar" style="width:' + p.percent + '%"></div></div><span>' + p.percent + ' %</span></div></td>' +
      '<td data-label="Alerte">' + (C.isLate(s) ? badge('En retard', 'danger') : '-') + '</td>' +
      '<td class="is-end"><a class="bp-link" href="' + BP.url('chantier_suivi') + '?id=' + id + '">Ouvrir →</a></td>' +
    '</tr>';
  }).join('') || '<tr><td colspan="6" class="bp-table__empty">Aucun chantier en cours.</td></tr>';

  /* --- Chantiers par statut (rien à afficher = message vide) ------------- */
  const counts = [by('upcoming').length, current.length, by('done').length, archive.length];
  if (!counts.some(Boolean) || !window.Chart) { $('#chart-status-empty').hidden = false; $('#chart-status').closest('.bp-chart').hidden = true; return; }
  Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
  Chart.defaults.font.size = 12;
  Chart.defaults.color = '#475569';

  new Chart($('#chart-status'), {
    type: 'bar',
    data: {
      labels: ['À venir', 'En cours', 'Terminés', 'Archivés'],
      datasets: [{ label: 'Chantiers', data: counts, backgroundColor: '#4272d7', borderRadius: 4, borderSkipped: 'start', barPercentage: 0.6 }]
    },
    options: {
      indexAxis: 'y', responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1f2937', padding: 10, cornerRadius: 6, callbacks: { label: ctx => ' ' + ctx.parsed.x + ' chantier(s)' } } },
      scales: {
        x: { beginAtZero: true, border: { display: false }, grid: { color: '#eef0f3' }, ticks: { precision: 0 } },
        y: { grid: { display: false }, border: { display: false } }
      }
    }
  });
})();
