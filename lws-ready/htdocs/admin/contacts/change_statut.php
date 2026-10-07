<?php
/**
 * Admin - Contacts : changement de statut d'un message.
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
    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    admin_redirect('index.php');
}

// -----------------------------------------------
// RÉCUPÉRATION DES DONNÉES
// -----------------------------------------------

$id = (int)($_POST['id'] ?? 0);
$statut = $_POST['statut'] ?? '';
$token = $_POST['csrf_token'] ?? '';

// -----------------------------------------------
// VALIDATION
// -----------------------------------------------

if ($id <= 0 || !in_array($statut, ['nouveau', 'lu', 'traite', 'archive'], true) || !admin_csrf_verify($token)) {
    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }
    admin_redirect('index.php');
}

// -----------------------------------------------
// MISE À JOUR DU STATUT
// -----------------------------------------------

if ($statut === 'traite') {
    $stmt = $pdo->prepare("UPDATE formulaire_contact SET statut = ?, traite_le = NOW() WHERE id = ?");
} else {
    $stmt = $pdo->prepare("UPDATE formulaire_contact SET statut = ? WHERE id = ?");
}
$stmt->execute([$statut, $id]);

// -----------------------------------------------
// RÉPONSE JSON OU REDIRECTION
// -----------------------------------------------

if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

$referer = admin_safe_referer('index.php');
header('Location: ' . $referer);
exit;
