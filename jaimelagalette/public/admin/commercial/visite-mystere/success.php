<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../../app/config/database.php';
require_once __DIR__ . '/../../../../app/config/admin.php';
require_once __DIR__ . '/../../../../app/config/commercial.php';

$commercial = commercial_check_auth();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visite enregistrée – J'aime la Galette</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,900;1,400&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/page/admin/admin.css">
    <link rel="stylesheet" href="/assets/css/page/commercial/commercial.css">
</head>
<body class="commercial-body">
    <header class="commercial-header">
        <div class="commercial-header-inner">
            <div class="commercial-header-brand">
                <img src="/assets/images/logo_jaimelagalette.png" alt="J'aime la Galette" class="commercial-header-logo">
                <span class="commercial-header-title">Visite mystère</span>
            </div>
            <div class="commercial-header-right">
                <span class="commercial-header-user"><?= htmlspecialchars($commercial['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="/admin/logout.php" class="commercial-btn commercial-btn--outline">Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="commercial-main">
        <div class="commercial-container">
            <div class="commercial-success">
                <div class="commercial-success-icon">✅</div>
                <h1>Visite mystère enregistrée !</h1>
                <p>Les informations ont bien été transmises à l'équipe d'administration.</p>
                <div class="commercial-success-actions">
                    <a href="/admin/commercial/visite-mystere/index.php" class="commercial-btn commercial-btn--primary">
                        ➕ Nouvelle visite
                    </a>
                    <a href="/admin/logout.php" class="commercial-btn commercial-btn--outline">
                        Se déconnecter
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
