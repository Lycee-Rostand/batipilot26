# BatiPilot (design_batipilot)

Application de gestion BatiPilot (BTS SIO) pour l'entreprise « Combles & Vous ».
Projet **Symfony 7.4** (PHP 8.2 minimum, Twig, Doctrine, MariaDB).

Ce dépôt contient la **base commune** : le design (gabarits Twig, design system) et la navigation.
Chaque équipe y ajoute ses entités, ses CRUD et ses relations, en réutilisant le design.

Le code de l'équipe (branche `develop` de batipilot26) est intégré : entités, relations, formulaires et CRUD
(client, chantier, tâches de chantier, heures des employés, documents de devis, employé, poste, tâche,
statut de chantier, TVA, type de document), avec le design BatiPilot.

> Les pages Chantiers, Devis et Rentabilité du menu « Espace de travail » sont les maquettes de la prof :
> leurs données sont fictives (navigateur). Elles serviront de modèle pour brancher les vrais CRUD.

## Installation

```bash
composer install
# Base de données : copier la ligne DATABASE_URL de .env dans .env.local et y mettre vos identifiants
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve            # ou : php -S 127.0.0.1:8000 -t public
```

Ouvrir http://127.0.0.1:8000 → page de connexion.
Comptes de la maquette (mot de passe `demo`) : `responsable`, `admin`, `employe`.

Avec WAMP : MariaDB écoute sur le port **3307** (MySQL sur 3306). Utiliser PHP 8.2 ou plus (ex. `C:\wamp64\bin\php\php8.4.15\php.exe bin/console …`).

## Organisation

| Dossier | Contenu |
|---|---|
| `templates/base.html.twig` | Gabarit commun : en-tête, zone de contenu, pied de page (à venir), scripts |
| `templates/layout.html.twig` | **Gabarit de toutes les pages** : choisit le menu du profil connecté |
| `templates/espace_employe/base.html.twig` | Menu de l'employé (consultation seule) |
| `templates/responsable/base.html.twig` | Menu du responsable : tout sauf l'administration |
| `templates/admin/base.html.twig` | Hérite du responsable et ajoute les données référentielles |
| `config/routes/pages.yaml` | Routes des pages sans contrôleur (maquettes, pages à venir) |
| `src/` | Code de l'équipe : `Entity/`, `Repository/`, `Form/`, `Controller/` |
| `templates/<entité>/` | Pages CRUD (liste, fiche, nouveau, modifier) habillées avec le design |
| `templates/form/batipilot_theme.html.twig` | Thème de formulaire : tous les `form_row` prennent le style BatiPilot |
| `assets/scss/app/_batipilot.scss` | Le design system : toutes les classes `bp-…` |
| `public/` | CSS compilé, JS, images, polices, Bootstrap, Font Awesome, Chart.js |

**Référence du design : la page `/design-system`** (chaque composant avec son code à copier).

## Ajouter sa fonctionnalité (exemple : maisons)

Modèle à copier : `templates/poste/` (6 fichiers). Détail sur la page `/design-system`, section « CRUD Symfony ».

1. Créer l'entité et le CRUD : `php bin/console make:entity Maison` puis `php bin/console make:crud Maison`.
2. Ajouter le lien dans le menu : `responsable/base.html.twig` (responsable + admin par héritage) ou
   `admin/base.html.twig`, bloc `menu_admin` (admin seulement) :
   `{{ menu.link('app_maison_index', 'fa-solid fa-house', 'Maisons') }}`.
3. Si une page provisoire existait pour cette fonctionnalité, supprimer sa route dans `config/routes/pages.yaml` et son template.
4. Dans les templates générés : remplacer `{% extends 'base.html.twig' %}` par `{% extends 'layout.html.twig' %}`.
   La barre du haut et le menu sont alors ceux de la personne connectée, sur toutes les pages.
5. Habiller le HTML avec les classes du design system : tableau `bp-table`, formulaire `bp-form-grid`, boutons `m-btn`…

## Règles pour les équipes

1. Pas de CSS dans les pages : on utilise les classes du design system.
2. Un besoin qui n'existe pas ? Le demander au responsable design, qui ajoute la classe et son exemple.
3. Textes en français. Champ obligatoire = astérisque rouge.
4. Chaque page doit rester utilisable sur téléphone et tablette.

## Modifier le design (responsable design)

Les sources SCSS sont dans `assets/scss/`. Après modification :

```bash
npm install          # une seule fois
npm run build:css    # ou npm run watch:css pendant le travail
```

## Crédits

Base graphique : template [CoolAdmin](https://github.com/puikinsh/CoolAdmin) de Colorlib, licence MIT (voir `LICENSE.md`).
