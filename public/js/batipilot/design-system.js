/* ============================================================================
   BATIPILOT - route « design_system »
   Sous chaque exemple (.bp-ds-demo), affiche automatiquement le code HTML à
   copier : le code affiché est donc toujours identique à l'exemple.
   Pour documenter un nouveau composant : ajouter un bloc .bp-ds-demo.
   ============================================================================ */
(function () {
  'use strict';

  const { $, $$, esc } = BP;

  /* Code HTML de chaque exemple, sans l'indentation du fichier */
  $$('.bp-ds-demo').forEach(demo => {
    const lines = demo.innerHTML.replace(/^\s*\n|\n\s*$/g, '').split('\n');
    const indent = Math.min(...lines.filter(l => l.trim()).map(l => l.match(/^ */)[0].length));
    const code = lines.map(l => l.slice(indent)).join('\n')
      .replace(/ data-ds-(toggle|acc)/g, '')
      .replace(/ onsubmit="return false"/g, '');
    const pre = document.createElement('pre');
    pre.className = 'bp-code';
    pre.innerHTML = esc(code);
    demo.after(pre);
  });

  /* Démonstrations interactives */
  $$('[data-ds-toggle]').forEach(btn => btn.addEventListener('click', () => {
    const section = btn.closest('.bp-section');
    section.classList.toggle('is-open');
    btn.setAttribute('aria-expanded', section.classList.contains('is-open'));
  }));
  $$('[data-ds-acc]').forEach(btn => btn.addEventListener('click', () => {
    const acc = btn.closest('.bp-acc');
    const open = acc.classList.toggle('is-open');
    acc.querySelector('.bp-acc__body').hidden = !open;
    btn.setAttribute('aria-expanded', open);
  }));
  $('#ds-toast').addEventListener('click', () => BP.toast('Client enregistré'));
  $('#ds-toast-error').addEventListener('click', () => BP.toast('Le montant dépasse le montant contractuel', 'error'));
  $('#ds-confirm').addEventListener('click', () => {
    BP.confirm({ title: 'Supprimer ce client ?', message: 'Cette action est définitive.', confirmLabel: 'Supprimer', danger: true })
      .then(ok => BP.toast(ok ? 'Confirmé (démo)' : 'Annulé', 'info'));
  });
})();
