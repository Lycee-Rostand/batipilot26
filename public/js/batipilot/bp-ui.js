/* ============================================================================
   BATIPILOT - comportements communs de l'interface (chargé sur toutes les pages)
   - Menu de gauche : ouvrir / fermer (bouton .js-sidebar-toggle)
   - Barre du haut  : menu du compte (.js-item-menu)
   - Notifications  : window.toast.success / info / warning / error(message)
   Aucune dépendance (pas de jQuery nécessaire).
   ============================================================================ */
(function () {
  'use strict';

  const body = document.body;
  const MOBILE_BP = 992;
  const isMobile = () => window.innerWidth < MOBILE_BP;

  /* --- Menu de gauche ------------------------------------------------------ */
  const toggles = document.querySelectorAll('.js-sidebar-toggle');
  let backdrop = null;
  const setExpanded = open => toggles.forEach(t => t.hasAttribute('aria-expanded') && t.setAttribute('aria-expanded', String(open)));
  function openMobile() {
    body.classList.add('sidebar-open');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.className = 'sidebar-backdrop';
      backdrop.addEventListener('click', closeMobile);
      body.appendChild(backdrop);
    }
    setExpanded(true);
  }
  function closeMobile() { body.classList.remove('sidebar-open'); setExpanded(false); }

  toggles.forEach(btn => btn.addEventListener('click', e => {
    e.preventDefault();
    if (isMobile()) body.classList.contains('sidebar-open') ? closeMobile() : openMobile();
    else {
      const collapsed = body.classList.toggle('sidebar-collapsed');
      try { localStorage.setItem('bp_sidebar', collapsed ? 'collapsed' : 'open'); } catch (err) { /* ignore */ }
    }
  }));

  /* Écran de bienvenue juste après la connexion (affiché une seule fois) */
  const welcome = document.getElementById('bp-welcome');
  if (welcome && body.classList.contains('bp-welcome-on')) {
    try { sessionStorage.removeItem('bp_welcome'); } catch (err) { /* ignore */ }
    let closing = false;
    const close = () => {
      if (closing) return;
      closing = true;
      welcome.classList.add('is-leaving');
      body.classList.add('bp-welcome-reveal');
      setTimeout(() => body.classList.remove('bp-welcome-on'), 1200);
    };
    welcome.addEventListener('click', close);
    setTimeout(close, 1600);
  }

  /* Le menu garde sa position de défilement d'une page à l'autre */
  const navScroll = document.querySelector('.menu-sidebar__content');
  if (navScroll) {
    const saveScroll = () => { try { sessionStorage.setItem('bp_sidebar_scroll', String(navScroll.scrollTop)); } catch (err) { /* ignore */ } };
    navScroll.addEventListener('scroll', saveScroll, { passive: true });
    window.addEventListener('pagehide', saveScroll);
  }
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMobile(); });
  document.querySelectorAll('.menu-sidebar a').forEach(a => a.addEventListener('click', () => { if (isMobile()) closeMobile(); }));
  window.addEventListener('resize', () => { if (!isMobile()) closeMobile(); });

  /* --- Menus déroulants de la barre du haut -------------------------------- */
  const menus = document.querySelectorAll('.js-item-menu');
  menus.forEach(item => item.addEventListener('click', e => {
    e.stopPropagation();
    const open = item.classList.contains('show-dropdown');
    menus.forEach(m => m.classList.remove('show-dropdown'));
    if (!open) item.classList.add('show-dropdown');
  }));
  document.querySelectorAll('.js-dropdown').forEach(d => d.addEventListener('click', e => e.stopPropagation()));
  document.addEventListener('click', () => menus.forEach(m => m.classList.remove('show-dropdown')));

  /* --- Notifications (toasts) --------------------------------------------- */
  const ICONS = { success: 'fa-circle-check', info: 'fa-circle-info', warning: 'fa-triangle-exclamation', error: 'fa-circle-xmark' };
  let container = null;
  function show({ message = '', title = '', type = 'info', duration = 4000 } = {}) {
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      container.setAttribute('aria-live', 'polite');
      body.appendChild(container);
    }
    const t = type in ICONS ? type : 'info';
    const el = document.createElement('div');
    el.className = 'toast toast--' + t;
    el.setAttribute('role', t === 'error' ? 'alert' : 'status');
    el.innerHTML = '<span class="toast__icon"><i class="fa-solid ' + ICONS[t] + '" aria-hidden="true"></i></span>' +
      '<div class="toast__body">' + (title ? '<p class="toast__title"></p>' : '') + '<p class="toast__message"></p></div>' +
      '<button type="button" class="toast__close" aria-label="Fermer"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>';
    if (title) el.querySelector('.toast__title').textContent = title;
    el.querySelector('.toast__message').textContent = message;
    container.appendChild(el);
    requestAnimationFrame(() => el.classList.add('is-visible'));
    const dismiss = () => { el.classList.remove('is-visible'); el.classList.add('is-leaving'); setTimeout(() => el.remove(), 220); };
    el.querySelector('.toast__close').addEventListener('click', dismiss);
    if (duration > 0) setTimeout(dismiss, duration);
  }
  window.toast = {
    show,
    success: (message, title) => show({ message, title, type: 'success' }),
    info: (message, title) => show({ message, title, type: 'info' }),
    warning: (message, title) => show({ message, title, type: 'warning' }),
    error: (message, title) => show({ message, title, type: 'error' })
  };
})();
