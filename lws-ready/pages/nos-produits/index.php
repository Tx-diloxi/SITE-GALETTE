<?php
// Page Nos Produits - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Intro ---
$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'nos-produits'")->fetch();

// --- Section Produits ---
$produits    = $pdo->query("SELECT * FROM partial_Produit WHERE id = 1")->fetch();
$cardsProduits = $pdo->query("
    SELECT cp.*, m.nom as marque_nom, m.slug as marque_slug, m.logo as marque_logo, m.couleur_hex as marque_couleur
    FROM card_Produit cp
    LEFT JOIN marque m ON cp.marque_id = m.id
    WHERE cp.partial_produit_id = 1
    ORDER BY cp.id
")->fetchAll();

// --- Section Transparence ---
$transparence       = $pdo->query("SELECT * FROM partial_Transparence WHERE id = 1")->fetch();
$cardsTransparence = $pdo->query("SELECT * FROM card_Transparence WHERE partial_transparence_id = 1 ORDER BY id")->fetchAll();

// --- Section Labels ---
$label     = $pdo->query("SELECT * FROM partial_Label WHERE id = 1")->fetch();
$cardsLabel = $pdo->query("SELECT * FROM card_Label WHERE partial_label_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Contact ---
$contact     = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// SEO
$pageTitle = "Nos gammes de crêpes et galettes bretonnes – J'aime la Galette";
$pageDesc  = "Découvrez nos gammes J'aime la Galette et Be Good'n : galettes de blé noir, crêpes sucrées, chips de galettes. Fabriquées en Bretagne sans additifs, ultra-fraîches pour professionnels.";

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page Nos Produits -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/nos-produits/nos-produits.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <?php include PARTIALS . 'cta-produits.php'; ?>

    <?php include PARTIALS . 'transparence.php'; ?>

    <?php include PARTIALS . 'label.php'; ?>

    <?php include PARTIALS . 'contact.php'; ?>
</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';