<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Constantes de chemins ─────────────────────────
// Définit les constantes de chemins utilisées dans toutes les pages du site

// Chemins racines (APP_ROOT, BASE_URL, WEB_ROOT)
require_once __DIR__ . '/../config/paths.php';

// Définit le chemin vers le répertoire des partials
if (!defined('PARTIALS')) {
    define('PARTIALS', APP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR);
}

// Définit le chemin vers le répertoire des helpers
if (!defined('HELPERS')) {
    define('HELPERS', APP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR);
}

