<?php
/**
 * Admin - Gestion des produits
 * Bascule le statut en ligne/hors ligne d'un produit
 */
declare(strict_types=1);

// ---- Initialisation ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// ---- Vérification CSRF ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !admin_csrf_verify($_POST['csrf_token'] ?? '')) {
    $referer = admin_safe_referer('/admin/produits/index.php');
    header('Location: ' . $referer);
    exit;
}

// ---- Récupération du produit ----
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    $referer = admin_safe_referer('/admin/produits/index.php');
    header('Location: ' . $referer);
    exit;
}

$stmt = $pdo->prepare("SELECT en_ligne FROM produit WHERE id = :id");
$stmt->execute([':id' => $id]);
$produit = $stmt->fetch();

if (!$produit) {
    $referer = admin_safe_referer('/admin/produits/index.php');
    header('Location: ' . $referer);
    exit;
}

// ---- Mise à jour du statut ----
$newValue = (int)(!$produit['en_ligne']);
$stmt = $pdo->prepare("UPDATE produit SET en_ligne = :en_ligne WHERE id = :id");
$stmt->execute([':en_ligne' => $newValue, ':id' => $id]);

$referer = admin_safe_referer('/admin/produits/index.php');
header('Location: ' . $referer);
exit;
