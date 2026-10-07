# J'aime la Galette — Site vitrine

Site vitrine du groupe **J'aime la Galette**, producteur artisanal de crêpes et galettes bretonnes, et de sa marque **Be Good'n**.

## Stack

- **PHP 8.x procédural** — pas de framework, pas de MVC
- **MySQL 8.0** (InnoDB, utf8mb4)
- **HTML/CSS/JS vanilla** — SCSS compilé en CSS
- **Docker** (PHP 8.2-Apache + MySQL + phpMyAdmin)
- **PHPMailer** pour l'envoi d'emails

## Structure

```
jaimelagalette/
├── config.php              # Connexion PDO
├── public/                 # Webroot, front controller, assets, admin, API
├── app/
│   ├── config/             # Constantes, BDD, admin, mail
│   ├── helpers/            # Forms, mailer, horaires, page
│   └── partials/           # 29 sections HTML incluses par les pages
├── pages/                  # 12 pages métier (home, le-groupe, nos-produits…)
├── docker/                 # Dockerfile + SQL (schema + seeds)
└── maquette/               # Mockups de référence
```

## Démarrage

```bash
docker compose up -d
```

Le site est accessible sur `http://localhost:8080`, phpMyAdmin sur `http://localhost:8081`.

## Backoffice

`/admin` — identifiants par défaut : `admin` / `admin123`.

## Pages

| URL | Contenu |
|---|---|
| `/` | Accueil |
| `/le-groupe` | Le Groupe |
| `/nos-produits` | Nos produits |
| `/nos-produits/jaime-la-galette` | Marque J'aime la Galette |
| `/nos-produits/be-goodn` | Marque Be Good'n |
| `/savoir-faire` | Savoir-faire |
| `/rse` | Engagements RSE |
| `/recrutement` | Recrutement |
| `/contact` | Contact |
| `/faq` | FAQ + Chatbot |
