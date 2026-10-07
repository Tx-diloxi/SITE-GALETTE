<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'mentions-legales'")->fetch();

$legal = $pdo->query("
    SELECT ls.id, ls.titre, ls.contenu, ls.ordre
    FROM partial_Legal pl
    JOIN legal_Section ls ON ls.partial_legal_id = pl.id
    WHERE pl.nom_page = 'mentions-legales'
    ORDER BY ls.ordre
")->fetchAll();

$pageTitle = "Mentions légales – J'aime la Galette";
$pageDesc  = "Mentions légales du site J'aime la Galette : éditeur, hébergement, propriété intellectuelle et protection des données personnelles.";

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<main>
    <?php include PARTIALS . 'intro.php'; ?>

    <?php $legalSections = $legal; ?>
    <?php include PARTIALS . 'legal.php'; ?>
</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
