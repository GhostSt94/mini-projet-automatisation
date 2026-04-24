# Rapport — Mini-Projet DevOps

**Sujet** : Automatisation du déploiement d'une application web
**Application** : FormaPro — plateforme de listing de formations
**Stack** : Laravel 10 + MySQL 8 + Docker + GitHub Actions
**Auteur** : _[à compléter]_
**Date** : Avril 2026

---

## 1. Description de l'architecture

### 1.1 Vue d'ensemble

L'application **FormaPro** est une application web légère développée avec le framework
PHP **Laravel 10**. Elle expose quatre routes :

- `GET /` — page d'accueil présentant la plateforme ;
- `GET /formations` — liste dynamique des formations stockées en base de données ;
- `GET /formations/create` — formulaire d'ajout d'une formation (titre, description, durée, niveau) ;
- `POST /formations` — enregistrement d'une formation avec validation serveur (champs requis, titre unique, niveau dans la liste `Débutant|Intermédiaire|Avancé`) et message flash de confirmation.

L'ensemble est conteneurisé et composé de **deux services** orchestrés par Docker Compose :

```
                    ┌──────────────────────────────────────────┐
                    │              Docker host                 │
                    │                                          │
  ┌─────────┐  HTTP │   ┌─────────────────┐   TCP  ┌────────┐  │
  │ Browser │─────────▶│  Service "app"  │───────▶│   db   │  │
  │         │  :8000 │  │ Laravel 10      │  :3306 │ MySQL8 │  │
  └─────────┘        │  │ PHP 8.2 + serve │        │        │  │
                    │   └─────────────────┘        └────────┘  │
                    │        network default (bridge)          │
                    │                                          │
                    │   volume : mysql_data (persistance DB)   │
                    └──────────────────────────────────────────┘
```

### 1.2 Composants

| Composant | Rôle | Image / Base |
|-----------|------|--------------|
| **app** | Sert l'application Laravel via `php artisan serve` (port 8000) | `php:8.2-cli` + composer + extensions (`pdo_mysql`, `mbstring`, `zip`, `bcmath`) |
| **db** | Stocke les formations dans la table `formations` | `mysql:8.0` avec healthcheck `mysqladmin ping` |
| **mysql_data** | Volume Docker pour la persistance des données | Volume nommé |

### 1.3 Flux applicatif

1. Le navigateur envoie une requête HTTP sur `http://localhost:8000/formations`.
2. Le port `8000` du conteneur `app` est mappé sur l'hôte.
3. Laravel route la requête vers `FormationController@index`.
4. Le contrôleur interroge le modèle Eloquent `Formation`, qui établit une connexion
   TCP avec MySQL sur le nom de service `db:3306` (résolution DNS interne à Docker).
5. Les données sont rendues dans la vue Blade `formations.index` (layout commun, Tailwind via CDN).

---

## 2. Étapes d'automatisation

### 2.1 Bootstrap sans installation globale

Aucune dépendance PHP/Composer n'est installée sur la machine hôte. Le squelette Laravel
a été généré via un **conteneur jetable** :

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 \
  create-project laravel/laravel:^10.0 .
```

### 2.2 Conteneurisation — Dockerfile

Le [`Dockerfile`](../Dockerfile) construit une image mono-processus :

1. Base `php:8.2-cli` + installation des extensions requises (`pdo_mysql`, `mbstring`, `zip`, `bcmath`) ;
2. Composer copié depuis l'image officielle `composer:2` ;
3. `composer install --no-dev --optimize-autoloader` exécuté dans l'image ;
4. Script `docker/entrypoint.sh` copié et rendu exécutable ;
5. `ENTRYPOINT` = l'entrypoint, `CMD` = `php artisan serve --host=0.0.0.0 --port=8000`.

**Optimisations** :

- Copie séparée de `composer.json` / `composer.lock` avant le reste du code pour exploiter le cache Docker.
- Image finale basée sur `php:8.2-cli` (plus légère que `php:8.2-fpm` + Nginx pour ce use-case).
- Fichier `.dockerignore` pour exclure `vendor/`, `.git`, `tests/`, etc.

### 2.3 Entrypoint intelligent

Le script [`docker/entrypoint.sh`](../docker/entrypoint.sh) rend le premier démarrage
idempotent :

1. Attente de MySQL via `mysqladmin ping` ;
2. Génération de `APP_KEY` si absente ;
3. `php artisan migrate --force --seed` ;
4. `php artisan config:cache && route:cache` ;
5. `exec "$@"` (démarre la commande du `CMD`).

### 2.4 Orchestration — Docker Compose

Le fichier [`docker-compose.yml`](../docker-compose.yml) déclare les deux services et une
**healthcheck MySQL** consommée par `depends_on: condition: service_healthy`. Cela garantit
que `app` ne démarre pas avant que la base ne réponde aux `ping`.

Commandes :

```bash
docker compose up -d --build   # build + lancement
docker compose logs -f app     # logs
docker compose down -v         # arrêt + purge volume
```

### 2.5 Pipeline CI/CD — GitHub Actions

Le workflow [`.github/workflows/ci.yml`](../.github/workflows/ci.yml) enchaîne
**trois jobs séquentiels** :

1. **test** — Setup PHP 8.2, `composer install`, exécution de `php artisan test` sur une base SQLite éphémère (pas besoin de conteneur MySQL dans le runner).
2. **docker-build** — Build de l'image avec `docker/build-push-action` + cache GHA, puis sanity check (`php artisan --version` dans le conteneur).
3. **deploy-stack** — `docker compose up -d --build` dans le runner, attente de disponibilité HTTP et smoke test sur `/` et `/formations` avec `curl | grep`.

Le pipeline se déclenche sur `push` et `pull_request` des branches `main`/`master`,
ainsi que manuellement via `workflow_dispatch`.

---

## 3. Difficultés rencontrées

### 3.1 Chemins Windows et Git Bash

Le bootstrap via `docker run -v "$PWD":/app` a échoué sous Git Bash car MSYS
convertit `/app` en `C:/Program Files/Git/app`. Solution : utiliser `MSYS_NO_PATHCONV=1`
et `$(pwd -W)` pour obtenir le chemin Windows natif, puis `//app` pour empêcher
la conversion du chemin cible.

