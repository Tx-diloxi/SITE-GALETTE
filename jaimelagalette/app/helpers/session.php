<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// Initialise la session PHP pour les pages publiques et génère un token CSRF
function init_page_session(): void
{
    // Vérifie si la session n'est pas déjà démarrée et que les en-têtes n'ont pas été envoyés
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    // Génère un token CSRF sécurisé s'il n'existe pas déjà
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
