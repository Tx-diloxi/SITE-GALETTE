<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Administration du site ────────────────────────

// Définit le chemin racine du projet si pas déjà défini
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
}
// Définit l'URL de base du site si pas déjà défini
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

// Identifiant administrateur depuis l'environnement ou valeur par défaut (dev local)
define('ADMIN_USER', getenv('ADMIN_USER') ?: 'la-galette-admin');
// Hash du mot de passe administrateur depuis l'environnement ou valeur par défaut (dev local)
define('ADMIN_PASS_HASH', getenv('ADMIN_PASS_HASH') ?: password_hash('9?UqSACao3UcG#g!', PASSWORD_BCRYPT));
// Nom de la session administrateur
define('ADMIN_SESSION_NAME', 'jalg_admin');
// Durée de vie de la session admin (2 heures en secondes)
define('ADMIN_SESSION_LIFETIME', 7200);
// Répertoire de destination pour les uploads d'images admin
define('ADMIN_UPLOAD_DIR', APP_ROOT . 'htdocs' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'admin-uploads' . DIRECTORY_SEPARATOR);
// URL de base pour accéder aux fichiers uploadés
define('ADMIN_UPLOAD_URL', BASE_URL . 'assets/images/admin-uploads/');
// Taille maximale des fichiers uploadés (5 Mo)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Vérifie que l'utilisateur est authentifié en tant qu'administrateur
function admin_check_auth(): void {
    // Démarre la bufferisation de sortie si pas déjà fait
    if (ob_get_level() === 0) {
        ob_start();
    }
    // Définit le nom de session admin
    session_name(ADMIN_SESSION_NAME);
    // Démarre la session si pas déjà active
    if (session_status() === PHP_SESSION_NONE) {
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
    // Vérifie si l'utilisateur est connecté avec le rôle admin
    if (empty($_SESSION['auth_logged_in']) || $_SESSION['auth_role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'admin/login.php');
        exit;
    }
    // Vérifie le timeout de session
    auth_check_timeout();
}

// Génère un token CSRF sécurisé pour les formulaires admin
function admin_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifie la validité du token CSRF soumis
function admin_csrf_verify(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Upload une image dans le répertoire admin avec validation des types MIME
function admin_upload_image(array $file): ?string {
    // Liste des types MIME autorisés pour les images
    // NOTE : SVG volontairement exclu pour prévenir les risques XSS liés au script embarqué
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

    // Vérifie qu'il n'y a pas d'erreur d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    // Vérifie la taille du fichier
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;

    // Vérifie le type MIME réel du fichier (pas seulement l'extension)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Vérifie que le type MIME est autorisé
    if (!in_array($mime, $allowed_mimes, true)) return null;

    // Détermine l'extension de fichier à partir du type MIME réel
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => null,
    };
    if (!$ext) return null;

    // Crée le répertoire d'upload s'il n'existe pas
    if (!is_dir(ADMIN_UPLOAD_DIR)) {
        @mkdir(ADMIN_UPLOAD_DIR, 0777, true);
    }
    // Vérifie que le répertoire est accessible en écriture
    if (!is_dir(ADMIN_UPLOAD_DIR) || !is_writable(ADMIN_UPLOAD_DIR)) {
        return null;
    }

    // Génère un nom de fichier unique
    $filename = uniqid('admin_') . '.' . $ext;
    $dest = ADMIN_UPLOAD_DIR . $filename;
    // Déplace le fichier uploadé vers sa destination finale
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    // Définit les permissions de lecture pour le fichier
    chmod($dest, 0644);

    // Retourne l'URL publique du fichier uploadé
    return ADMIN_UPLOAD_URL . $filename;
}

// Redirige l'utilisateur vers un chemin de l'interface admin
function admin_redirect(string $path): void {
    // Vide le buffer de sortie si actif
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

// Génère une alerte HTML stylisée pour les retours d'interface admin
function admin_alert(string $message, string $type = 'success'): string {
    return '<div class="admin-alert admin-alert--' . $type . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
}

// Vérifie si la session admin n'a pas expiré (timeout d'inactivité)
function auth_check_timeout(): void {
    if (isset($_SESSION['auth_last_activity']) && (time() - $_SESSION['auth_last_activity'] > ADMIN_SESSION_LIFETIME)) {
        session_destroy();
        header('Location: ' . BASE_URL . 'admin/login.php?expired=1');
        exit;
    }
    $_SESSION['auth_last_activity'] = time();
}

// Démarre la session en utilisant le nom de session admin
function auth_start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(ADMIN_SESSION_NAME);
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
}

// Vérifie que l'utilisateur a un rôle spécifique et redirige si nécessaire
function auth_check(string $role): void {
    auth_start_session();
    if (empty($_SESSION['auth_logged_in'])) {
        header('Location: ' . BASE_URL . 'admin/login.php');
        exit;
    }
    if ($_SESSION['auth_role'] !== $role) {
        if ($_SESSION['auth_role'] === 'admin') {
            header('Location: ' . BASE_URL . 'admin/index.php');
        } else {
            header('Location: ' . BASE_URL . 'admin/commercial/visite-mystere/index.php');
        }
        exit;
    }
    auth_check_timeout();
}

// Vérifie si l'utilisateur connecté est un administrateur
function auth_is_admin(): bool {
    auth_start_session();
    return !empty($_SESSION['auth_logged_in']) && $_SESSION['auth_role'] === 'admin';
}

// Vérifie si l'utilisateur connecté est un commercial
function auth_is_commercial(): bool {
    auth_start_session();
    return !empty($_SESSION['auth_logged_in']) && $_SESSION['auth_role'] === 'commercial';
}

// Retourne une URL de redirection sécurisée basée sur le referer HTTP
// Sécurité : n'utilise que le chemin pour éviter toute redirection externe
function admin_safe_referer(string $default): string {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if ($referer === '') return $default;
    $path = parse_url($referer, PHP_URL_PATH);
    // Seuls les chemins commençant par /admin/ sont autorisés
    if ($path !== null && str_starts_with($path, '/admin/')) {
        $query = parse_url($referer, PHP_URL_QUERY);
        return $path . ($query ? '?' . $query : '');
    }
    return $default;
}