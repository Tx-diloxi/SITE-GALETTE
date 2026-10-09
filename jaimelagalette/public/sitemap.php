<?php
declare(strict_types=1);

header('Content-Type: application/xml; charset=utf-8');

require_once dirname(__DIR__) . '/app/config/paths.php';

// Domaine public unique (SITE_URL)
$baseUrl = SITE_URL;

$pages = [
    '/'                                   => ['1.0', 'weekly'],
    '/le-groupe'                          => ['0.8', 'monthly'],
    '/nos-produits'                       => ['0.9', 'weekly'],
    '/nos-produits/jaime-la-galette'      => ['0.7', 'monthly'],
    '/nos-produits/be-goodn'              => ['0.7', 'monthly'],
    '/savoir-faire'                       => ['0.8', 'monthly'],
    '/rse'                                => ['0.8', 'monthly'],
    '/faq'                                => ['0.7', 'weekly'],
    '/recrutement'                        => ['0.6', 'weekly'],
    '/contact'                            => ['0.6', 'yearly'],
    '/mentions-legales'                   => ['0.3', 'yearly'],
    '/politique-confidentialite'          => ['0.3', 'yearly'],
    '/cookies'                            => ['0.3', 'yearly'],
];

// Ajoute les produits et ateliers depuis la BDD
try {
    require_once dirname(__DIR__) . '/config.php';
    require_once dirname(__DIR__) . '/app/helpers/seo.php';

    $produits = $pdo->query("SELECT id, nom, titre FROM produit WHERE en_ligne = TRUE")->fetchAll();
    foreach ($produits as $p) {
        $pages[produitUrl($p)] = ['0.6', 'monthly'];
    }

    $ateliers = $pdo->query("SELECT id, nom, ville FROM point_Carte WHERE est_ouvert = TRUE")->fetchAll();
    foreach ($ateliers as $a) {
        $pages[atelierUrl($a)] = ['0.5', 'monthly'];
    }
} catch (Exception $e) {
    // Si la BDD n'est pas disponible, on sert juste les pages statiques
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $loc => [$priority, $changefreq]): ?>
    <url>
        <loc><?= $baseUrl ?><?= $loc ?></loc>
        <priority><?= $priority ?></priority>
        <changefreq><?= $changefreq ?></changefreq>
    </url>
<?php endforeach; ?>
</urlset>
