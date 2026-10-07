<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Configuration de la base de données ───────────
// Établit une connexion PDO à la base de données MySQL
// Utilise les variables d'environnement pour les paramètres de connexion

// Définit le fuseau horaire par défaut sur Europe/Paris
date_default_timezone_set('Europe/Paris');

// Récupère les paramètres de connexion depuis les variables d'environnement ou valeurs par défaut
$host = getenv('DB_HOST') ?: 'db';
// Nom de la base de données
$dbName = getenv('DB_NAME') ?: 'jaimelagalette';
// Nom d'utilisateur MySQL
$dbUser = getenv('DB_USER') ?: 'jalg_user';
// Mot de passe MySQL
$dbPass = getenv('DB_PASS') ?: '';
// Jeu de caractères UTF-8 pour la compatibilité Unicode
$charset = 'utf8mb4';

// Construit la chaîne DSN (Data Source Name) pour PDO
$dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

// Définit les options de connexion PDO
$options = [
    // Active le mode exception pour les erreurs SQL
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // Utilise le mode FETCH_ASSOC par défaut (tableaux associatifs)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Désactive l'émulation des requêtes préparées (requêtes natives)
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Tente la connexion à la base de données avec PDO
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // Logge l'erreur de connexion
    error_log('Erreur de connexion BDD : ' . $e->getMessage());
    // Retourne un code HTTP 500
    http_response_code(500);
    // Affiche une page d'erreur simple et sécurisée
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur technique</title></head><body>';
    echo '<h1>Erreur technique</h1>';
    echo '<p>Une erreur est survenue. Veuillez réessayer dans quelques instants.</p>';
    echo '</body></html>';
    // Arrête l'exécution du script
    exit;
}
