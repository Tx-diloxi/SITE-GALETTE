<?php
/**
 * Admin FAQ question toggle endpoint
 *
 * Handles POST requests to toggle the published status (en_ligne)
 * of a FAQ question.
 */
declare(strict_types=1); // Active le mode strict de typage en PHP

// --- Chargement des dépendances et authentification ---
require_once __DIR__ . '/../../../app/config/database.php'; // Connexion BDD
require_once __DIR__ . '/../../../app/config/admin.php';   // Fonctions d'administration
admin_check_auth(); // Vérifie que l'utilisateur est connecté en tant qu'admin

// ---- Vérification de la méthode de requête ----
// Vérifie que la requête est bien de type POST (pas d'accès direct en GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    admin_redirect('questions.php'); // Redirige si accès direct
}

// --- Récupération des paramètres POST ---
$id = (int)($_POST['id'] ?? 0);       // ID de la question à basculer
$token = $_POST['csrf_token'] ?? '';  // Token CSRF de sécurité

// Vérifie que l'ID est valide et que le token CSRF est correct
if ($id <= 0 || !admin_csrf_verify($token)) {
    admin_redirect('questions.php');
}

// --- Récupération de l'état actuel de la question ---
$stmt = $pdo->prepare("SELECT en_ligne FROM question_FAQ WHERE id = ?");
$stmt->execute([$id]);
$q = $stmt->fetch(); // Ligne contenant le champ en_ligne

// --- Basculement de l'état (toggle) ---
if ($q) {
    // Inverse la valeur : 1 devient 0, 0 devient 1
    $new = $q['en_ligne'] ? 0 : 1;
    $upd = $pdo->prepare("UPDATE question_FAQ SET en_ligne = ? WHERE id = ?");
    $upd->execute([$new, $id]);
}

// --- Redirection vers la page précédente (ou la liste des questions) ---
$referer = admin_safe_referer('questions.php');
header('Location: ' . $referer);
exit; // Fin du script
