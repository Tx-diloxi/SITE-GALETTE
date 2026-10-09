<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Constantes de chemins partagées ───────────────
// Source unique pour APP_ROOT, BASE_URL et WEB_ROOT (config admin, commercial et pages publiques)

// Chemin racine du projet
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
}

// URL de base du site pour la génération de liens
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

// Racine web (avec séparateur final) : htdocs/ sur LWS, public/ en local
if (!defined('WEB_ROOT')) {
    define('WEB_ROOT', APP_ROOT . (is_dir(APP_ROOT . 'htdocs') ? 'htdocs' : 'public') . DIRECTORY_SEPARATOR);
}

// URL publique du site (sans slash final) : canonical, Open Graph, sitemap et données structurées.
// À adapter au domaine définitif, ou à surcharger avec la variable d'environnement SITE_URL.
if (!defined('SITE_URL')) {
    define('SITE_URL', rtrim((string)(getenv('SITE_URL') ?: 'https://www.jaimelagalette.com'), '/'));
}
