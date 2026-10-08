<?php
// --- Front Controller J'aime la Galette ---
// Point d'entrée unique pour toutes les requêtes HTTP
// Redirige vers la page dynamique demandée

declare(strict_types=1);

$url = $_GET['url'] ?? null;
if ($url === null) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $url = trim(str_replace($scriptPath, '', $requestUri), '/');
}

$routes = [
    '' => 'pages/home.php',
    'le-groupe' => 'pages/le-groupe/index.php',
    '404' => 'pages/404.php',
    'nos-produits' => 'pages/nos-produits/index.php',
    'savoir-faire' => 'pages/savoir-faire/index.php',
    'rse' => 'pages/rse/index.php',
    'faq' => 'pages/faq/index.php',
    'recrutement' => 'pages/recrutement/index.php',
    'contact' => 'pages/contact/index.php',
    'nos-produits/jaime-la-galette' => 'pages/nos-produits/jaime-la-galette/index.php',
    'nos-produits/be-goodn' => 'pages/nos-produits/be-goodn/index.php',
    // Routes SEO
    'sitemap.xml' => 'sitemap.php',
    // Routes légales
    'mentions-legales' => 'pages/mentions-legales/index.php',
    'politique-confidentialite' => 'pages/confidentialite/index.php',
    'cookies' => 'pages/cookies/index.php',
    // Routes admin
    'admin' => 'admin/index.php',
];

// Route dynamique pour les ateliers : /atelier/{id}/{slug}
$page = null;
if (strpos($url, 'atelier/') === 0) {
    $page = 'pages/atelier/index.php';
} elseif (preg_match('#^produit/(\d+)(?:/|$)#', $url, $m)) {
    // Route dynamique pour les produits : /produit/{id}/{slug}
    $_GET['produit_id'] = $m[1];
    $page = 'pages/produit/index.php';
} else {
    $page = $routes[$url] ?? null;
}

// Résolution du chemin : on cherche d'abord dans APP_ROOT (au-dessus de htdocs),
// puis dans HTDOCS (__DIR__) pour les fichiers web racine (sitemap.php, admin/)
if ($page !== null) {
    $appPath  = __DIR__ . '/../' . $page;
    $docPath  = __DIR__ . '/' . $page;
    if (file_exists($appPath)) {
        require_once $appPath;
        exit;
    }
    if (file_exists($docPath)) {
        require_once $docPath;
        exit;
    }
}

http_response_code(404);
require_once __DIR__ . '/../pages/404.php';
exit;