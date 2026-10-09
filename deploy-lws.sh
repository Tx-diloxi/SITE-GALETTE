#!/bin/bash
# Prépare le dossier lws-ready/ : exactement ce qu'il faut envoyer sur l'hébergement LWS.
# Utilisation (depuis la racine du dépôt) :  bash deploy-lws.sh
#
# Le dossier généré n'est pas versionné (.gitignore) : relancez ce script avant chaque mise à jour.
# Procédure complète : DEPLOIEMENT-LWS.md

set -euo pipefail

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
SOURCE="$BASE_DIR/jaimelagalette"
DEST="$BASE_DIR/lws-ready"

echo "=== Préparation du déploiement LWS pour J'aime la Galette ==="

# Vérifications préalables
if [ ! -f "$SOURCE/vendor/autoload.php" ]; then
    echo "ERREUR : jaimelagalette/vendor/autoload.php est introuvable (PHPMailer manquant)." >&2
    echo "Lancez : docker compose exec php composer install --no-dev" >&2
    exit 1
fi
if [ ! -f "$BASE_DIR/docker/sql/01_schema.sql" ] || [ ! -f "$BASE_DIR/docker/sql/02_seeds.sql" ]; then
    echo "ERREUR : docker/sql/01_schema.sql ou 02_seeds.sql est introuvable." >&2
    exit 1
fi

rm -rf "$DEST"
mkdir -p "$DEST/htdocs" "$DEST/app" "$DEST/pages" "$DEST/vendor" "$DEST/_sql_a_importer"

echo "1. Site public : public/ -> htdocs/"
cp -r "$SOURCE/public/." "$DEST/htdocs/"
# Fichiers inutiles ou internes sur le serveur : sources SCSS, cartes de sources, notes internes
find "$DEST/htdocs" \( -name '*.scss' -o -name '*.css.map' -o -name '.DS_Store' \) -type f -delete
rm -f "$DEST/htdocs/chat.md" "$DEST/htdocs/text.php"
# Aucune donnée personnelle ni envoi de test : les dossiers d'envoi repartent vides (on garde leur .htaccess)
find "$DEST/htdocs/assets/uploads" -type f ! -name '.htaccess' ! -name '.gitkeep' -delete
find "$DEST/htdocs/assets/images/admin-uploads" -type f ! -name '.htaccess' -delete

echo "2. Application : app/, pages/, vendor/ et fichiers racine"
cp -r "$SOURCE/app/." "$DEST/app/"
cp -r "$SOURCE/pages/." "$DEST/pages/"
cp -r "$SOURCE/vendor/." "$DEST/vendor/"
cp "$SOURCE/config.php" "$SOURCE/config.local.example.php" "$SOURCE/composer.json" "$SOURCE/composer.lock" "$DEST/"

echo "3. Base de données : scripts à importer dans phpMyAdmin (à NE PAS envoyer sur le serveur)"
cp "$BASE_DIR/docker/sql/01_schema.sql" "$BASE_DIR/docker/sql/02_seeds.sql" "$DEST/_sql_a_importer/"

cp "$BASE_DIR/DEPLOIEMENT-LWS.md" "$DEST/LISEZMOI-DEPLOIEMENT.md"

# Archive facultative (si un Python fonctionnel est disponible) : pratique pour un envoi en une fois.
# On teste chaque candidat : sous Windows, "python3" peut être un raccourci du Microsoft Store inutilisable.
PY=""
for candidat in python3 python py; do
    if command -v "$candidat" >/dev/null 2>&1 && "$candidat" -c "import zipfile" >/dev/null 2>&1; then
        PY="$candidat"
        break
    fi
done
if [ -n "$PY" ]; then
    "$PY" - "$DEST" <<'PYEOF'
import os, sys, zipfile
dest = sys.argv[1]
archive = dest + '.zip'
with zipfile.ZipFile(archive, 'w', zipfile.ZIP_DEFLATED) as z:
    for dossier, _, fichiers in os.walk(dest):
        if os.path.basename(dossier) == '_sql_a_importer' or '_sql_a_importer' in dossier.split(os.sep):
            continue
        for f in fichiers:
            if f == 'LISEZMOI-DEPLOIEMENT.md' and dossier == dest:
                continue
            chemin = os.path.join(dossier, f)
            z.write(chemin, os.path.relpath(chemin, dest))
print('4. Archive prête : ' + os.path.basename(archive) + ' (sans le dossier _sql_a_importer)')
PYEOF
fi

echo ""
echo "=== lws-ready/ est prêt ==="
echo ""
echo "À la racine de votre FTP LWS, envoyez :"
echo "   htdocs/   app/   pages/   vendor/   config.php   config.local.example.php"
echo "(ou décompressez lws-ready.zip à la racine du FTP)"
echo ""
echo "Ensuite, sur le serveur :"
echo "   1. copiez config.local.example.php en config.local.php et remplissez-le (base, e-mail, mot de passe admin)"
echo "   2. importez _sql_a_importer/01_schema.sql puis 02_seeds.sql dans phpMyAdmin"
echo "   3. créez la tâche cron de purge RGPD"
echo ""
echo "Détail pas à pas : DEPLOIEMENT-LWS.md"
