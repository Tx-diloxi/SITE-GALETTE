#!/bin/bash
# Script de préparation pour déploiement LWS
# Utilisation : bash deploy-lws.sh
# Crée un dossier lws-ready/ avec la structure exacte à uploader sur LWS

set -euo pipefail

BASE_DIR="$(dirname "$0")"
SOURCE="$BASE_DIR/jaimelagalette"
DEST="$BASE_DIR/lws-ready"

echo "=== Préparation du déploiement LWS pour J'aime la Galette ==="
echo ""

# Nettoie une éventuelle précédente préparation
rm -rf "$DEST"

echo "1. Création de la structure..."

# Crée les dossiers
mkdir -p "$DEST/htdocs"
mkdir -p "$DEST/app/config"
mkdir -p "$DEST/app/helpers"
mkdir -p "$DEST/app/partials"
mkdir -p "$DEST/pages"
mkdir -p "$DEST/vendor"

echo "2. Copie des fichiers web (public/ → htdocs/)..."

# Copie tout le contenu de public/ dans htdocs/
cp -r "$SOURCE/public/"* "$DEST/htdocs/"
cp -r "$SOURCE/public/."[!.]* "$DEST/htdocs/" 2>/dev/null || true

echo "3. Copie des dossiers applicatifs..."

# Copie app/, pages/, vendor/ à la racine (au même niveau que htdocs/)
cp -r "$SOURCE/app/"* "$DEST/app/"
cp -r "$SOURCE/pages/"* "$DEST/pages/"
cp -r "$SOURCE/vendor/"* "$DEST/vendor/" 2>/dev/null || true

# Fichiers racine
cp "$SOURCE/config.php" "$DEST/config.php" 2>/dev/null || true
cp "$SOURCE/composer.json" "$DEST/composer.json" 2>/dev/null || true
cp "$SOURCE/composer.lock" "$DEST/composer.lock" 2>/dev/null || true

echo "5. Correction du chemin 'public/' → 'htdocs/' dans app/config/admin.php..."
sed -i "s|APP_ROOT \. 'public'|APP_ROOT . 'htdocs'|g" "$DEST/app/config/admin.php"

echo "6. Création du fichier config.php LWS..."
cat > "$DEST/config.php" << 'CONFIGEOF'
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
CONFIGEOF

echo ""
echo "=== ✓ Dossier 'lws-ready/' prêt ! ==="
echo ""
echo "Ce qu'il reste à faire :"
echo ""
echo "A) Connectez-vous à votre FTP LWS"
echo "B) Uploadez le contenu de 'lws-ready/' à la racine de votre FTP :"
echo ""
echo "   lws-ready/htdocs/   →   /htdocs/    (dossier web public)"
echo "   lws-ready/app/      →   /app/"
echo "   lws-ready/pages/    →   /pages/"
echo "   lws-ready/vendor/   →   /vendor/"
echo "   lws-ready/config.php →  /config.php"
echo ""
echo "C) Dans votre panel LWS :"
echo "   1. Créez une base MySQL (MySql & PhpMyAdmin > Créer une base)"
echo "   2. Importez le fichier docker/sql/01_schema.sql dans phpMyAdmin"
echo "   3. Modifiez /config.php sur le serveur avec vos vrais identifiants"
echo ""
echo "D) Supprimez default_index.html de /htdocs/ sur le serveur"
echo ""
