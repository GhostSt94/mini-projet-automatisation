# Mini-Projet DevOps — FormaPro

Application web Laravel 10 listant des formations, conteneurisée avec Docker,
orchestrée avec Docker Compose et automatisée via GitHub Actions.

## Stack technique

- **Laravel 10** + **PHP 8.2**
- **MySQL 8.0**
- **Docker** + **Docker Compose**
- **GitHub Actions** (CI/CD)

## Prérequis

- Docker Desktop (ou Docker Engine + Docker Compose v2)
- Rien d'autre : aucune dépendance n'est installée globalement sur la machine hôte.

## Démarrage rapide

```bash
# 1. Copier le fichier d'environnement
cp .env.example .env

# 2. Construire et lancer la stack
docker compose up -d --build

# 3. Ouvrir l'application
#    http://localhost:8000/            -> page d'accueil
#    http://localhost:8000/formations  -> liste des formations
```

Au premier démarrage, l'entrypoint :

1. Attend que MySQL soit prêt (healthcheck),
2. Génère la `APP_KEY` si absente,
3. Exécute les migrations et seeders,
4. Démarre le serveur PHP built-in sur le port 8000.

## Commandes utiles

```bash
# Logs
docker compose logs -f app

# Shell dans le conteneur app
docker compose exec app bash

# Re-seed la base
docker compose exec app php artisan migrate:fresh --seed

# Arrêter et nettoyer
docker compose down        # garde le volume MySQL
docker compose down -v     # supprime aussi le volume (reset complet)
```

## Structure du projet

```
mini-projet/
├── app/Http/Controllers/FormationController.php
├── app/Models/Formation.php
├── database/migrations/ ...create_formations_table.php
├── database/seeders/FormationSeeder.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── home.blade.php
│   └── formations/index.blade.php
├── routes/web.php
├── docker/entrypoint.sh
├── Dockerfile
├── docker-compose.yml
├── .github/workflows/ci.yml
└── docs/
    ├── enonce_mini_projet_automatisation (1).pdf
    └── rapport.md
```

## Pipeline CI/CD

Le workflow [`.github/workflows/ci.yml`](.github/workflows/ci.yml) enchaîne :

1. **test** — installe les dépendances Composer, migre une base SQLite et exécute `php artisan test`. Tourne sur `push` et `pull_request`.
2. **build-push** — construit l'image Docker et la pousse sur Docker Hub (`<user>/formation-app:latest`). Ne tourne **que sur `push` vers `main`**.
3. **deploy** — copie [`docker-compose.prod.yml`](docker-compose.prod.yml) sur le VPS via SCP, puis lance `docker compose pull && up -d` via SSH. Termine par un smoke test HTTP sur le VPS. Ne tourne **que sur `push` vers `main`**.

### Configuration des secrets GitHub

Pour que les jobs `build-push` et `deploy` fonctionnent, configurer dans
**Settings → Secrets and variables → Actions** :

| Secret | Contenu |
|---|---|
| `DOCKERHUB_USERNAME` | nom d'utilisateur Docker Hub |
| `DOCKERHUB_TOKEN` | Personal Access Token (créer sur https://hub.docker.com/settings/security) |
| `VPS_HOST` | IP ou domaine du VPS |
| `VPS_USER` | utilisateur SSH du VPS |
| `VPS_PASSWORD` | mot de passe SSH du VPS |

### Préparation du VPS (one-shot)

```bash
ssh user@vps-host

# 1. Vérifier que SSH par password est autorisé
sudo sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config
sudo systemctl restart ssh

# 2. Créer le répertoire de déploiement
mkdir -p ~/formapro

# 3. Ouvrir le port 8000 (si firewall actif)
sudo ufw allow 8000/tcp

# 4. Vérifier Docker
docker --version && docker compose version
```

Après le premier `git push` sur `main` :
- L'image apparaît sur `https://hub.docker.com/r/<user>/formation-app/tags`
- L'application est accessible sur `http://<vps-host>:8000/`

## Rapport

Voir [`docs/rapport.md`](docs/rapport.md) pour la description de l'architecture,
les étapes d'automatisation et les difficultés rencontrées.
