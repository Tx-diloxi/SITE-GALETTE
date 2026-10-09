<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Configuration SMTP ────────────────────────────

// Lecture de la configuration : variables d'environnement (Docker) ou config.local.php (hébergeur)
require_once __DIR__ . '/env.php';

// Hôte du serveur SMTP sortant
define('MAIL_HOST', env('MAIL_HOST', 'mail.jaimelagalette.com'));
// Port SMTP avec STARTTLS
define('MAIL_PORT', (int)env('MAIL_PORT', '587'));
// Nom d'utilisateur SMTP
define('MAIL_USERNAME', env('MAIL_USERNAME', ''));
// Mot de passe SMTP
define('MAIL_PASSWORD', env('MAIL_PASSWORD', ''));
// Adresse d'expédition des emails (par défaut : le nom d'utilisateur SMTP)
define('MAIL_FROM', env('MAIL_FROM', MAIL_USERNAME));
// Nom d'affichage de l'expéditeur
define('MAIL_FROM_NAME', 'J\'aime la Galette');
// Destinataire par défaut (fallback si aucun destinataire spécifique)
define('MAIL_TO', env('MAIL_TO', MAIL_USERNAME));
// Mode debug SMTP (0 = désactivé en production)
define('MAIL_DEBUG', (int)env('MAIL_DEBUG', '0'));
