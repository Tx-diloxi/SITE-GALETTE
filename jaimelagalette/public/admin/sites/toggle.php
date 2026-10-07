<?php
declare(strict_types=1);

// Inclusion des fichiers de configuration
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
// Vérifie que l'utilisateur est connecté en tant qu'admin
admin_check_auth();

// Récupère l'URL de provenance pour y revenir après l'action
$referer = admin_safe_referer('/admin/sites/index.php');
// Redirige vers la page précédente (le toggle est géré côté JS/interface)
header('Location: ' . $referer);
exit;
