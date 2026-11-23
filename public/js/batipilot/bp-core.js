/* ============================================================================
   BATIPILOT - utilitaires communs (chargé sur toutes les pages, après bp-ui.js)
   Expose l'objet global `BP` :
     BP.$(sel), BP.$$(sel)        querySelector / querySelectorAll
     BP.esc(texte)                échappe le HTML (TOUJOURS pour afficher une saisie)
     BP.dateFR('2026-11-09')      → '09/11/2026'
     BP.toast(message, type)      notification : success | info | warning | error
     BP.flash(message, type)      notification affichée sur la PAGE SUIVANTE
     BP.confirm({ title, message, confirmLabel, danger }) → Promise<boolean>
     BP.badge(texte, variante)    HTML d'un badge de statut
     BP.uid(prefixe)              identifiant unique
     BP.url('nom_de_route')       adresse d'une route Symfony (liste dans
                                  templates/partials/_routes_js.html.twig)
     BP.readOnly                  true si la page est en consultation seule
                                  (attribut data-readonly sur <body>, posé par
                                  le gabarit employé)
   Sans JS à écrire :
     <form data-bp-confirm="Message">   confirmation avant l'envoi du formulaire
     <button data-bp-toggle>             ouvre / ferme la .bp-section parente
   ============================================================================ */
(function () {
  'use strict';

  const FLASH_KEY = 'batipilot_flash';

  const $ = (sel, root) => (root || document).querySelector(sel);
  const $$ = (sel, root) => Array.from((root || document).querySelectorAll(sel));
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  function dateFR(value) {
    if (!value) return '-';
    const parts = String(value).split('-');
    return parts.length === 3 ? parts.reverse().join('/') : esc(value);
  }
  const uid = prefix => (prefix || 'id') + '-' + Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
  const url = name => (window.BP_ROUTES && window.BP_ROUTES[name]) || '#';
  const badge = (text, variant) => '<span class="bp-badge bp-badge--' + esc(variant || 'neutral') + '">' + esc(text) + '</span>';

  function toast(message, type) {
    const kind = type || 'success';
    if (window.toast && window.toast[kind]) window.toast[kind](message);
  }
  function flash(message, type) {
    try { sessionStorage.setItem(FLASH_KEY, JSON.stringify({ message, type: type || 'success' })); } catch (e) { /* ignore */ }
  }
  function showFlash() {
    try {
      const raw = sessionStorage.getItem(FLASH_KEY);
      if (!raw) return;
      sessionStorage.removeItem(FLASH_KEY);
      const data = JSON.parse(raw);
      setTimeout(() => toast(data.message, data.type), 150);
    } catch (e) { /* ignore */ }
  }

  /* Fenêtre de confirmation (modale Bootstrap 5) */
  function confirm(options) {
    const o = Object.assign({ title: 'Confirmer', message: 'Voulez-vous continuer ?', confirmLabel: 'Confirmer', cancelLabel: 'Annuler', danger: false }, options);
    if (!window.bootstrap) return Promise.resolve(window.confirm(o.message));
    return new Promise(resolve => {
      const wrap = document.createElement('div');
      wrap.innerHTML =
        '<div class="modal fade" tabindex="-1" aria-labelledby="bp-confirm-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">' +
          '<div class="modal-header"><h2 class="modal-title fs-5" id="bp-confirm-title">' + esc(o.title) + '</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button></div>' +
          '<div class="modal-body">' + esc(o.message) + '</div>' +
          '<div class="modal-footer"><button type="button" class="m-btn m-btn--ghost" data-bs-dismiss="modal">' + esc(o.cancelLabel) + '</button>' +
          '<button type="button" class="m-btn ' + (o.danger ? 'm-btn--danger' : 'm-btn--primary') + '" data-bp-confirm-ok>' + esc(o.confirmLabel) + '</button></div>' +
        '</div></div></div>';
      const el = wrap.firstElementChild;
      document.body.appendChild(el);
      const modal = new window.bootstrap.Modal(el);
      let ok = false;
      el.querySelector('[data-bp-confirm-ok]').addEventListener('click', () => { ok = true; modal.hide(); });
      el.addEventListener('hidden.bs.modal', () => { modal.dispose(); el.remove(); resolve(ok); });
      modal.show();
    });
  }

  /* Formulaire à confirmer avant envoi (ex. suppression d'un CRUD) :
     <form method="post" action="…" data-bp-confirm="Supprimer ce poste ?"> */
  document.addEventListener('submit', e => {
    const form = e.target.closest('form[data-bp-confirm]');
    if (!form || form.dataset.bpConfirmed) return;
    e.preventDefault();
    confirm({ title: 'Confirmer', message: form.dataset.bpConfirm, confirmLabel: form.dataset.bpConfirmLabel || 'Supprimer', danger: true })
      .then(ok => { if (ok) { form.dataset.bpConfirmed = '1'; form.submit(); } });
  });

  /* Section dépliable sans JS de page :
     <section class="bp-section is-open"><button class="bp-section__toggle" data-bp-toggle aria-expanded="true">… */
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-bp-toggle]');
    if (!btn) return;
    const open = btn.closest('.bp-section').classList.toggle('is-open');
    btn.setAttribute('aria-expanded', String(open));
  });

  window.BP = { $, $$, esc, dateFR, uid, url, badge, toast, flash, confirm, readOnly: document.body.hasAttribute('data-readonly') };
  document.addEventListener('DOMContentLoaded', showFlash);
})();