### 3.2 `composer.lock` incompatible PHP 8.2

L'image officielle `composer:2` embarque PHP 8.4, ce qui a généré un lock
avec `symfony/css-selector v8.0.8` (exigeant PHP ≥ 8.4). Le build dans `php:8.2-cli`
plantait donc avec `Your lock file does not contain a compatible set of packages`.
Solution : ajouter `config.platform.php = "8.2"` dans `composer.json` puis relancer
`composer update` pour régénérer un lock compatible (`css-selector` rétrogradé en v7.4.8).

### 3.3 Ordre de démarrage MySQL / Laravel

Au premier run, Laravel tentait de migrer alors que MySQL n'avait pas fini son
initialisation interne. Deux niveaux de protection ont été mis en place :

- **Healthcheck** `mysqladmin ping` dans `docker-compose.yml` avec
  `depends_on: condition: service_healthy`.
- **Boucle d'attente PHP** (`PDO::__construct`) dans `entrypoint.sh`.
  Le `mysqladmin` de Debian (MariaDB) ne gérait pas correctement le TLS de MySQL 8
  et renvoyait `self-signed certificate in certificate chain` ; le client PDO PHP,
  lui, fonctionne sans configuration TLS particulière.

### 3.4 `APP_KEY` écrasée par l'environnement

Première version du compose utilisait `env_file: .env`, qui injectait `APP_KEY=`
(vide, car pas encore générée) dans l'environnement du processus PHP. Laravel lit
`env()` depuis `$_ENV` avant le fichier `.env`, donc même après `php artisan key:generate`
la clé était ignorée → **500 "No application encryption key has been specified"**.

Solution : supprimer `env_file:` et déclarer les variables utiles directement dans
`environment:` (sans `APP_KEY`), laisser l'entrypoint générer `APP_KEY` dans le
fichier `.env` interne au conteneur.

### 3.5 Permissions `storage/` et `bootstrap/cache/`

Laravel écrit logs, cache de config et cache de vues dans ces dossiers. Le Dockerfile
applique `chmod -R 775 storage bootstrap/cache` après `composer install` pour éviter
les erreurs `Permission denied` lors du démarrage.

### 3.6 Tests CI sans MySQL

Pour garder le job `test` rapide et isolé, la configuration est réécrite en SQLite
(`sed` sur `.env`) dans le runner. Le vrai test d'intégration avec MySQL se fait dans
le job `deploy-stack` via `docker compose`.

---

## 4. Bilan

| Livrable | Statut |
|----------|--------|
| Application fonctionnelle (accueil + liste formations) | OK |
| Dockerfile + build | OK |
| docker-compose.yml (app + mysql) | OK |
| Pipeline CI/CD (3 jobs : test, build, integration) | OK |
| Rapport | Ce document |

**Points forts** :

- Zéro installation globale : tout passe par des conteneurs.
- Premier démarrage "one-shot" : `cp .env.example .env && docker compose up -d --build` suffit.
- Pipeline à 3 étages qui valide code → image → stack complète.

**Améliorations possibles** :

- Remplacer `php artisan serve` par Nginx + PHP-FPM pour un déploiement proche de la production.
- Ajouter un reverse-proxy Traefik avec HTTPS automatique (Let's Encrypt).
- Publier l'image sur Docker Hub / GHCR depuis le pipeline (`docker/login-action`).
- Ajouter des tests Feature (`php artisan make:test`) qui vérifient la présence des formations seedées.
