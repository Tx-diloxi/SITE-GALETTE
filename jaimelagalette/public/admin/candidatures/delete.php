<?php
/**
 * Admin - Candidatures : suppression d'une candidature.
 */
declare(strict_types=1);

// -----------------------------------------------
// INITIALISATION ET VÉRIFICATION D'ACCÈS
// -----------------------------------------------

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// -----------------------------------------------
// VÉRIFICATION POST
// -----------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    admin_redirect('admin/candidatures/index.php');
}

// -----------------------------------------------
// RÉCUPÉRATION DE L'IDENTIFIANT ET CSRF
// -----------------------------------------------

$id = (int)($_POST['id'] ?? 0);
$token = $_POST['csrf_token'] ?? '';

// -----------------------------------------------
// VALIDATION
// -----------------------------------------------

if ($id <= 0 || !admin_csrf_verify($token)) {
    admin_redirect('admin/candidatures/index.php');
}

// -----------------------------------------------
// RÉCUPÉRATION DU CHEMIN DU CV
// -----------------------------------------------

$stmt = $pdo->prepare("SELECT cv_path FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

// -----------------------------------------------
// SUPPRESSION FICHIER + BASE
// -----------------------------------------------

if ($app) {
    if ($app['cv_path']) {
        $filePath = __DIR__ . '/../../assets/uploads/cv/' . $app['cv_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $del = $pdo->prepare("DELETE FROM applications WHERE id = ?");
    $del->execute([$id]);
}

// -----------------------------------------------
// REDIRECTION
// -----------------------------------------------

admin_redirect('admin/candidatures/index.php');
