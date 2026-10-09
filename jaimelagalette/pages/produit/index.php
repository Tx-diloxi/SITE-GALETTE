<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';
require_once __DIR__ . '/../../app/helpers/seo.php';

$produitId = (int)($_GET['produit_id'] ?? 0);
if ($produitId < 1) {
    http_response_code(404);
    require_once __DIR__ . '/../404.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, m.nom as marque_nom, m.slug as marque_slug
    FROM produit p
    LEFT JOIN marque m ON p.marque_id = m.id
    WHERE p.id = ? AND p.en_ligne = TRUE
");
$stmt->execute([$produitId]);
$produit = $stmt->fetch();

if (!$produit) {
    http_response_code(404);
    require_once __DIR__ . '/../404.php';
    exit;
}

$slug = slugify($produit['nom'] ?: $produit['titre']);
$expectedSlug = $_GET['url'] ?? '';
if (!str_ends_with($expectedSlug, '/' . $slug) && !str_ends_with($expectedSlug, '/' . $slug . '/')) {
    $canonical = produitUrl($produit);
}

$stmtApropos = $pdo->prepare("SELECT * FROM produit_Apropos WHERE produit_id = ? LIMIT 1");
$stmtApropos->execute([$produitId]);
$apropos = $stmtApropos->fetch();

$stmtIngredient = $pdo->prepare("SELECT * FROM partial_Ingredient WHERE produit_id = ? LIMIT 1");
$stmtIngredient->execute([$produitId]);
$ingredient = $stmtIngredient->fetch();

$cardsIngredient = [];
if ($ingredient) {
    $stmtCards = $pdo->prepare("SELECT * FROM card_Ingredient WHERE partial_ingredient_id = ? ORDER BY ordre ASC");
    $stmtCards->execute([$ingredient['id']]);
    $cardsIngredient = $stmtCards->fetchAll();
}

$marqueSlug = $produit['marque_slug'] ?? '';

$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

$label     = $pdo->query("SELECT * FROM partial_Label WHERE id = 1")->fetch();
$cardsLabel = $pdo->query("SELECT * FROM card_Label WHERE partial_label_id = 1 ORDER BY ordre")->fetchAll();

$contact     = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

$pageTitle = $produit['nom'] . " – " . ($produit['marque_nom'] ?? "J'aime la Galette");
$pageDesc = strip_tags($apropos['contenu'] ?? "Découvrez " . $produit['nom'] . " de la gamme " . ($produit['marque_nom'] ?? "J'aime la Galette") . ". Sans additifs, fabrication française.");

$ogImage = !empty($produit['image']) ? $produit['image'] : null;

// JSON-LD Product
$productSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $produit['nom'] ?: $produit['titre'],
    'description' => strip_tags($apropos['contenu'] ?? ''),
    'brand' => ['@type' => 'Brand', 'name' => $produit['marque_nom'] ?? "J'aime la Galette"],
    'category' => 'Crêpes et galettes',
];
if (!empty($produit['image'])) {
    $productSchema['image'] = SITE_URL . '/' . ltrim($produit['image'], '/');
}

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/nos-produits/marque/marque.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/partials/intro/intro.css">

<main>
<section id="intro">
    <!-- Fil d'Ariane : pastille Accueil › Nos produits › marque › produit -->
    <div class="fil_ariane">
        <nav aria-label="Fil d'Ariane">
            <a href="/">Accueil</a>
            <span class="sep" aria-hidden="true">›</span>
            <a href="/nos-produits">Nos produits</a>
            <span class="sep" aria-hidden="true">›</span>
            <?php if ($marqueSlug): ?>
            <a href="/nos-produits/<?= htmlspecialchars($marqueSlug) ?>"><?= htmlspecialchars($produit['marque_nom'] ?? '') ?></a>
            <span class="sep" aria-hidden="true">›</span>
            <?php endif; ?>
            <span aria-current="page"><?= htmlspecialchars($produit['nom']) ?></span>
        </nav>
    </div>
    <div class="titre">
        <h3><?= htmlspecialchars($produit['sous_titre'] ?? '') ?></h3>
        <h1><?= htmlspecialchars($produit['nom']) ?></h1>
    </div>
</section>

<?php if ($apropos): ?>
<section id="a_propos" class="section">
    <div class="titre">
        <h3><?= htmlspecialchars($apropos['sous_titre'] ?? '') ?></h3>
        <h2><?= htmlspecialchars($apropos['titre'] ?? '') ?></h2>
    </div>
    <?php if (!empty($apropos['contenu'])): ?>
    <div class="contenu">
        <p><?= nl2br(htmlspecialchars($apropos['contenu'])) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($apropos['image'])): ?>
    <img src="<?= htmlspecialchars($apropos['image']) ?>" alt="<?= htmlspecialchars($apropos['alt'] ?? '') ?>" loading="lazy">
    <?php endif; ?>
    <?php if (!empty($apropos['cta_label']) && !empty($apropos['cta_lien'])): ?>
    <div>
        <a href="<?= htmlspecialchars($apropos['cta_lien']) ?>"><button><?= htmlspecialchars($apropos['cta_label']) ?></button></a>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($ingredient): ?>
<section id="essentiel" class="section">
    <div class="titre">
        <h3><?= htmlspecialchars($ingredient['sous_titre'] ?? '') ?></h3>
        <h2><?= htmlspecialchars($ingredient['titre'] ?? '') ?></h2>
    </div>
    <?php if (!empty($cardsIngredient)): ?>
    <div class="grille">
        <?php foreach ($cardsIngredient as $card): ?>
        <div class="carte">
            <img src="<?= htmlspecialchars($card['image']) ?>" alt="<?= htmlspecialchars($card['alt'] ?? '') ?>" loading="lazy">
            <h3><?= htmlspecialchars($card['titre']) ?></h3>
            <p><?= htmlspecialchars($card['description']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<section id="cta" class="section">
    <div class="contenu">
        <h2>Vous souhaitez commander <?= htmlspecialchars($produit['nom']) ?> ?</h2>
        <p><a href="/contact"><button>Contactez-nous</button></a></p>
    </div>
</section>

<?php include PARTIALS . 'label.php'; ?>
<?php include PARTIALS . 'contact.php'; ?>
</main>

<script type="application/ld+json"><?= json_encode($productSchema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
?>
