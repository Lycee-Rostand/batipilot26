# BatiPilot : Guide Git / GitHub

> Lycée Jean Rostand - Caen - BTS SIO 2026-2027
> Projet : BatiPilot (application web Symfony)

## Historique des modifications

| Version | Date | Auteur | Modification |
|---|---|---|---|
| 1.0 | 11/11/2026 | Joackim | Création du document |

---

## 1. Structure des branches

Nous utilisons un **Git Flow simplifié**.

| Branche | Rôle | Règle |
|---|---|---|
| `main` | Version de production (déployée sur le serveur du lycée) | Aucun commit direct. Mise à jour uniquement en fin de sprint |
| `develop` | Intégration de tout ce qui est terminé | Uniquement via Pull Request |
| `feature/...` | Une user story = une branche | Créée depuis `develop`, supprimée après merge |

### Nommage des feature branches

```
feature/<equipe>/<US>-<description-courte>
```

Équipes : `devis`, `kpi`, `clients`, `chantiers`, `bdd`, `design`.

Exemples :

```
feature/devis/US12-creation-devis
feature/kpi/US03-dashboard-ca
feature/clients/US07-crud-maison
feature/chantiers/US05-login-authentification
feature/bdd/entites-metier
```

Règles : minuscules, tirets, pas d'espaces ni d'accents.

---

## 2. Convention de commits

Format (Conventional Commits) :

```
type(portée): description courte
```

| Type | Usage |
|---|---|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction de bug |
| `docs` | Documentation |
| `style` | Mise en forme, sans changement de logique |
| `refactor` | Réécriture sans changer le comportement |
| `test` | Ajout ou modification de tests |
| `chore` | Configuration, dépendances, migrations |

Exemples :

```
feat(devis): ajoute la création d'un devis avec lignes
fix(auth): corrige la redirection après connexion
chore(bdd): ajoute la migration pour la table Etape
docs(devis): met à jour le plan de tests
```

Règles :

- Un commit = une modification logique (pas de message « modifs » ou « tout »)
- Commits petits et fréquents
- Ne jamais commiter de code qui ne s'exécute pas

---

## 3. Workflow d'une Pull Request

1. Se mettre à jour : `git checkout develop && git pull`
2. Créer sa branche : `git checkout -b feature/devis/US12-creation-devis`
3. Coder et commiter régulièrement
4. Se resynchroniser avant de pousser : `git fetch && git rebase origin/develop` (ou `git merge origin/develop`)
5. Pousser : `git push -u origin feature/devis/US12-creation-devis`
7. **Joackim s'occupe de la pull request sinon n membre de la même équipe relit et approuve** (jamais l'auteur)
8. Merge, puis suppression de la branche

---

## 4. Spécificités Symfony

Le fichier `.env.dev` contient les identifiants votre base de données : **il ne doit jamais être commité**.

### Migrations Doctrine

Risque principal de conflit entre équipes.

- Seul le responsable BDD (Ali) ou un référent par équipe génère les migrations
- Toujours faire `git pull` sur `develop` avant de générer une migration
- Une migration = un fichier = un commit `chore(bdd): ...`
- **Ne jamais modifier une migration déjà mergée** : en créer une nouvelle

### `composer.lock`

En cas de conflit, ne pas le fusionner à la main : garder une des deux versions puis lancer `composer install`. Toute nouvelle dépendance doit être annoncée à l'équipe.