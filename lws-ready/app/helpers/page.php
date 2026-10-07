<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Constantes de chemins ─────────────────────────
// Définit les constantes de chemins utilisées dans toutes les pages du site

// Définit le chemin racine du projet si pas déjà défini
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
}

// Définit le chemin vers le répertoire des partials
if (!defined('PARTIALS')) {
    define('PARTIALS', APP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR);
}

// Définit le chemin vers le répertoire des helpers
if (!defined('HELPERS')) {
    define('HELPERS', APP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR);
}

// Définit l'URL de base du site pour la génération de liens
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}
