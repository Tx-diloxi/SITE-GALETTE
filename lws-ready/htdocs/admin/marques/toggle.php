<?php
/**
 * Admin - Gestion des marques : bascule en ligne/hors ligne.
 */
declare(strict_types=1);

// ----
// Initialisation
// ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// ----
// Vérification CSRF
// ----
// Rejette la requête si ce n'est pas du POST ou si le token CSRF est invalide
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !admin_csrf_verify($_POST['csrf_token'] ?? '')) {
    $referer = admin_safe_referer('/admin/marques/index.php');
    header('Location: ' . $referer);
    exit;
}

// Récupère et valide l'ID de la marque depuis le formulaire
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    $referer = admin_safe_referer('/admin/marques/index.php');
    header('Location: ' . $referer);
    exit;
}

// Requête SQL : récupère l'état actuel (en_ligne) de la marque
$stmt = $pdo->prepare("SELECT en_ligne FROM marque WHERE id = :id");
$stmt->execute([':id' => $id]);
$marque = $stmt->fetch();

// Redirige si la marque n'existe pas
if (!$marque) {
    $referer = admin_safe_referer('/admin/marques/index.php');
    header('Location: ' . $referer);
    exit;
}

// Inverse la valeur de en_ligne (0 devient 1, 1 devient 0)
$new_value = (int)(!$marque['en_ligne']);
// Requête SQL : met à jour le statut en ligne/hors ligne
$stmt = $pdo->prepare("UPDATE marque SET en_ligne = :en_ligne WHERE id = :id");
$stmt->execute([':en_ligne' => $new_value, ':id' => $id]);

// Redirige vers la page précédente
$referer = admin_safe_referer('/admin/marques/index.php');
header('Location: ' . $referer);
exit;
