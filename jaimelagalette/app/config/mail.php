<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Configuration SMTP ────────────────────────────

// Hôte du serveur SMTP sortant
define('MAIL_HOST', 'mail.jaimelagalette.com');
// Port SMTP avec STARTTLS
define('MAIL_PORT', 587);
// Nom d'utilisateur SMTP depuis les variables d'environnement
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME'] ?? '');
// Mot de passe SMTP depuis les variables d'environnement
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?? '');
// Adresse d'expédition des emails (identique au nom d'utilisateur SMTP)
define('MAIL_FROM', $_ENV['MAIL_USERNAME'] ?? '');
// Nom d'affichage de l'expéditeur
define('MAIL_FROM_NAME', 'J\'aime la Galette');
// Destinataire par défaut (fallback si aucun destinataire spécifique)
define('MAIL_TO', $_ENV['MAIL_USERNAME'] ?? '');
// Mode debug SMTP (0 = désactivé en production)
define('MAIL_DEBUG', (int)($_ENV['MAIL_DEBUG'] ?? 0));
