/* ============================================================================
   BATIPILOT - route « chantiers_archives » : chantiers archivés
   ============================================================================ */
(function () {
  'use strict';

  const { $, esc, dateFR } = BP;
  const C = BP.chantiers;
  const canEdit = !BP.readOnly;

  function render() {
    const { archive } = C.load();
    $('#archives-count').textContent = 'Historique des chantiers archivés · ' + archive.length + ' chantier(s)';
    $('#archives-rows').innerHTML = archive.map(s =>
      '<tr>' +
        '<td class="bp-table__ref" data-label="Chantier">' + esc(s.reference) + '</td>' +
        '<td data-label="Client">' + esc(s.client?.name || '-') + '</td>' +
        '<td data-label="Ville">' + esc(s.house?.city || '-') + '</td>' +
        '<td data-label="Devis">' + C.quoteLink(s.general?.quoteRef) + '</td>' +
        '<td class="bp-table__muted" data-label="Démarrage">' + dateFR(s.general?.start) + '</td>' +
        '<td class="is-end"><div class="bp-actions">' +
          '<a class="bp-link" href="' + BP.url('chantier_suivi') + '?id=' + encodeURIComponent(s.id) + '">Consulter</a>' +
          (canEdit ? '<button type="button" class="bp-link" data-restore="' + esc(s.id) + '">Restaurer →</button>' : '') +
        '</div></td>' +
      '</tr>'
    ).join('') || '<tr><td colspan="6" class="bp-table__empty">Aucun chantier archivé.</td></tr>';
  }

  $('#archives-rows').addEventListener('click', e => {
    const b = e.target.closest('[data-restore]');
    if (b && C.restoreSite(b.dataset.restore)) { render(); BP.toast('Chantier restauré'); }
  });

  render();
})();
