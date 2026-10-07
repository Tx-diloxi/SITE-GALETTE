<?php
// Page Savoir-faire - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Intro ---
$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'savoir-faire'")->fetch();

// --- Section Savoir-faire (timeline) ---
$titreSavoirFaire = $pdo->query("SELECT * FROM partial_SavoirFaire")->fetch();
$etapesSavoirFaire = $pdo->query("SELECT * FROM card_SavoirFaire ORDER BY ordre")->fetchAll();

// --- Section Contact ---
$contact     = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// SEO
$pageTitle = "Notre savoir-faire artisanal – Fabrication de crêpes et galettes | J'aime la Galette";
$pageDesc  = "Découvrez comment nous fabriquons nos crêpes et galettes bretonnes : pâte fraîche, cuisson traditionnelle, surgélation rapide. Un savoir-faire artisanal pour une qualité sans additif.";

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page Savoir-faire -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/savoir-faire/savoir-faire.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <?php include PARTIALS . 'infos-savoir.php'; ?>

    <?php include PARTIALS . 'contact.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';