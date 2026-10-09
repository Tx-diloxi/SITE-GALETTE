<?php
// ════════════════════════════════════════════════════════════════════════════
//  Configuration de CET hébergement (modèle)
//
//  1. Copiez ce fichier en « config.local.php » à la racine du projet sur le serveur
//     (au même niveau que app/, pages/, vendor/ et config.php, PAS dans htdocs/).
//  2. Remplacez chaque valeur « A_REMPLACER ».
//  3. Ne le versionnez jamais : il contient des mots de passe (config.local.php est dans .gitignore).
//
//  Toute clé laissée vide ou absente prend la valeur par défaut du code.
// ════════════════════════════════════════════════════════════════════════════

return [
    // ── URL publique du site (canonical, Open Graph, sitemap, données structurées) ──
    // Sans slash final. Doit correspondre au domaine définitif.
    'SITE_URL' => 'https://www.jaimelagalette.com',

    // ── Base de données MySQL (panel LWS > MySQL & phpMyAdmin) ──
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'A_REMPLACER',   // ex. u123456_jaimelagalette
    'DB_USER' => 'A_REMPLACER',   // ex. u123456_jalg
    'DB_PASS' => 'A_REMPLACER',

    // ── Messagerie (envoi des formulaires de contact et de candidature) ──
    'MAIL_HOST'     => 'mail.jaimelagalette.com',
    'MAIL_USERNAME' => 'A_REMPLACER',   // adresse d'envoi, ex. siteweb@jaimelagalette.com
    'MAIL_PASSWORD' => 'A_REMPLACER',
    // 'MAIL_TO'    => '',              // destinataire par défaut (par défaut : MAIL_USERNAME)
    'MAIL_DEBUG'    => '0',

    // ── Espace d'administration ──
    // Générez le hash en ligne de commande, avec le mot de passe de votre choix :
    //   php -r "echo password_hash('VOTRE_MOT_DE_PASSE', PASSWORD_BCRYPT), PHP_EOL;"
    // Sans hash, personne ne peut se connecter à l'administration (aucun mot de passe par défaut).
    'ADMIN_USER'      => 'la-galette-admin',
    'ADMIN_PASS_HASH' => 'A_REMPLACER',

    // ── Divers ──
    'APP_DEBUG' => '0',   // 1 = affiche les erreurs PHP à l'écran (développement uniquement)
];
