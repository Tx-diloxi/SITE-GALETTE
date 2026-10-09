<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/seo.php';

// --- Computed values ---
// Toutes les URL publiques (canonical, Open Graph, schémas) reposent sur SITE_URL (app/config/paths.php)
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$currentUrl = SITE_URL . ($requestPath === '/' ? '/' : rtrim($requestPath, '/'));
$canonicalUrl = $canonical ?? $currentUrl;
// Un canonical fourni sous forme de chemin ('/produit/1/nom') est rendu absolu
if (str_starts_with($canonicalUrl, '/')) {
    $canonicalUrl = SITE_URL . $canonicalUrl;
}
$imageUrl = $ogImage ?? (SITE_URL . '/assets/images/logo_jaimelagalette.png');
$noIndexTag = $noIndex ?? 'index, follow, max-image-preview:large, max-snippet:-1';

// --- Breadcrumb ---
$breadcrumbLabel = [
    '' => 'Accueil',
    'le-groupe' => 'Le Groupe',
    'nos-produits' => 'Nos produits',
    'jaime-la-galette' => "J'aime la Galette",
    'be-goodn' => "Be Good'n",
    'savoir-faire' => 'Savoir-faire',
    'rse' => 'RSE',
    'faq' => 'FAQ',
    'recrutement' => 'Recrutement',
    'contact' => 'Contact',
    '404' => 'Page non trouvée',
];
$currentRoute = trim($requestPath, '/');
$breadcrumbItems = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => SITE_URL . '/'],
];
if ($currentRoute !== '' && $currentRoute !== '404') {
    $position = 2;
    $segments = explode('/', $currentRoute);
    $pathAccum = '';
    foreach ($segments as $seg) {
        $pathAccum .= '/' . $seg;
        $breadcrumbName = $breadcrumbLabel[$seg] ?? $breadcrumbLabel[$currentRoute] ?? ucfirst(str_replace('-', ' ', $seg));
        if ($seg === end($segments)) {
            $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $position, 'name' => $breadcrumbName];
        } else {
            $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $position, 'name' => $breadcrumbName, 'item' => SITE_URL . $pathAccum];
        }
        $position++;
    }
}

// --- JSON-LD Organization ---
$orgSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => SITE_URL . '/#organization',
    'name' => "J'aime la Galette",
    'legalName' => 'LA GALETTE DE BROONS',
    'alternateName' => ['La Galette de Broons', 'Jaime la Galette'],
    'url' => SITE_URL . '/',
    'logo' => SITE_URL . '/assets/images/logo_jaimelagalette.png',
    'image' => SITE_URL . '/assets/images/logo_jaimelagalette.png',
    'description' => "Fabricant de galettes de blé noir et de crêpes bretonnes artisanales, ultra fraîches, sans additifs ni conservateurs, pour les professionnels (GMS, restauration, traiteurs).",
    'foundingDate' => '2013',
    'vatID' => 'FR21402557763',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Zone Artisanale du Pilaga',
        'postalCode' => '22250',
        'addressLocality' => 'Broons',
        'addressRegion' => 'Bretagne',
        'addressCountry' => 'FR',
    ],
    'telephone' => '+33 2 14 02 51 51',
    'areaServed' => ['@type' => 'Country', 'name' => 'France'],
    'brand' => [
        ['@type' => 'Brand', 'name' => "J'aime la Galette"],
        ['@type' => 'Brand', 'name' => "Be Good'n"],
    ],
    'knowsAbout' => [
        'galette de blé noir', 'galette bretonne artisanale', 'galette de Broons', 'crêpes bretonnes',
        'crêpes sucrées', 'fabrication artisanale', 'produits ultra-frais sans additifs',
    ],
    'contactPoint' => [[
        '@type' => 'ContactPoint',
        'contactType' => 'customer service',
        'telephone' => '+33 2 14 02 51 51',
        'url' => SITE_URL . '/contact',
        'areaServed' => 'FR',
        'availableLanguage' => 'fr',
    ]],
    'sameAs' => ['https://www.facebook.com/lagalettedebroons'],
];

// --- JSON-LD WebSite ---
$websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    '@id' => SITE_URL . '/#website',
    'url' => SITE_URL . '/',
    'name' => "J'aime la Galette",
    'inLanguage' => 'fr-FR',
    'publisher' => ['@id' => SITE_URL . '/#organization'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? "J'aime la Galette") ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? '') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta name="robots" content="<?= htmlspecialchars($noIndexTag) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? "J'aime la Galette") ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc ?? '') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($imageUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="J'aime la Galette">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle ?? "J'aime la Galette") ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDesc ?? '') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($imageUrl) ?>">
    <meta name="theme-color" content="#111111">
    <?= jsonLd($orgSchema) ?>

    <?= jsonLd($websiteSchema) ?>

    <?= jsonLd(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumbItems]) ?>

    <?php foreach (($extraSchemas ?? []) as $extraSchema): ?>
    <?= jsonLd($extraSchema) ?>

    <?php endforeach; ?>
    <link rel="preload" href="<?= BASE_URL ?>assets/fonts/montserrat.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="icon" href="<?= BASE_URL ?>assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
