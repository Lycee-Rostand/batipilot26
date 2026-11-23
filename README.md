<div align="center">

# 🏗️ BatiPilot

**Application web de gestion de chantiers, de devis et d'indicateurs pour le bâtiment**

![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Doctrine](https://img.shields.io/badge/Doctrine_ORM-FC6A31?style=for-the-badge)
![Git](https://img.shields.io/badge/Git-F05032?style=for-the-badge&logo=git&logoColor=white)
![Scrum](https://img.shields.io/badge/Méthode-Scrum-2ea44f?style=for-the-badge)

*Projet de BTS SIO option SLAM, 2ème année, réalisé en équipe*
*Lycée Jean Rostand - Caen - BTS SIO 2026-2027*

</div>

---

> ## ⚠️ IMPÉRATIF : lire le [CONTRIBUTING.md](./CONTRIBUTING.md) avant de toucher au code
> Il explique comment nommer les branches, écrire les commits, ouvrir une Pull Request et gérer les migrations. **Aucun push direct sur `main` ni `develop`.**

---

## 📑 Sommaire

1. [À propos](#-à-propos)
2. [Fonctionnalités](#-fonctionnalités)
3. [L'équipe](#-léquipe)
4. [Prérequis](#-prérequis)
5. [Installation](#-installation)
6. [Configuration de l'environnement](#-configuration-de-lenvironnement)
7. [Workflow Git en bref](#-workflow-git-en-bref)
8. [Aide-mémoire des commandes](#-aide-mémoire-des-commandes)
9. [Organisation du dépôt](#-organisation-du-dépôt)

---

## 📖 À propos

**BatiPilot** est une application web développée avec le framework **Symfony**, dans le cadre du projet de 2ème année du BTS SIO (option SLAM). Elle permet à une entreprise du bâtiment de gérer ses clients, ses devis, ses chantiers et ses indicateurs de performance.

Le projet est mené par **16 étudiants répartis en 4 équipes**, selon la méthode agile **Scrum** : sprints, mêlées en début de séance, revues de sprint avec le client (l'enseignante).

## ✨ Fonctionnalités

Le projet est découpé en quatre modules, un par équipe.

| Module | Contenu |
|---|---|
| 🧾 **Devis / factures** | Création et gestion des devis |
| 📊 **KPI** | Indicateurs de suivi et tableaux de bord |
| 🏠 **Maisons / clients / paramètres** | Gestion des clients, des maisons, des postes, des étapes et des paramètres |
| 🚧 **Suivi des chantiers / authentification** | Suivi de l'avancement des chantiers, connexion et gestion des accès |

## 👥 L'équipe

| Équipe | Membres |
|---|---|
| 🧾 Devis / factures | Joackim, Mathis, Xander, Alan |
| 📊 KPI | Nathan, Arda, Elouan, Gabin H. |
| 🏠 Maisons / clients / paramètres | Jules, Yanis, Ali, Gabin D. |
| 🚧 Suivi des chantiers / authentification | Baptiste, Quentin, Robin, Candace |

**Rôles transverses**

| Rôle | Responsable |
|---|---|
| Git / GitHub | Joackim |
| Scrum Master | Nathan |
| Diagramme de classes, MCD / MLD, base de données | Ali |
| Ergonomie, IHM, design | Robin |

**Client :** Z. Annouche (enseignante)

## 🧰 Prérequis

À installer sur votre poste avant de commencer :

- [Git](https://git-scm.com/)
- [PHP](https://www.php.net/) (version exigée par le projet : voir `composer.json`)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)
- [Wampserver](https://wampserver.aviatechno.net/) ou autre serveur de base de données (celui utilisé par le projet : voir la ligne `DATABASE_URL` du fichier `.env`)

Vérification rapide :

```bash
git --version
php --version
composer --version
symfony version
```

## 🚀 Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Lycee-Rostand/batipilot26.git
cd batipilot26

# 2. Installer les dépendances PHP
composer install

# 3. Créer son fichier de configuration locale
cp .env .env.local
#    puis décommenter la ligne DATABASE_URL et renseigner vos infos locales

# 4. Appliquer les migrations (création des tables)
php bin/console doctrine:migrations:migrate

# 5. Lancer le serveur de développement
symfony server:start
```

L'application est ensuite accessible sur l'adresse affichée par Symfony (en général `https://127.0.0.1:8000`).

## ⚙️ Configuration de l'environnement

Chaque machine possède son propre fichier **`.env.local`**.

| Fichier | Versionné ? | Rôle |
|---|---|---|
| `.env` | ✅ Oui | Valeurs d'exemple, partagées par tous |
| `.env.local` | ❌ **Non, jamais** | Vos vrais identifiants, propres à votre machine |

Exemple de contenu pour votre poste :

```dotenv
APP_ENV=dev
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/batipilot"
```

> 🚨 Ne commitez **jamais** `.env.local` et ne mettez **jamais** les identifiants de la base de production dans le dépôt. Ils sont uniquement présents sur le serveur de production.

Avant de commiter, vérifiez avec `git status` que `.env.local` n'apparaît pas.

## 🌿 Workflow Git en bref

| Branche | Rôle |
|---|---|
| `main` | Version de production, mise à jour uniquement en fin de sprint |
| `develop` | Intégration de tout ce qui est terminé |
| `feature/<equipe>/<US>-<description>` | Une user story = une branche |

**Format des commits :**

```
type(portée): description courte
```

Exemples : `feat(devis): ajoute la création d'un devis`, `fix(auth): corrige la redirection après connexion`, `chore(bdd): ajoute la migration Etape`.

**Les étapes d'une contribution :**

1. `git checkout develop && git pull`
2. `git checkout -b feature/devis/US12-creation-devis`
3. Coder et commiter
4. `git push -u origin feature/devis/US12-creation-devis`
5. Ouvrir une Pull Request vers `develop`, relue par un membre de la même équipe

👉 Tous les détails sont dans le [CONTRIBUTING.md](./CONTRIBUTING.md).

## 🧭 Aide-mémoire des commandes

| Besoin | Commande |
|---|---|
| Voir l'état | `git status` |
| Voir l'historique | `git log --oneline --graph --all` |
| Créer et changer de branche | `git checkout -b <nom>` |
| Ajouter des fichiers | `git add <fichier>` |
| Commiter | `git commit -m "feat(devis): ..."` |
| Mettre de côté | `git stash` / `git stash pop` |
| Annuler le dernier commit (garder les modifs) | `git reset --soft HEAD~1` |
| Récupérer sans fusionner | `git fetch` |
| Supprimer une branche locale | `git branch -d <nom>` |

**Commandes Symfony utiles**

| Besoin | Commande |
|---|---|
| Lancer le serveur | `symfony server:start` |
| Vider le cache | `php bin/console cache:clear` |
| Appliquer les migrations | `php bin/console doctrine:migrations:migrate` |
| Voir l'état des migrations | `php bin/console doctrine:migrations:status` |
| Lister les routes | `php bin/console debug:router` |

## 🗂️ Organisation du dépôt

Structure standard d'un projet Symfony :

```
batipilot26/
├── config/          # Configuration de l'application
├── docs/            # Documentation et livrables du projet
├── migrations/      # Migrations Doctrine (schéma de la base)
├── public/          # Point d'entrée web, CSS, JS, images
├── src/             # Code PHP (contrôleurs, entités, repositories)
├── templates/       # Vues Twig
├── tests/           # Tests
├── .env             # Configuration d'exemple (versionnée)
├── CONTRIBUTING.md  # Règles de contribution (à lire !)
└── README.md        # Ce fichier
```

---

<div align="center">

**Lycée Jean Rostand - Caen - BTS SIO 2027**
*Projet BatiPilot, ADP Année 2 SLAM*

</div>
