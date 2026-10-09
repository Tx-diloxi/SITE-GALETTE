<?php
// Page Contact - J'aime la Galette
declare(strict_types=1);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';
require_once __DIR__ . '/../../app/helpers/session.php';
require_once __DIR__ . '/../../app/helpers/forms.php';

init_page_session();

// Traite le formulaire de contact si soumis
$formResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formResult = handleContactForm($pdo);
}

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Intro ---
$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'contact'")->fetch();

// --- Section Carte des points de vente ---
$carte       = $pdo->query("SELECT * FROM partial_Carte WHERE id = 1")->fetch();
$pointsCarte = $pdo->query("SELECT * FROM point_Carte WHERE partial_carte_id = 1 AND est_ouvert = TRUE ORDER BY id")->fetchAll();

// SEO
$pageTitle = "Contact et ateliers – Galette de Broons | J'aime la Galette";
$pageDesc  = "Contactez J'aime la Galette (La Galette de Broons) : formulaire pour consommateurs et professionnels (GMS, restauration, traiteurs) et coordonnées de nos 8 ateliers en Bretagne, Normandie et Pays de la Loire.";

// JSON-LD : un LocalBusiness par atelier (coordonnées affichées sur la carte)
require_once HELPERS . 'seo.php';
$extraSchemas = array_map('atelierSchema', array_filter($pointsCarte, fn($p) => !empty($p['nom'])));

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page Contact -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/contact/contact.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <?php include PARTIALS . 'formulaire-cta.php'; ?>

    <?php include PARTIALS . 'carte.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
