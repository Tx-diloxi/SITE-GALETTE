<?php
// Page RSE - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Intro ---
$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'rse'")->fetch();

// --- Section À propos (RSE) ---
$apropos = $pdo->query("SELECT * FROM partial_Apropos WHERE id = 2")->fetch();

// --- Section Actions RSE ---
$actions_rse = $pdo->query("SELECT * FROM action_RSE ORDER BY ordre")->fetchAll();

// --- Section Contact ---
$contact     = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// --- Section Partenaires ---
$partenaire      = $pdo->query("SELECT * FROM partial_Partenaire WHERE id = 1")->fetch();
$cardsPartenaire = $pdo->query("SELECT * FROM partenaire WHERE partial_partenaire_id = 1 ORDER BY ordre")->fetchAll();

// SEO
$pageTitle = "Engagements RSE – J'aime la Galette | Fabricant responsable";
$pageDesc  = "J'aime la Galette s'engage pour une production responsable : circuits courts, 0 additif, 0 conservateur, réduction des déchets, bien-être au travail. Découvrez nos actions RSE.";

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page RSE -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/rse/rse.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <?php include PARTIALS . 'actions-rse.php'; ?>

    <?php include PARTIALS . 'apropos.php'; ?>

    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague3.svg" class="vague-transition" alt="Image d'une vague stylisée">

    <?php include PARTIALS . 'partenaires.php'; ?>

    <?php include PARTIALS . 'contact.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';