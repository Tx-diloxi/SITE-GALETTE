<?php
/**
 * Admin - Candidatures : téléchargement d'un CV.
 *
 * Les CV sont des données personnelles : le dossier assets/uploads/cv/ est fermé à l'accès
 * direct (.htaccess) et chaque fichier est servi ici, après authentification d'un administrateur.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT cv_path FROM applications WHERE id = ?");
$stmt->execute([$id]);
$cvPath = (string)$stmt->fetchColumn();

// Le nom enregistré en base ne doit jamais pouvoir sortir du dossier des CV
$fichier = basename($cvPath);
$chemin = WEB_ROOT . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR . $fichier;

if ($fichier === '' || !is_file($chemin)) {
    http_response_code(404);
    exit('CV introuvable.');
}

$types = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];
$extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));

header('Content-Type: ' . ($types[$extension] ?? 'application/octet-stream'));
header('Content-Length: ' . (string)filesize($chemin));
header('Content-Disposition: inline; filename="' . rawurlencode($fichier) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($chemin);
