<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Configuration de l'espace commercial ──────────

// Définit les chemins racines si pas déjà définis
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

// Nom de session pour les commerciaux (partagé avec le nom admin)
define('COMMERCIAL_SESSION_NAME', 'jalg_admin');
// Durée de vie de la session commercial (2 heures)
define('COMMERCIAL_SESSION_LIFETIME', 7200);
// Répertoire de destination pour les photos d'inspection
define('INSPECTION_UPLOAD_DIR', APP_ROOT . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'inspections' . DIRECTORY_SEPARATOR);
// URL de base pour accéder aux photos d'inspection
define('INSPECTION_UPLOAD_URL', BASE_URL . 'assets/uploads/inspections/');
// Taille maximale des fichiers uploadés (5 Mo)
if (!defined('MAX_UPLOAD_SIZE')) {
    define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
}

// Vérifie le timeout d'inactivité de la session commercial
function commercial_auth_check_timeout(): void {
    if (isset($_SESSION['auth_last_activity']) && (time() - $_SESSION['auth_last_activity'] > COMMERCIAL_SESSION_LIFETIME)) {
        session_destroy();
        header('Location: ' . BASE_URL . 'admin/login.php?expired=1');
        exit;
    }
    $_SESSION['auth_last_activity'] = time();
}

// Vérifie que l'utilisateur est authentifié en tant que commercial
function commercial_check_auth(): array {
    if (ob_get_level() === 0) {
        ob_start();
    }
    session_name(COMMERCIAL_SESSION_NAME);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['auth_logged_in']) || $_SESSION['auth_role'] !== 'commercial') {
        header('Location: ' . BASE_URL . 'admin/login.php');
        exit;
    }
    commercial_auth_check_timeout();
    return [
        'id' => $_SESSION['auth_user_id'],
        'nom' => $_SESSION['auth_user'] ?? 'Commercial',
    ];
}

// Génère un token CSRF pour les formulaires de l'espace commercial
function commercial_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifie la validité du token CSRF soumis dans l'espace commercial
function commercial_csrf_verify(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// S'assure que le répertoire d'upload des inspections existe et est accessible en écriture
function commercial_ensure_upload_dir(): bool {
    $base = rtrim(INSPECTION_UPLOAD_DIR, DIRECTORY_SEPARATOR);
    if (!is_dir($base)) {
        @mkdir($base, 0777, true);
    }
    return is_dir($base) && is_writable($base);
}

// Upload multiple de photos pour une inspection donnée
function commercial_upload_photos(array $files, int $inspectionId, PDO $pdo): void {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $maxFiles = 10;
    $uploaded = 0;

    $count = count($files['name']);
    if ($count > $maxFiles) {
        throw new RuntimeException("Maximum $maxFiles photos autorisées.");
    }

    $dir = rtrim(INSPECTION_UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $inspectionId;
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (!is_dir($dir) || !is_writable($dir)) {
        throw new RuntimeException("Impossible d'écrire les photos sur le serveur.");
    }

    $stmt = $pdo->prepare("INSERT INTO inspection_photo (inspection_id, chemin) VALUES (:inspection_id, :chemin)");

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (empty($files['name'][$i])) continue;
        if ($files['size'][$i] > MAX_UPLOAD_SIZE) continue;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $files['tmp_name'][$i]);
        finfo_close($finfo);

        if (!in_array($mime, $allowed, true)) continue;

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
        if (!$ext) continue;

        $filename = 'photo_' . uniqid() . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($files['tmp_name'][$i], $dest)) continue;

        chmod($dest, 0644);
        $url = rtrim(INSPECTION_UPLOAD_URL, '/') . '/' . $inspectionId . '/' . $filename;
        $stmt->execute([':inspection_id' => $inspectionId, ':chemin' => $url]);
        $uploaded++;
    }

    if ($uploaded === 0 && $count > 0) {
        throw new RuntimeException("Aucune photo n'a pu être enregistrée. Vérifiez le format et la taille (max 5Mo, JPEG/PNG/WebP).");
    }
}

// Upload d'une seule photo pour une inspection donnée (retourne l'URL ou null)
function commercial_upload_photo(array $file, int $inspectionId): ?string {
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_mimes, true)) return null;

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => null,
    };
    if (!$ext) return null;

    $dir = rtrim(INSPECTION_UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $inspectionId;
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (!is_dir($dir) || !is_writable($dir)) {
        return null;
    }

    $filename = 'photo_' . uniqid() . '.' . $ext;
    $dest = $dir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    chmod($dest, 0644);
    return rtrim(INSPECTION_UPLOAD_URL, '/') . '/' . $inspectionId . '/' . $filename;
}
