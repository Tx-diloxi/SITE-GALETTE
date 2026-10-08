<?php
// Page Be Good'n (marque) - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/page.php';

// --- Données globales ---
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Récupération des produits de la marque ---
$marqueSlug = 'be-goodn';

// Requête des produits de la marque via JOIN
$stmt = $pdo->prepare("
    SELECT p.id, p.sous_titre, p.titre, COALESCE(m.nom, p.marque) as marque, p.nom, p.image, p.en_ligne,
           m.slug as marque_slug, m.logo as marque_logo
    FROM produit p
    LEFT JOIN marque m ON p.marque_id = m.id
    WHERE m.slug = ? AND p.en_ligne = TRUE
    ORDER BY p.id ASC
");
$stmt->execute([$marqueSlug]);
$listeProduits = $stmt->fetchAll();

// Construction du tableau produitsData avec toutes les données associées
$produitsData = [];

foreach ($listeProduits as $produit) {
    $produitId = $produit['id'];
    
    // Récupération des données "À propos" pour ce produit
    $stmtApropos = $pdo->prepare("SELECT * FROM produit_Apropos WHERE produit_id = ? LIMIT 1");
    $stmtApropos->execute([$produitId]);
    $apropos = $stmtApropos->fetch() ?: null;
    
    // Récupération des données "Ingrédients" pour ce produit
    $stmtIngredient = $pdo->prepare("SELECT * FROM partial_Ingredient WHERE produit_id = ? LIMIT 1");
    $stmtIngredient->execute([$produitId]);
    $ingredient = $stmtIngredient->fetch() ?: null;
    
    // Récupération des cartes d'ingrédients si la section existe
    $cardsIngredient = [];
    if ($ingredient) {
        $stmtCards = $pdo->prepare("SELECT * FROM card_Ingredient WHERE partial_ingredient_id = ? ORDER BY ordre ASC");
        $stmtCards->execute([$ingredient['id']]);
        $cardsIngredient = $stmtCards->fetchAll();
    }
    
    // Stockage dans le tableau produitsData
    $produitsData[$produitId] = [
        'produit'    => $produit,
        'apropos'    => $apropos,
        'ingredient' => $ingredient,
        'cards'      => $cardsIngredient,
    ];
}

// --- Titres du carrousel (depuis le premier produit) ---
$titre = $listeProduits[0]['sous_titre'] ?? 'Nos produits';
$sousTitre = $listeProduits[0]['titre'] ?? 'Découvrez notre gamme';

// --- Données statiques (non liées aux produits) ---
// Section Ingrédients essentiels
$essentielTitre  = $pdo->query("SELECT * FROM partial_Ingredient WHERE id = 1")->fetch();
$cardsIngredient = $pdo->query("SELECT * FROM card_Ingredient WHERE partial_ingredient_id = 1 ORDER BY id")->fetchAll();

// Section Labels
$label     = $pdo->query("SELECT * FROM partial_Label WHERE id = 1")->fetch();
$cardsLabel = $pdo->query("SELECT * FROM card_Label WHERE partial_label_id = 1 ORDER BY ordre")->fetchAll();

// Section Contact
$contact      = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// SEO
$pageTitle = "Gamme Be Good'n – Crêpes et galettes bio | J'aime la Galette";
$pageDesc  = "Découvrez la gamme Be Good'n par J'aime la Galette : crêpes et galettes bio, sans additifs ni conservateurs. Des recettes authentiques pour une alimentation saine et responsable.";

// --- JSON-LD Product for featured products ---
$siteProtocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$siteHost = $_SERVER['HTTP_HOST'] ?? 'www.jaimelagalette.com';
$productSchemas = [];
foreach ($listeProduits as $p) {
    $ps = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $p['nom'] ?: $p['titre'],
        'description' => $p['sous_titre'] ?? "Produit de la gamme Be Good'n",
        'brand' => ['@type' => 'Brand', 'name' => "Be Good'n"],
        'category' => 'Crêpes et galettes bio',
    ];
    if (!empty($p['image'])) {
        $ps['image'] = $siteProtocol . '://' . $siteHost . '/' . ltrim($p['image'], '/');
    }
    $productSchemas[] = $ps;
}

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page marque -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/nos-produits/marque/marque.css">

<main>

    <?php include PARTIALS . 'carrousel.php'; ?>

    <?php include PARTIALS . 'apropos.php'; ?>

    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague4.svg" class="vague-transition" alt="Image d'une vague stylisée">

    <?php include PARTIALS . 'essentiel.php'; ?>

    <?php include PARTIALS . 'label.php'; ?>

    <!-- JSON-LD Product -->
    <?php foreach ($productSchemas as $ps): ?>
    <script type="application/ld+json"><?= json_encode($ps, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>
    <?php endforeach; ?>

    <?php include PARTIALS . 'contact.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
?>