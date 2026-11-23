/* ============================================================================
   BATIPILOT - route « devis_edition » : assistant de création de devis
   ----------------------------------------------------------------------------
   Reprend la maquette de la professeure (12 étapes) :
   Client → Maison → Structure → 8 postes → Récapitulatif.
   route « devis_edition »            → nouveau devis
   route « devis_edition »?ref=XXX    → reprendre un brouillon
   Montants indicatifs, hors taxes.
   ============================================================================ */
(function () {
  'use strict';

  const { $, $$, esc } = BP;
  const D = BP.devis;
  const { num, money } = D;
  const STEPS = D.STEPS, LAST = STEPS.length - 1;

  const state = D.load();
  const editing = new URLSearchParams(location.search).get('ref');
  let quote = {};
  if (editing) {
    const found = D.findRow(state, editing);
    if (!found) { BP.flash('Ce devis est introuvable.', 'error'); location.replace(BP.url('devis')); return; }
    quote = state.records[editing] ? JSON.parse(JSON.stringify(state.records[editing])) : { client1: { name: found.row[2] }, house: { city: found.row[3] } };
  }
  let step = 0;

  /* --- Gabarits -------------------------------------------------------------- */
  function field(label, id, value, type, opts) {
    const o = opts || {};
    return '<div class="bp-field' + (o.full ? ' bp-field--full' : '') + '">' +
      '<label for="' + id + '">' + esc(label) + (o.required ? '<span class="bp-required" aria-hidden="true">*</span>' : '') + '</label>' +
      '<input class="form-control" id="' + id + '" type="' + (type || 'text') + '"' + (type === 'number' ? ' min="0" step="0.01"' : '') + ' value="' + esc(value ?? '') + '">' +
      '<div class="invalid-feedback" data-feedback="' + id + '"></div></div>';
  }

  function personBlock(n) {
    const p = quote['client' + n] || {};
    return '<div class="bp-subcard"><h3 class="bp-subcard__title mb-3">' + (n === 1 ? 'Personne 1' : 'Deuxième personne (facultatif)') + '</h3><div class="bp-form-grid">' +
      '<div class="bp-field"><label for="p' + n + 'civ">Civilité</label><select class="form-select" id="p' + n + 'civ">' +
        ['Madame', 'Monsieur', 'Autre'].map(x => '<option' + (p.civility === x ? ' selected' : '') + '>' + x + '</option>').join('') + '</select></div>' +
      field('Nom et prénom', 'p' + n + 'name', p.name, 'text', { required: n === 1 }) +
      (n === 1 ? field('Date de naissance', 'p1birth', p.birth, 'date') + field('Lieu de naissance', 'p1birthplace', p.birthplace) : '') +
      field('Téléphone', 'p' + n + 'phone', p.phone, 'tel') +
      field('Mail', 'p' + n + 'email', p.email, 'email') +
    '</div></div>';
  }

  function clientHTML() {
    return '<h2 class="bp-form-card__title">Informations client</h2><p class="bp-form-card__intro">Renseignez les coordonnées du ou des clients.</p>' +
      personBlock(1) +
      '<label class="bp-check mb-3"><input class="form-check-input" type="checkbox" id="hasSecond"' + (quote.hasSecond ? ' checked' : '') + '> Ajouter une deuxième personne</label>' +
      '<div id="secondPerson"' + (quote.hasSecond ? '' : ' hidden') + '>' + personBlock(2) + '</div>';
  }

  function maisonHTML() {
    const h = quote.house || {};
    return '<h2 class="bp-form-card__title">Informations maison</h2><p class="bp-form-card__intro">Adresse et caractéristiques générales du bien.</p><div class="bp-form-grid">' +
      field('Numéro de rue', 'hnumber', h.number) + field('Rue', 'hstreet', h.street) +
      field('Code postal', 'hzip', h.zip) + field('Ville', 'hcity', h.city, 'text', { required: true }) +
      field('Année de construction', 'hyear', h.year, 'number') + field('Référence cadastrale', 'hcadastral', h.cadastral) +
      field('Surface cadastrale (m²)', 'hcadasurface', h.cadasurface, 'number') + field('Surface habitable existante (m²)', 'hliving', h.living, 'number') +
      field('Surface au sol estimée (m²)', 'hgroundEstimated', h.groundEstimated, 'number') + field('Surface habitable estimée (m²)', 'hlivingEstimated', h.livingEstimated, 'number', { required: true }) +
      '<div class="bp-field"><label for="hroof">Type de charpente</label><select class="form-select" id="hroof"><option' + (h.roof !== 'Fermette' ? ' selected' : '') + '>Traditionnelle</option><option' + (h.roof === 'Fermette' ? ' selected' : '') + '>Fermette</option></select></div>' +
      '<div class="bp-field bp-field--full"><label class="bp-check"><input class="form-check-input" type="checkbox" id="phenix"' + (h.phenix ? ' checked' : '') + '> Maison Phénix</label></div>' +
    '</div>';
  }

  function structureHTML() {
    const s = quote.structure || {}, area = num(quote.house?.livingEstimated), ph = !!quote.house?.phenix;
    return '<h2 class="bp-form-card__title">Structure / Charpente</h2><div class="bp-post-total" id="postTotal"></div>' +
      '<p class="bp-form-card__intro">Les références CH1 et CH2 ainsi que CH16 et CH17 sont conservées du fichier source. Les intitulés CH3 à CH15 ne figuraient pas dans ce fichier.</p>' +
      '<div class="bp-calc-row"><span>Surface habitable estimée</span><strong>' + area.toLocaleString('fr-FR') + ' m²</strong><span>×</span>' +
        '<label>Montant unitaire (€ / m²) <input class="form-control" type="number" id="structureRate" min="0" step="0.01" value="' + esc(s.rate ?? 0) + '"></label><span>=</span><strong id="structureBase">-</strong></div>' +
      (ph ? '<div class="bp-calc-row"><strong>Maison Phénix</strong><span>' + area.toLocaleString('fr-FR') + ' m² ×</span>' +
        '<label>Montant unitaire Phénix (€ / m²) <input class="form-control" type="number" id="phenixRate" min="0" step="0.01" value="' + esc(s.phenixRate ?? 0) + '"></label><span>=</span><strong id="phenixBase">-</strong></div>' : '') +
      '<div class="bp-notice"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>La ligne Maison Phénix est un supplément distinct ajouté à la base Structure. Les montants unitaires sont à renseigner selon votre grille tarifaire.</span></div>' +
      '<div class="bp-task-list">' + D.CH.map(([code, desc], i) => {
        const v = s.items?.[code] || {};
        return '<div class="bp-task' + (v.on ? ' is-done' : '') + '"><div class="bp-task__head">' +
          '<input class="form-check-input ch-check" type="checkbox" id="ch-' + code + '" data-code="' + code + '"' + (v.on ? ' checked' : '') + '>' +
          '<span class="bp-task__code">' + code + '</span><label class="bp-task__title bp-task__title--desc" for="ch-' + code + '">' + esc(desc) + '</label></div>' +
          (i >= 15 ? '<div class="bp-item-price"><div class="bp-help">' + (code === 'CH16' ? 'Montant indicatif source : 600 à 1 000 €' : 'Montant source : 800 €') + '</div>' +
            '<div class="bp-field"><label for="cha-' + code + '">Montant (€)</label><input class="form-control ch-amount" id="cha-' + code + '" type="number" data-code="' + code + '" min="0" step="0.01" value="' + esc(v.amount ?? (code === 'CH16' ? 600 : 800)) + '"></div>' +
            '<div class="bp-field"><span class="bp-label">Montant retenu</span><strong class="bp-line-total" data-ch-total="' + code + '">-</strong></div></div>' : '') +
        '</div>';
      }).join('') + '</div>';
  }

  function postHTML(stepIndex) {
    const [key, title] = D.MAPPING[stepIndex];
    return '<h2 class="bp-form-card__title">' + title + '</h2><div class="bp-post-total" id="postTotal"></div>' +
      '<p class="bp-form-card__intro">Cochez les prestations à retenir, puis ajustez les quantités et les prix unitaires. Les prix ajoutés sont indicatifs.</p>' +
      '<div class="bp-task-list">' + D.CATALOG[key].map(([code, desc, unit, price]) => {
        const v = quote.posts?.[key]?.[code] || {};
        return '<div class="bp-task' + (v.on ? ' is-done' : '') + '"><div class="bp-task__head">' +
          '<input class="form-check-input item-check" type="checkbox" id="it-' + code + '" data-code="' + code + '"' + (v.on ? ' checked' : '') + '>' +
          '<span class="bp-task__code">' + code + '</span><label class="bp-task__title bp-task__title--desc" for="it-' + code + '">' + esc(desc) + '</label></div>' +
          '<div class="bp-item-price"><div class="bp-help">Unité : ' + esc(unit) + (unit === 'forfait' ? ' · forfait sélectionnable' : '') + '</div>' +
            '<div class="bp-field"><label for="q-' + code + '">' + (unit === 'forfait' ? 'Quantité' : 'Quantité (' + esc(unit) + ')') + '</label><input class="form-control item-qty" id="q-' + code + '" type="number" min="0" step="0.01" data-code="' + code + '" value="' + esc(v.qty ?? 1) + '"></div>' +
            '<div class="bp-field"><label for="r-' + code + '">Montant unitaire (€)</label><input class="form-control item-rate" id="r-' + code + '" type="number" min="0" step="0.01" data-code="' + code + '" value="' + esc(v.rate ?? price) + '"></div>' +
          '</div>' +
          '<div class="bp-help bp-item-total">Montant calculé : <strong class="bp-line-total" data-total="' + code + '">-</strong></div>' +
        '</div>';
      }).join('') + '</div>';
  }

  function summaryHTML() {
    const h = quote.house || {}, p = quote.client1 || {};
    const rows = [['Structure', D.structureTotal(quote)]].concat(Object.values(D.MAPPING).map(([key, title]) => [title, D.postTotal(quote, key)]));
    return '<h2 class="bp-form-card__title">Récapitulatif du devis</h2><p class="bp-form-card__intro">Montants calculés par poste, sans TVA ni TTC.</p>' +
      '<div class="bp-subcard"><h3 class="bp-subcard__title">Client</h3>' + esc(p.name || '-') + ' · ' + esc(p.phone || '') + ' · ' + esc(p.email || '') + '</div>' +
      '<div class="bp-subcard"><h3 class="bp-subcard__title">Maison</h3>' + esc([h.number, h.street, h.zip, h.city].filter(Boolean).join(' ')) +
        '<p class="bp-help mb-0 mt-1">Surface au sol estimée : ' + num(h.groundEstimated) + ' m² · Surface habitable estimée : ' + num(h.livingEstimated) + ' m² · Maison Phénix : ' + (h.phenix ? 'Oui' : 'Non') + '</p></div>' +
      '<div class="bp-subcard bp-subcard--soft mb-0"><h3 class="bp-subcard__title mb-2">Totaux par poste</h3>' +
        rows.map(([label, total]) => '<div class="bp-total-row"><span>' + esc(label) + '</span><strong>' + money(total) + '</strong></div>').join('') +
        '<div class="bp-total-row bp-total-row--grand"><strong>Total général</strong><strong>' + money(D.grandTotal(quote)) + '</strong></div>' +
      '</div>';
  }

  /* --- Rendu ------------------------------------------------------------------ */
  function renderStep() {
    $('#step-heading').textContent = STEPS[step];
    $('#step-count').textContent = 'Étape ' + (step + 1) + ' sur ' + STEPS.length;
    $('#step-progress').style.width = (100 * (step + 1) / STEPS.length) + '%';
    $('#step-chips').innerHTML = STEPS.map((s, i) =>
      '<button type="button" class="bp-step' + (i === step ? ' is-active' : i < step ? ' is-done' : '') + '" data-goto="' + i + '"' + (i === step ? ' aria-current="step"' : '') + '>' +
        '<span class="bp-step__num">' + (i + 1) + '.</span><span>' + esc(s) + '</span></button>').join('');
    $('#prev-step').disabled = step === 0;
    $('#next-step').innerHTML = step === LAST ? 'Terminer le devis <i class="fa-solid fa-check" aria-hidden="true"></i>' : 'Suivant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
    let html;
    if (step === 0) html = clientHTML();
    else if (step === 1) html = maisonHTML();
    else if (step === 2) html = structureHTML();
    else if (D.MAPPING[step]) html = postHTML(step);
    else html = summaryHTML();
    $('#step-content').innerHTML = html;
    calcVisible();
  }

  function setError(id, msg) {
    const el = $('#' + id);
    if (el) el.classList.add('is-invalid');
    const fb = $('[data-feedback="' + id + '"]');
    if (fb) fb.textContent = msg;
    el?.focus();
  }

  /* Recopie l'étape affichée dans `quote` ; valide si demandé */
  function collectStep(validate) {
    if (step === 0) {
      for (const n of [1, 2]) {
        const p = {};
        for (const [k, id] of Object.entries({ civility: 'civ', name: 'name', birth: 'birth', birthplace: 'birthplace', phone: 'phone', email: 'email' })) p[k] = $('#p' + n + id)?.value || '';
        quote['client' + n] = p;
      }
      quote.hasSecond = $('#hasSecond').checked;
      if (validate && !quote.client1.name.trim()) { setError('p1name', 'Le nom du premier client est obligatoire.'); BP.toast('Le nom du premier client est obligatoire', 'error'); return false; }
    } else if (step === 1) {
      const h = {};
      for (const k of ['number', 'street', 'zip', 'city', 'year', 'cadastral', 'cadasurface', 'living', 'groundEstimated', 'livingEstimated']) h[k] = $('#h' + k)?.value || '';
      h.roof = $('#hroof').value;
      h.phenix = $('#phenix').checked;
      quote.house = h;
      if (validate && !h.city.trim()) { setError('hcity', 'Renseignez la ville.'); return false; }
      if (validate && num(h.livingEstimated) <= 0) { setError('hlivingEstimated', 'Renseignez une surface habitable estimée valide.'); return false; }
    } else if (step === 2) {
      const s = quote.structure || {};
      s.rate = num($('#structureRate').value);
      s.phenixRate = num($('#phenixRate')?.value ?? s.phenixRate);
      s.items = s.items || {};
      $$('.ch-check').forEach(el => {
        const code = el.dataset.code;
        s.items[code] = Object.assign({ on: el.checked }, (code === 'CH16' || code === 'CH17') ? { amount: num($('.ch-amount[data-code="' + code + '"]')?.value) } : {});
      });
      quote.structure = s;
    } else if (D.MAPPING[step]) {
      const key = D.MAPPING[step][0];
      quote.posts = quote.posts || {};
      quote.posts[key] = quote.posts[key] || {};
      $$('.item-check').forEach(el => {
        const code = el.dataset.code;
        quote.posts[key][code] = { on: el.checked, qty: num($('.item-qty[data-code="' + code + '"]')?.value), rate: num($('.item-rate[data-code="' + code + '"]')?.value) };
      });
    }
    return true;
  }

  function calcVisible() {
    if (step === 2) {
      const s = quote.structure || {}, a = num(quote.house?.livingEstimated);
      $('#structureBase').textContent = money(a * num(s.rate));
      if ($('#phenixBase')) $('#phenixBase').textContent = money(a * num(s.phenixRate));
      for (const c of ['CH16', 'CH17']) { const e = $('[data-ch-total="' + c + '"]'); if (e) e.textContent = money(s.items?.[c]?.on ? s.items[c].amount : 0); }
      $('#postTotal').textContent = 'Total du poste Structure : ' + money(D.structureTotal(quote));
    } else if (D.MAPPING[step]) {
      const key = D.MAPPING[step][0];
      for (const [code] of D.CATALOG[key]) {
        const v = quote.posts?.[key]?.[code];
        const e = $('[data-total="' + code + '"]');
        if (e) e.textContent = money(v?.on ? num(v.qty) * num(v.rate) : 0);
      }
      $('#postTotal').textContent = 'Total du poste : ' + money(D.postTotal(quote, key));
    }
  }

  function saveQuote() {
    if (!collectStep(true)) return;
    const p = quote.client1 || {}, h = quote.house || {};
    const ref = editing || 'DEV-' + new Date().getFullYear() + '-' + String(Date.now()).slice(-7);
    const row = [ref, new Date().toLocaleDateString('fr-FR'), p.name || 'Nouveau client', h.city || '-', 'V0'];
    Object.keys(state.data).forEach(k => { state.data[k] = state.data[k].filter(x => x[0] !== ref); });
    state.data.draft.unshift(row);
    state.records[ref] = JSON.parse(JSON.stringify(quote));
    if (!D.persist(state)) return;
    BP.flash('Devis ' + ref + ' enregistré en brouillon');
    location.href = BP.url('devis');
  }

  /* --- Événements ------------------------------------------------------------- */
  const content = $('#step-content');
  content.addEventListener('input', e => {
    e.target.classList.remove('is-invalid');
    if (step === 2 || D.MAPPING[step]) {
      collectStep(false);
      calcVisible();
      const card = e.target.closest('.bp-task');
      const check = card?.querySelector('.ch-check, .item-check');
      if (card && check) card.classList.toggle('is-done', check.checked);
    }
  });
  content.addEventListener('change', e => {
    if (e.target.id === 'hasSecond') $('#secondPerson').hidden = !e.target.checked;
  });
  $('#prev-step').addEventListener('click', () => { collectStep(false); step = Math.max(0, step - 1); renderStep(); });
  $('#next-step').addEventListener('click', () => {
    if (!collectStep(true)) return;
    if (step < LAST) { step++; renderStep(); $('#bp-stepper').scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    else saveQuote();
  });
  $('#save-draft').addEventListener('click', saveQuote);
  $('#step-chips').addEventListener('click', e => {
    const b = e.target.closest('[data-goto]');
    if (!b) return;
    const t = Number(b.dataset.goto);
    if (t > step && !collectStep(true)) return;
    if (t <= step) collectStep(false);
    step = t;
    renderStep();
  });
  $('#wizard-form').addEventListener('submit', e => e.preventDefault());

  /* --- Démarrage ---------------------------------------------------------------- */
  const title = editing ? 'Modifier le devis ' + editing : 'Nouveau devis';
  $('#wizard-title').textContent = title;
  $('#wizard-crumb').textContent = editing || 'Nouveau devis';
  document.title = title + ' | BatiPilot';
  renderStep();
})();
