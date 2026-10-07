<?php
declare(strict_types=1);

// --- Computed values ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'www.jaimelagalette.com';
// Validation : n'autorise que les noms d'hôte standards (prévention host header injection)
if (!preg_match('/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?(\:[0-9]+)?$/i', $host)) {
    $host = 'www.jaimelagalette.com';
}
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentUrl = rtrim($protocol . '://' . $host . $requestPath, '/') ?: $protocol . '://' . $host . '/';
$canonicalUrl = $canonical ?? $currentUrl;
$imageUrl = $ogImage ?? ($protocol . '://' . $host . '/assets/images/logo_jaimelagalette.png');
$noIndexTag = $noIndex ?? 'index, follow';

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
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => $protocol . '://' . $host . '/'],
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
            $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $position, 'name' => $breadcrumbName, 'item' => $protocol . '://' . $host . $pathAccum];
        }
        $position++;
    }
}

// --- JSON-LD Organization ---
$orgSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => "J'aime la Galette",
    'url' => $protocol . '://' . $host . '/',
    'logo' => $protocol . '://' . $host . '/assets/images/logo_jaimelagalette.png',
    'description' => "Fabricant de crêpes et galettes bretonnes ultra fraîches sans additifs pour professionnels et GMS.",
    'foundingDate' => '2013',
    'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'FR'],
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => $protocol . '://' . $host . '/faq?q={search_term_string}',
        ],
        'query-input' => 'required name=search_term_string',
    ],
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
    <script type="application/ld+json"><?= json_encode($orgSchema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumbItems], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="icon" href="<?= BASE_URL ?>assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
