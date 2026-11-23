/* ============================================================================
   BATIPILOT - route « rentabilite » : rentabilité d'un chantier
   ----------------------------------------------------------------------------
   Ouverte depuis un chantier : route « rentabilite »?id=XXX.
   Reprend la maquette de la professeure (Rentabilite_chantier.html) :
   ventes, coûts et marges par poste, uniquement hors taxes.
     Coût de revient HT = fournitures + prestations externes + main-d'œuvre
     Main-d'œuvre       = heures passées × coût horaire chargé (par employé)
     Marge HT           = vendu HT - coût de revient
   Démarre vide (montants à 0, aucun employé) en attendant les données de la base.
   ============================================================================ */
(function () {
  'use strict';

  const { $, esc } = BP;
  const siteId = new URLSearchParams(location.search).get('id');
  const site = siteId && BP.chantiers ? BP.chantiers.findSite(siteId) : null;
  if (!site) { $('#rent-missing').hidden = false; $('#rent-content').hidden = true; $('#print').hidden = true; $('#rent-back').hidden = true; $('#rent-crumb').textContent = 'Rentabilité'; return; }

  const KEY = 'batipilot_rentabilite_' + site.id;
  const suivi = BP.url('chantier_suivi') + '?id=' + encodeURIComponent(site.id);
  $('#rent-title').textContent = 'Rentabilité du chantier ' + site.reference;
  $('#rent-crumb').textContent = site.reference;
  $('#rent-crumb').href = suivi;
  $('#rent-back').href = suivi;
  document.title = 'Rentabilité ' + site.reference + ' | BatiPilot';

  /* Les 9 postes de la maquette de la prof */
  const POSTS = ['Structure', 'Plancher', 'Couverture', 'Escalier', 'Plâtrerie - Isolation', 'Plâtrerie - Cloison', 'Sols et murs', 'Électricité et chauffage', 'Plomberie'];
  const empty = () => POSTS.map(name => ({ name, sales: 0, supplies: 0, services: 0, employees: [] }));

  let saved = null;
  try { saved = JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) { /* ignore */ }
  let posts = Array.isArray(saved?.posts) && saved.posts.length === 9 ? saved.posts : empty();
  let info = saved?.info || { client: site.client?.name || '', city: site.house?.city || '', reference: site.general?.quoteRef || '' };
  const opened = new Set();

  const money = n => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n);
  const percent = n => n === null || !Number.isFinite(n) ? '-' : new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 1 }).format(n) + ' %';
  const safe = n => Math.max(0, Number(n) || 0);
  const calc = p => {
    const labor = p.employees.reduce((s, e) => s + safe(e.hours) * safe(e.rate), 0);
    const cost = safe(p.supplies) + safe(p.services) + labor;
    const margin = safe(p.sales) - cost;
    return { labor, cost, margin, rate: p.sales ? margin / p.sales * 100 : null, profit: cost ? margin / cost * 100 : null };
  };
  const tone = v => v < 0 ? 'text-danger' : 'text-success';
  const save = () => { try { localStorage.setItem(KEY, JSON.stringify({ posts, info })); } catch (e) { /* ignore */ } };

  function postHTML(p, i) {
    const c = calc(p), isOpen = opened.has(i);
    return '<article class="bp-acc' + (isOpen ? ' is-open' : '') + '">' +
      '<button type="button" class="bp-acc__head bp-rent-grid" data-toggle="' + i + '" aria-expanded="' + isOpen + '" aria-controls="detail-' + i + '">' +
        '<span class="bp-acc__title"><i class="fa-solid fa-chevron-right bp-acc__chevron" aria-hidden="true"></i>' + esc(p.name) + '</span>' +
        '<span class="bp-money" data-label="Vendu HT">' + money(p.sales) + '</span>' +
        '<span class="bp-money bp-rent-secondary" data-label="Fournitures">' + money(p.supplies) + '</span>' +
        '<span class="bp-money bp-rent-secondary" data-label="Prestations">' + money(p.services) + '</span>' +
        '<span class="bp-money bp-rent-secondary" data-label="Main-d’œuvre">' + money(c.labor) + '</span>' +
        '<span class="bp-money ' + tone(c.margin) + '" data-label="Marge HT">' + money(c.margin) + '</span>' +
        '<span class="bp-money ' + tone(c.margin) + '" data-label="Marge / ventes">' + percent(c.rate) + '</span>' +
      '</button>' +
      '<div class="bp-acc__body" id="detail-' + i + '"' + (isOpen ? '' : ' hidden') + '>' +
        '<div class="row g-3">' +
          '<div class="col-lg-6"><div class="bp-subcard h-100 mb-0"><h3 class="bp-subcard__title mb-3">Ventes et achats</h3>' +
            [['sales', 'Montant vendu HT (€)'], ['supplies', 'Fournitures HT (€)'], ['services', 'Prestations externes HT (€)']].map(([k, label]) =>
              '<div class="bp-field mb-3"><label for="' + k + '-' + i + '">' + label + '</label><input class="form-control" id="' + k + '-' + i + '" type="number" min="0" step="0.01" data-post="' + i + '" data-field="' + k + '" value="' + p[k] + '"></div>').join('') +
          '</div></div>' +
          '<div class="col-lg-6"><div class="bp-subcard h-100 mb-0"><h3 class="bp-subcard__title mb-2">Résultat du poste</h3>' +
            '<div class="bp-total-row"><span>Coût main-d’œuvre</span><strong>' + money(c.labor) + '</strong></div>' +
            '<div class="bp-total-row"><span>Coût de revient HT</span><strong>' + money(c.cost) + '</strong></div>' +
            '<div class="bp-total-row"><span>Marge HT</span><strong class="' + tone(c.margin) + '">' + money(c.margin) + '</strong></div>' +
            '<div class="bp-total-row"><span>Marge sur ventes</span><strong>' + percent(c.rate) + '</strong></div>' +
            '<div class="bp-total-row"><span>Rentabilité sur coût</span><strong>' + percent(c.profit) + '</strong></div>' +
          '</div></div>' +
        '</div>' +
        '<div class="bp-subcard mt-3 mb-0"><h3 class="bp-subcard__title mb-3">Employés affectés au poste</h3>' +
          '<div class="bp-table-wrap"><table class="bp-table bp-table--cards"><thead><tr><th>Employé</th><th>Heures passées</th><th>Coût horaire chargé (€)</th><th class="is-num">Coût total HT</th><th></th></tr></thead><tbody>' +
          (p.employees.map((e, j) => '<tr>' +
            '<td data-label="Employé"><input class="form-control" aria-label="Employé" placeholder="Nom de l’employé" data-post="' + i + '" data-employee="' + j + '" data-empfield="name" value="' + esc(e.name) + '"></td>' +
            '<td data-label="Heures"><input class="form-control" aria-label="Heures" type="number" min="0" step="0.25" data-post="' + i + '" data-employee="' + j + '" data-empfield="hours" value="' + e.hours + '"></td>' +
            '<td data-label="Coût horaire"><input class="form-control" aria-label="Coût horaire" type="number" min="0" step="0.01" data-post="' + i + '" data-employee="' + j + '" data-empfield="rate" value="' + e.rate + '"></td>' +
            '<td class="is-num" data-label="Coût total"><strong>' + money(safe(e.hours) * safe(e.rate)) + '</strong></td>' +
            '<td class="is-end"><button type="button" class="bp-link bp-link--danger" data-remove="' + i + '" data-employee="' + j + '">Retirer</button></td>' +
          '</tr>').join('') || '<tr><td colspan="5" class="bp-table__empty">Aucun employé affecté.</td></tr>') +
          '</tbody></table></div>' +
          '<div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mt-3"><button type="button" class="m-btn m-btn--ghost m-btn--sm" data-add="' + i + '"><i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter un employé</button><strong>Total : ' + money(c.labor) + '</strong></div>' +
        '</div>' +
      '</div></article>';
  }

  function render() {
    $('#posts').innerHTML = posts.map(postHTML).join('');
    let sales = 0, supplies = 0, services = 0, labor = 0, cost = 0, margin = 0;
    posts.forEach(p => { const c = calc(p); sales += safe(p.sales); supplies += safe(p.supplies); services += safe(p.services); labor += c.labor; cost += c.cost; margin += c.margin; });
    const set = (id, v) => { $('#' + id).textContent = v; };
    set('topSales', money(sales)); set('topCost', money(cost)); set('topMargin', money(margin)); set('topRate', percent(sales ? margin / sales * 100 : null));
    set('sSupplies', money(supplies)); set('sServices', money(services)); set('sLabor', money(labor)); set('sCost', money(cost)); set('sMargin', money(margin)); set('sProfit', percent(cost ? margin / cost * 100 : null));
    ['topMargin', 'sMargin'].forEach(id => { const e = $('#' + id); e.classList.toggle('text-danger', margin < 0); e.classList.toggle('text-success', margin >= 0); });
  }

  const root = $('#posts');
  root.addEventListener('click', e => {
    const toggle = e.target.closest('[data-toggle]');
    if (toggle) { const i = +toggle.dataset.toggle; opened.has(i) ? opened.delete(i) : opened.add(i); render(); return; }
    const add = e.target.closest('[data-add]');
    if (add) { const i = +add.dataset.add; posts[i].employees.push({ name: '', hours: 0, rate: 0 }); opened.add(i); save(); render(); return; }
    const remove = e.target.closest('[data-remove]');
    if (remove) { posts[+remove.dataset.remove].employees.splice(+remove.dataset.employee, 1); save(); render(); }
  });
  root.addEventListener('change', e => {
    const el = e.target;
    if (el.dataset.post === undefined) return;
    const i = +el.dataset.post;
    if (el.dataset.field) posts[i][el.dataset.field] = safe(el.value);
    else if (el.dataset.empfield) { const emp = posts[i].employees[+el.dataset.employee]; emp[el.dataset.empfield] = el.dataset.empfield === 'name' ? el.value : safe(el.value); }
    save();
    render();
  });
  ['client', 'city', 'reference'].forEach(id => {
    const el = $('#' + id);
    el.value = info[id] || '';
    el.addEventListener('input', () => { info[id] = el.value; save(); });
  });
  $('#expand').addEventListener('click', () => { posts.forEach((_, i) => opened.add(i)); render(); });
  $('#collapse').addEventListener('click', () => { opened.clear(); render(); });
  $('#print').addEventListener('click', () => window.print());

  render();
})();
