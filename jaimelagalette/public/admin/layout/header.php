<!-- --- En-tête du layout admin --- -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – J'aime la Galette</title>
    <!-- Polices et feuille de style admin -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,600;0,700;1,900&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/page/admin/admin.css">
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Barre de navigation supérieure -->
    <header class="admin-header">
        <div class="admin-header-brand">
            <img src="/assets/images/logo_jaimelagalette.png" alt="J'aime la Galette" class="admin-header-logo">
            <span class="admin-header-title"><?= auth_is_admin() ? 'Administration' : 'Espace Commercial' ?></span>
        </div>
        <div class="admin-header-right">
            <span class="admin-header-user"><?= htmlspecialchars($_SESSION['auth_user'] ?? 'Utilisateur', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="/admin/logout.php" class="admin-btn admin-btn--small admin-btn--outline">Déconnexion</a>
        </div>
    </header>
    <aside class="admin-sidebar">
        <nav class="admin-nav">
            <?php if (auth_is_admin()): ?>
            <a href="/admin/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/index.php') || $_SERVER['REQUEST_URI'] === '/admin/' ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">📊</span> Tableau de bord
            </a>
            <a href="/admin/pages/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/pages') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">📝</span> Pages
            </a>
            <a href="/admin/pages/edit.php?page=recrutement&section=offres&only=offres" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], 'section=offres') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">💼</span> Offres d'emploi
            </a>
            <a href="/admin/produits/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/produits') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">🥞</span> Produits
            </a>
            <a href="/admin/marques/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/marques') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">🏷️</span> Marques
            </a>
            <a href="/admin/sites/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/sites') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">📍</span> Sites
            </a>
            <a href="/admin/candidatures/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/candidatures') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">👥</span> Candidatures
            </a>
            <a href="/admin/contacts/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/contacts') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">✉️</span> Contacts
            </a>
            <a href="/admin/faq/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/faq') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">❓</span> FAQ
            </a>
            <hr class="admin-nav-separator">
            <a href="/admin/visites-mysteres/list.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/visites-mysteres') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">🕵️</span> Visites mystères
            </a>
            <a href="/admin/commerciaux/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/commerciaux') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">👤</span> Commerciaux
            </a>
            <a href="/admin/livreurs/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/livreurs') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">🚚</span> Livreurs
            </a>
            <?php else: ?>
            <a href="/admin/commercial/visite-mystere/index.php" class="admin-nav-link<?= str_contains($_SERVER['REQUEST_URI'], '/admin/commercial/visite-mystere') ? ' admin-nav-link--active' : '' ?>">
                <span class="admin-nav-icon">🕵️</span> Nouvelle visite mystère
            </a>
            <?php endif; ?>
        </nav>
    </aside>
