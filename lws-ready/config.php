<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Paris');

// ⚠️ REMPLACEZ CES VALEURS par vos identifiants LWS
// (trouvables dans votre panel LWS > MySQL & PhpMyAdmin)
$host   = 'localhost';        // Serveur MySQL LWS
$dbName = 'votre_base';       // Nom de la base (ex: u123456_jaimelagalette)
$dbUser = 'votre_utilisateur';// Utilisateur MySQL (ex: u123456_jalg)
$dbPass = 'votre_motdepasse'; // Mot de passe MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    error_log('Erreur de connexion BDD : ' . $e->getMessage());
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur technique</title></head><body>';
    echo '<h1>Erreur technique</h1>';
    echo '<p>Une erreur est survenue. Veuillez réessayer dans quelques instants.</p>';
    echo '</body></html>';
    exit;
}
