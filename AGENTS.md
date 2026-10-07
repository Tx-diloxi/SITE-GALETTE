# AGENTS.md

## Purpose
This file helps AI coding agents work effectively in the "J'aime la Galette" project.

## Key points
- The project is a **PHP/MySQL site** built without a framework.
- It uses **procedural PHP + PDO** and a **front controller pattern** with `jaimelagalette/public/index.php`.
- Pages are composed by including PHP partials from `jaimelagalette/app/partials/`.
- The site must remain compatible with a **standard mutualisé PHP/MySQL hosting** environment.
- Frontend is **HTML/CSS/JS vanilla**. SCSS is used for source styles in `jaimelagalette/public/assets/css/index/style.scss`.
- Routes are handled with `.htaccess` and `$_GET['url']`; refer to `jaimelagalette/public/index.php` for route mapping.

## Project structure
- `jaimelagalette/public/` — webroot, front controller, assets, API endpoints.
- `jaimelagalette/app/config/` — config, database connection.
- `jaimelagalette/app/helpers/` — reusable functions for pages, products, FAQ, jobs, forms.
- `jaimelagalette/app/partials/` — page sections included by `index.php`.
- `docker/` and `docker-compose.yml` — local dev environment with PHP/Apache, MySQL, phpMyAdmin.
- `maquette/` — existing page/content mockups and copy directions.

## Important conventions
- Do not introduce frameworks or package managers unless there is a clear project requirement.
- Prefer small, procedural functions and includes over object-oriented architecture.
- Keep generated code compatible with PHP 8.x and shared hosting restrictions.
- Avoid assumptions apropos advanced server features; use standard Apache/PHP behavior.

## Local development
- Use `docker compose up -d` and `docker compose down -v` from the repository root.
- Database schema and sample data are in `docker/sql/01_schema.sql` and `docker/sql/02_seeds.sql`.
- `jaimelagalette/public/api/` contains API endpoints for dynamic features (FAQ search, product category data).

## Notes for AI agents
- Use `context.md` as the primary source for architecture, goals, and functional expectations.
- When editing or creating PHP files, preserve the pattern of loading data first and then including partials.
- When updating copy/content, prefer French and maintain the site's brand tone.
- Do not change the public document root; the Apache config in `docker/php/Dockerfile` already points to `public/`.

## Useful files
- `context.md` — project context and architecture description.
- `jaimelagalette/public/index.php` — routing/front controller.
- `jaimelagalette/app/helpers/` — core business logic functions.
- `jaimelagalette/app/partials/` — HTML sections used across pages.
- `jaimelagalette/public/assets/css/index/style.scss` — SCSS source for styles.
- `docker-compose.yml` — local stack and service definitions.
