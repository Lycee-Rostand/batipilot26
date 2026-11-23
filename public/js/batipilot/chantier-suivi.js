/* ============================================================================
   BATIPILOT - route « chantier_suivi » (création / suivi d'un chantier)
   ----------------------------------------------------------------------------
   Reprend la maquette de la professeure : Général → un poste par étape →
   Récapitulatif. Chaque point de contrôle a une case « terminée », une
   réponse (oui/non, date, texte ou nombre) et une note complémentaire.
   route « chantier_suivi »          → nouveau chantier
   route « chantier_suivi »?id=XXX   → suivi d'un chantier existant
   Salarié : consultation seule (aucune modification possible).
   ============================================================================ */
(function () {
  'use strict';

  const { $, $$, esc, dateFR, badge } = BP;
  const C = BP.chantiers;
  const STEPS = C.STEPS;
  const LAST = STEPS.length - 1;

  const requestedId = new URLSearchParams(location.search).get('id');
  const stored = requestedId ? C.findSite(requestedId) : null;
  if (requestedId && !stored) {
    BP.flash('Ce chantier est introuvable.', 'error');
    location.replace(BP.readOnly ? BP.url('employe_chantiers') : BP.url('chantiers'));
    return;
  }
  const archived = !!(stored && C.isArchived(stored.id));
  const readOnly = archived || BP.readOnly;   // gabarit employé = consultation
  const LIST_URL = BP.readOnly ? BP.url('employe_chantiers') : BP.url('chantiers');
  if (!stored && readOnly) { location.replace(LIST_URL); return; }

  const site = stored ? JSON.parse(JSON.stringify(stored)) : C.newSite();
  site.general = site.general || {};
  site.tasks = site.tasks || {};
  site.custom = site.custom || {};
  let step = 0;

  /* --- Gabarits de champs ------------------------------------------------- */
  function field(label, id, value, type, opts) {
    const o = opts || {};
    return '<div class="bp-field' + (o.full ? ' bp-field--full' : '') + '">' +
      '<label for="' + id + '">' + label + (o.required ? '<span class="bp-required" aria-hidden="true">*</span>' : '') + '</label>' +
      '<input class="form-control" id="' + id + '" type="' + (type || 'text') + '" value="' + esc(value) + '"' + (o.placeholder ? ' placeholder="' + esc(o.placeholder) + '"' : '') + '>' +
      '<div class="invalid-feedback" data-feedback="' + id + '"></div></div>';
  }
  function select(label, id, options, value, opts) {
    const o = opts || {};
    return '<div class="bp-field' + (o.full ? ' bp-field--full' : '') + '">' +
      '<label for="' + id + '">' + label + (o.required ? '<span class="bp-required" aria-hidden="true">*</span>' : '') + '</label>' +
      '<select class="form-select" id="' + id + '">' + options.map(([v, t]) => '<option value="' + esc(v) + '"' + (value === v ? ' selected' : '') + '>' + esc(t) + '</option>').join('') + '</select>' +
      '<div class="invalid-feedback" data-feedback="' + id + '"></div></div>';
  }

  /* --- Étape 1 : informations générales ------------------------------------ */
  function generalHTML() {
    const g = site.general;
    const quotes = C.availableQuotes();
    const options = [['', '- Sélectionner un devis -']].concat(quotes.map(x => [x.ref, x.ref + ' - ' + (x.q.client1?.name || 'Client') + ' (' + (x.q.house?.city || 'Ville') + ')']));
    if (g.quoteRef && !quotes.some(x => x.ref === g.quoteRef)) options.push([g.quoteRef, g.quoteRef + ' - ' + (site.client?.name || 'devis du chantier')]);
    return '<h2 class="bp-form-card__title">Informations générales du chantier</h2>' +
      '<p class="bp-form-card__intro">Choisissez le devis d’origine : les informations client et maison sont reprises automatiquement, sans ressaisie.</p>' +
      '<div class="bp-form-grid">' +
        select('Devis d’origine', 'general_quoteRef', options, g.quoteRef || '', { full: true, required: true }) +
        '<div class="bp-field bp-field--full" id="quote-preview"></div>' +
        select('Statut du chantier', 'general_status', [['upcoming', 'À venir'], ['current', 'En cours'], ['done', 'Terminé']], g.status || 'upcoming') +
        field('Date de démarrage', 'general_start', g.start, 'date', { required: true }) +
        field('Date de fin prévue', 'general_end', g.end, 'date') +
        '<div class="bp-field bp-field--full"><label for="general_notes">Observations générales</label><textarea class="form-control" id="general_notes" rows="4">' + esc(g.notes || '') + '</textarea></div>' +
      '</div>' +
      (quotes.length ? '' : '<div class="bp-notice"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>Aucun devis enregistré trouvé dans ce navigateur. Créez et enregistrez d’abord un devis dans le module <a class="bp-link" href="' + BP.url('devis') + '">Devis</a>.</span></div>');
  }

  function renderQuotePreview() {
    const el = $('#quote-preview');
    if (!el) return;
    if (!site.general.quoteRef) { el.innerHTML = ''; return; }
    const c = site.client || {}, h = site.house || {};
    el.innerHTML = '<div class="bp-subcard mb-0">' +
      '<div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2"><h3 class="bp-subcard__title mb-0">Informations issues du devis</h3><span class="bp-tag"><i class="fa-solid fa-lock me-1" aria-hidden="true"></i>Lecture seule</span></div>' +
      '<dl class="bp-dl">' +
        '<dt>Client</dt><dd>' + esc(c.name || '-') + ' · ' + esc(c.phone || '-') + ' · ' + esc(c.email || '-') + '</dd>' +
        '<dt>Maison</dt><dd>' + esc([h.number, h.street, h.zip, h.city].filter(Boolean).join(' ') || '-') + '</dd>' +
        '<dt>Surfaces</dt><dd>' + esc(h.floor || '-') + ' m² au sol · ' + esc(h.living || '-') + ' m² habitables · Phénix : ' + (h.phenix ? 'Oui' : 'Non') + '</dd>' +
      '</dl></div>';
  }

  /* --- Étapes poste : points de contrôle ------------------------------------ */
  function taskInput(t, v) {
    const id = esc(t.id);
    if (t.type === 'boolean') {
      return '<select class="form-select" data-task-value="' + id + '" id="val-' + id + '">' +
        '<option value=""' + (!v.value ? ' selected' : '') + '>Non renseigné</option>' +
        '<option value="oui"' + (v.value === 'oui' ? ' selected' : '') + '>Oui</option>' +
        '<option value="non"' + (v.value === 'non' ? ' selected' : '') + '>Non</option></select>';
    }
    if (t.type === 'text') return '<textarea class="form-control" data-task-value="' + id + '" id="val-' + id + '" placeholder="Saisir une observation…">' + esc(v.value || '') + '</textarea>';
    return '<input class="form-control" data-task-value="' + id + '" id="val-' + id + '" type="' + (t.type === 'date' ? 'date' : 'number') + '"' + (t.type === 'number' ? ' min="0" step="0.01"' : '') + ' value="' + esc(v.value || '') + '">';
  }

  function taskHTML(t) {
    const v = site.tasks[t.id] || {};
    const id = esc(t.id);
    return '<div class="bp-task' + (v.done ? ' is-done' : '') + '">' +
      '<div class="bp-task__head">' +
        '<input class="form-check-input" type="checkbox" id="done-' + id + '" data-task-done="' + id + '"' + (v.done ? ' checked' : '') + ' aria-label="Tâche ' + id + ' terminée">' +
        '<span class="bp-task__code">' + (t.custom ? '' : id) + '</span>' +
        '<label class="bp-task__title" for="done-' + id + '">' + esc(t.label) + (t.custom ? ' <span class="bp-tag">Personnalisée</span>' : '') + '</label>' +
        (t.custom ? '<button type="button" class="bp-link bp-link--danger" data-remove="' + id + '">Supprimer</button>' : '') +
      '</div>' +
      '<div class="bp-task__controls">' +
        '<div class="bp-field"><label for="val-' + id + '">' + C.TASK_TYPES[t.type] + '</label>' + taskInput(t, v) + '</div>' +
        '<div class="bp-field"><label for="note-' + id + '">Note complémentaire</label><input class="form-control" id="note-' + id + '" data-task-note="' + id + '" value="' + esc(v.note || '') + '" placeholder="Facultatif"></div>' +
      '</div></div>';
  }

  function statsHTML(poste) {
    const p = C.progress(site, poste);
    return '<span class="bp-tag">' + p.done + ' / ' + p.total + ' tâches terminées</span>' +
      '<div class="bp-progress bp-progress--sm bp-progress--success"><div class="bp-progress__bar" style="width:' + p.percent + '%"></div></div>';
  }

  function posteHTML(poste) {
    return '<h2 class="bp-form-card__title">' + esc(poste) + '</h2>' +
      '<p class="bp-form-card__intro">Suivez les actions à réaliser. La case à cocher indique que la tâche est terminée. La réponse associée peut être une date, un texte, un nombre ou un oui / non.</p>' +
      '<div class="bp-inline-stats" id="poste-stats">' + statsHTML(poste) + '</div>' +
      '<div class="bp-task-list">' + C.tasksFor(site, poste).map(taskHTML).join('') + '</div>' +
      (readOnly ? '' :
      '<div class="bp-add-panel">' +
        '<h3 class="bp-add-panel__title"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Ajouter une tâche spécifique au chantier</h3>' +
        '<div class="bp-form-grid">' +
          field('Libellé de la tâche', 'custom_label', '', 'text', { placeholder: 'Ex. Contrôle de livraison' }) +
          select('Format de réponse', 'custom_type', [['boolean', 'Oui / Non'], ['date', 'Date'], ['text', 'Texte'], ['number', 'Nombre']], 'boolean') +
        '</div>' +
        '<div class="mt-3"><button type="button" class="m-btn m-btn--ghost" id="add-task"><i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter cette tâche</button></div>' +
      '</div>');
  }

  /* --- Récapitulatif --------------------------------------------------------- */
  function recapHTML() {
    const g = site.general;
    const st = C.STATUSES[g.status] || C.STATUSES.upcoming;
    const boxes = C.POSTES.map(poste => {
      const p = C.progress(site, poste);
      return '<div class="bp-summary-box"><span class="bp-summary-box__label">' + esc(poste) + '</span>' +
        '<strong class="bp-summary-box__value">' + p.done + ' / ' + p.total + '</strong>' +
        '<div class="bp-progress bp-progress--success"><div class="bp-progress__bar" style="width:' + p.percent + '%"></div></div>' +
        '<div class="bp-help mt-1">tâches terminées</div></div>';
    }).join('');
    const canArchive = !readOnly && g.status === 'done' && !!C.findSite(site.id);
    return '<h2 class="bp-form-card__title">Récapitulatif du chantier</h2>' +
      '<p class="bp-form-card__intro">Synthèse des informations et de l’avancement par poste.</p>' +
      '<div class="bp-subcard"><div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">' +
        '<h3 class="bp-subcard__title mb-0">' + esc(site.client?.name || 'Client non renseigné') + '</h3>' + badge(archived ? 'Archivé' : st.label, archived ? 'neutral' : st.badge) + '</div>' +
        '<dl class="bp-dl">' +
          '<dt>Référence</dt><dd>' + esc(site.reference) + '</dd>' +
          '<dt>Ville</dt><dd>' + esc(site.house?.city || 'Ville non renseignée') + '</dd>' +
          '<dt>Devis</dt><dd>' + C.quoteLink(g.quoteRef) + '</dd>' +
          '<dt>Démarrage</dt><dd>' + dateFR(g.start) + '</dd>' +
          '<dt>Fin prévue</dt><dd>' + dateFR(g.end) + '</dd>' +
        '</dl></div>' +
      '<div class="bp-summary-grid">' + boxes + '</div>' +
      '<div class="bp-notice"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>Vous pouvez revenir à chaque poste pour compléter les tâches, modifier les dates et ajouter des actions spécifiques.</span></div>' +
      (canArchive ? '<div class="mt-3"><button type="button" class="m-btn m-btn--ghost" id="archive-site"><i class="fa-solid fa-box-archive" aria-hidden="true"></i> Archiver ce chantier terminé</button></div>' : '');
  }

  /* --- Rendu ------------------------------------------------------------------ */
  function renderStep() {
    $('#step-heading').textContent = STEPS[step];
    $('#step-count').textContent = 'Étape ' + (step + 1) + ' sur ' + STEPS.length;
    $('#step-progress').style.width = (100 * (step + 1) / STEPS.length) + '%';
    $('#step-chips').innerHTML = STEPS.map((n, i) =>
      '<button type="button" class="bp-step' + (i === step ? ' is-active' : i < step ? ' is-done' : '') + '" data-goto="' + i + '"' + (i === step ? ' aria-current="step"' : '') + '>' +
        '<span class="bp-step__num">' + (i + 1) + '.</span><span>' + esc(n) + '</span></button>').join('');
    $('#prev-step').disabled = step === 0;
    $('#next-step').innerHTML = step === LAST
      ? (readOnly ? 'Retour aux chantiers' : 'Enregistrer et terminer <i class="fa-solid fa-check" aria-hidden="true"></i>')
      : 'Suivant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';

    $('#step-content').innerHTML = step === 0 ? generalHTML() : step === LAST ? recapHTML() : posteHTML(STEPS[step]);
    if (step === 0) renderQuotePreview();
    if (readOnly) $$('#step-content input, #step-content select, #step-content textarea').forEach(el => { el.disabled = true; });
  }

  function collect() {
    if (readOnly) return;
    const g = site.general;
    if (step === 0) {
      const ref = $('#general_quoteRef')?.value || '';
      if (ref !== g.quoteRef) C.applyQuote(site, ref);
      ['status', 'start', 'end'].forEach(k => { g[k] = $('#general_' + k)?.value || ''; });
      g.notes = $('#general_notes')?.value || '';
    } else if (step < LAST) {
      const ensure = id => (site.tasks[id] = site.tasks[id] || {});
      $$('[data-task-done]').forEach(el => { ensure(el.dataset.taskDone).done = el.checked; });
      $$('[data-task-value]').forEach(el => { ensure(el.dataset.taskValue).value = el.value; });
      $$('[data-task-note]').forEach(el => { ensure(el.dataset.taskNote).note = el.value; });
    }
  }

  function goTo(i) {
    collect();
    step = Math.max(0, Math.min(LAST, i));
    renderStep();
    $('#bp-stepper').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  /* --- Validation + enregistrement -------------------------------------------- */
  function showErrors(errors) {
    $$('.is-invalid', $('#step-content')).forEach(el => el.classList.remove('is-invalid'));
    Object.entries(errors).forEach(([id, msg]) => {
      $('#' + id)?.classList.add('is-invalid');
      const fb = $('[data-feedback="' + id + '"]');
      if (fb) fb.textContent = msg;
    });
    const first = Object.keys(errors)[0];
    if (first) $('#' + first)?.focus();
  }
  function save() {
    collect();
    const g = site.general, errors = {};
    if (!g.quoteRef) errors.general_quoteRef = 'Sélectionnez un devis d’origine.';
    if (!g.start) errors.general_start = 'Renseignez la date de démarrage.';
    if (g.start && g.end && g.end < g.start) errors.general_end = 'La fin prévue doit être postérieure au démarrage.';
    if (Object.keys(errors).length) {
      if (step !== 0) { step = 0; renderStep(); }
      showErrors(errors);
      BP.toast('Complétez les informations générales avant d’enregistrer.', 'error');
      return false;
    }
    return C.saveSite(site);
  }
  function saveAndLeave() {
    if (readOnly) { location.href = archived ? BP.url('chantiers_archives') : LIST_URL; return; }
    if (!save()) return;
    BP.flash('Chantier ' + site.reference + ' enregistré');
    location.href = BP.url('chantiers');
  }

  /* --- Événements ------------------------------------------------------------- */
  $('#step-chips').addEventListener('click', e => { const b = e.target.closest('[data-goto]'); if (b) goTo(Number(b.dataset.goto)); });
  $('#prev-step').addEventListener('click', () => goTo(step - 1));
  $('#next-step').addEventListener('click', () => { if (step < LAST) goTo(step + 1); else saveAndLeave(); });
  $('#save-draft').addEventListener('click', saveAndLeave);
  $('#wizard-form').addEventListener('submit', e => e.preventDefault());

  const content = $('#step-content');
  content.addEventListener('change', e => {
    const t = e.target;
    if (t.id === 'general_quoteRef') { C.applyQuote(site, t.value); t.classList.remove('is-invalid'); renderQuotePreview(); return; }
    if (t.matches('[data-task-done]')) {
      collect();
      t.closest('.bp-task').classList.toggle('is-done', t.checked);
      $('#poste-stats').innerHTML = statsHTML(STEPS[step]);
      return;
    }
    if (t.matches('[data-task-value],[data-task-note]')) collect();
  });
  content.addEventListener('click', e => {
    const remove = e.target.closest('[data-remove]');
    if (remove) {
      collect();
      const poste = STEPS[step], id = remove.dataset.remove;
      site.custom[poste] = (site.custom[poste] || []).filter(t => t.id !== id);
      delete site.tasks[id];
      renderStep();
      BP.toast('Tâche supprimée', 'info');
      return;
    }
    if (e.target.closest('#add-task')) {
      const labelEl = $('#custom_label');
      const label = labelEl.value.trim();
      if (!label) { labelEl.classList.add('is-invalid'); $('[data-feedback="custom_label"]').textContent = 'Saisissez le libellé de la tâche.'; labelEl.focus(); return; }
      collect();
      const poste = STEPS[step], id = BP.uid('T');
      site.custom[poste] = site.custom[poste] || [];
      site.custom[poste].push({ id, label, type: $('#custom_type').value });
      site.tasks[id] = { done: false, value: '', note: '' };
      renderStep();
      BP.toast('Tâche ajoutée');
      return;
    }
    if (e.target.closest('#archive-site')) {
      BP.confirm({ title: 'Archiver ce chantier ?', message: 'Le chantier ' + site.reference + ' sera déplacé dans les chantiers archivés. Il pourra être restauré.', confirmLabel: 'Archiver' })
        .then(ok => {
          if (!ok || !save()) return;
          if (C.archiveSite(site.id)) { BP.flash('Chantier ' + site.reference + ' archivé'); location.href = BP.url('chantiers'); }
          else BP.toast('Seuls les chantiers terminés peuvent être archivés', 'warning');
        });
    }
  });

  /* --- Démarrage ---------------------------------------------------------------- */
  const title = stored ? 'Suivi du chantier ' + site.reference : 'Nouveau chantier';
  $('#wizard-title').textContent = title;
  $('#wizard-crumb').textContent = stored ? site.reference : 'Nouveau chantier';
  document.title = title + ' | BatiPilot';
  if (stored && !BP.readOnly) {
    const kpi = $('#btn-rentabilite');
    kpi.href = BP.url('rentabilite') + '?id=' + encodeURIComponent(site.id);
    kpi.hidden = false;
  }
  if (readOnly) {
    $('#readonly-notice').hidden = false;
    $('#readonly-text').textContent = archived ? 'Chantier archivé : consultation seule.' : 'Consultation seule.';
    $$('a[href="' + BP.url('chantiers') + '"]').forEach(a => { a.href = LIST_URL; });
    $('#save-draft').hidden = true;
  }
  const hashStep = /^#etape-(\d+)$/.exec(location.hash);
  if (hashStep) step = Math.max(0, Math.min(LAST, Number(hashStep[1]) - 1));
  renderStep();
})();
