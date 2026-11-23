/* ============================================================================
   BATIPILOT - route « devis_archives » : devis archivés
   ============================================================================ */
(function () {
  'use strict';

  const { $, esc } = BP;
  const { archived } = BP.devis.load();

  $('#archives-rows').innerHTML = archived.map(r =>
    '<tr>' + ['Référence', 'Date', 'Client', 'Ville', 'Version'].map((label, i) =>
      '<td' + (i === 0 ? ' class="bp-table__ref"' : '') + ' data-label="' + label + '">' + esc(r[i]) + '</td>').join('') +
    '<td class="is-end"><span class="bp-badge">Archivé</span></td></tr>'
  ).join('') || '<tr><td colspan="6" class="bp-table__empty">Aucun devis archivé.</td></tr>';
})();
