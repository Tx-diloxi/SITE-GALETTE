<?php
// Page Recrutement - J'aime la Galette
declare(strict_types=1);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';
require_once __DIR__ . '/../../app/helpers/session.php';
require_once __DIR__ . '/../../app/helpers/forms.php';

init_page_session();

// Traite le formulaire de candidature si soumis
$formResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_recrutement'])) {
    $formResult = handleApplicationForm($pdo);
}

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Intro ---
$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'recrutement'")->fetch();
$introContact = $intro;

// --- Section Pourquoi nous rejoindre ---
$recrutement      = $pdo->query("SELECT * FROM partial_Recrutement WHERE id = 1")->fetch();
$cardsRecrutement = $pdo->query("SELECT * FROM card_Recrutement WHERE partial_recrutement_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Processus de recrutement ---
$processus       = $pdo->query("SELECT * FROM partial_Processus WHERE id = 1")->fetch();
$etapesProcessus = $pdo->query("SELECT * FROM etape_Processus WHERE partial_processus_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Offres d'emploi ---
$offreTitre     = $pdo->query("SELECT * FROM partial_Offre WHERE id = 1")->fetch();
$offresEmploi   = $pdo->query("SELECT * FROM offre_Emploi WHERE en_ligne = true ORDER BY date_publication DESC")->fetchAll();
$candidatureSpontanee = $pdo->query("SELECT * FROM candidature_spontanee")->fetchAll();

// Liste des sites pour le sélecteur de candidature spontanée
$sites = $pdo->query("SELECT id, nom, email_rh, ville FROM point_Carte WHERE email_rh IS NOT NULL AND email_rh != '' ORDER BY nom")->fetchAll();

// SEO
$pageTitle = "Recrutement – Offres d'emploi chez J'aime la Galette";
$pageDesc  = "Rejoignez J'aime la Galette ! Offres d'emploi dans nos ateliers de Bretagne, Normandie et Pays de la Loire : production, logistique, commercial et qualité.";

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page Recrutement -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/recrutement/recrutement.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <?php include PARTIALS . 'pourquoi-nous-rejoindre.php'; ?>

    <?php include PARTIALS . 'processus-recrutement.php'; ?>

    <?php include PARTIALS . 'offres-emploi.php'; ?>

    <?php include PARTIALS . 'formulaire-recrutement.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
