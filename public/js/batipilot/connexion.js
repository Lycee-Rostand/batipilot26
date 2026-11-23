/* ============================================================================
   BATIPILOT - route « connexion » (1re page de l'application)
   MAQUETTE : vérification simulée. Dans Symfony : formulaire de login du
   composant Security, mots de passe hachés, redirection selon le profil.
   Comptes de démonstration (mot de passe « demo ») :
     admin → route « accueil_admin » · responsable → route « accueil »
     employe → route « accueil_employe » · inactif → compte désactivé
   Le profil est gardé dans le cookie « bp_profil » : layout.html.twig s'en sert
   pour afficher le header et le menu du profil sur toutes les pages.
   ============================================================================ */
(function () {
  'use strict';

  const { $ } = BP;
  const HOMES = { admin: BP.url('accueil_admin'), responsable: BP.url('accueil'), employe: BP.url('accueil_employe') };
  const form = $('#login-form');
  const id = $('#login-id');
  const pwd = $('#login-password');
  const setProfil = (value, maxAge) => { document.cookie = 'bp_profil=' + value + '; path=/; max-age=' + maxAge + '; SameSite=Lax'; };

  setProfil('', 0); // arriver sur la connexion = se déconnecter

  function showError(msg) {
    $('#login-error-text').textContent = msg;
    $('#login-error').hidden = !msg;
  }

  form.addEventListener('submit', e => {
    e.preventDefault();
    showError('');
    const login = id.value.trim().toLowerCase();
    id.classList.toggle('is-invalid', !login);
    pwd.classList.toggle('is-invalid', !pwd.value);
    $('#password-feedback').hidden = !!pwd.value;
    if (!login || !pwd.value) { (login ? pwd : id).focus(); return; }
    if (login === 'inactif' && pwd.value === 'demo') { showError('Ce compte est désactivé. Contactez l’administrateur.'); return; }
    if (!HOMES[login] || pwd.value !== 'demo') { showError('Identifiant ou mot de passe incorrect.'); pwd.value = ''; pwd.focus(); return; }
    setProfil(login, 60 * 60 * 8);
    try { sessionStorage.setItem('bp_welcome', '1'); } catch (err) { /* ignore */ }
    location.href = HOMES[login];
  });

  [id, pwd].forEach(el => el.addEventListener('input', () => {
    el.classList.remove('is-invalid');
    if (el === pwd) $('#password-feedback').hidden = true;
  }));

  const toggle = $('#toggle-password');
  if (toggle) toggle.addEventListener('click', e => {
    const show = pwd.type === 'password';
    pwd.type = show ? 'text' : 'password';
    e.currentTarget.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    e.currentTarget.innerHTML = '<i class="fa-regular ' + (show ? 'fa-eye-slash' : 'fa-eye') + '" aria-hidden="true"></i>';
  });

  const forgot = $('#login-forgot');
  if (forgot) forgot.addEventListener('click', () => BP.toast('Contactez l’administrateur pour réinitialiser votre mot de passe.', 'info'));
  BP.$$('[data-login-social]').forEach(btn => btn.addEventListener('click', () => {
    BP.toast('Connexion avec ' + btn.dataset.loginSocial + ' non disponible.', 'info');
  }));
})();
