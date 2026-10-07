<?php
// Page Le Groupe - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Intro ---
$intro = $pdo->query("SELECT * FROM partial_Intro WHERE nom_page = 'le-groupe'")->fetch();

// --- Section Histoire ---
$titreHistoire  = $pdo->query("SELECT * FROM partial_Histoire")->fetch();
$etapesHistoire = $pdo->query("SELECT * FROM etape_Histoire ORDER BY ordre")->fetchAll();

// --- Section Carte ---
$carte       = $pdo->query("SELECT * FROM partial_Carte WHERE id = 1")->fetch();
$pointsCarte = $pdo->query("SELECT * FROM point_Carte WHERE partial_carte_id = 1 AND est_ouvert = TRUE ORDER BY id")->fetchAll();

// --- Section Valeurs ---
$valeurs     = $pdo->query("SELECT * FROM partial_Valeur WHERE id = 1")->fetch();
$cardsValeurs = $pdo->query("SELECT * FROM card_Valeur WHERE partial_valeur_id = 1 ORDER BY id")->fetchAll();

// --- Section Engagements ---
$engagement      = $pdo->query("SELECT * FROM partial_Engagement WHERE id = 1")->fetch();
$cardsEngagement = $pdo->query("SELECT * FROM card_Engagement WHERE partial_engagement_id = 1 ORDER BY id")->fetchAll();

// --- Section Actions RSE ---
$actions_rse = $pdo->query("SELECT * FROM action_RSE ORDER BY ordre")->fetchAll();

// --- Section Animation ---
$animation      = $pdo->query("SELECT * FROM partial_Animation WHERE id = 1")->fetch();
$cardsAnimation = $pdo->query("SELECT * FROM card_Animation WHERE partial_animation_id = 1 ORDER BY id")->fetchAll();

// --- Section Livraison ---
$livraison = $pdo->query("SELECT * FROM partial_Livraison WHERE id = 1")->fetch();

// --- Section Contact ---
$contact     = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// SEO
$pageTitle = "Le Groupe J'aime la Galette – Fabricant breton de crêpes et galettes";
$pageDesc  = "Découvrez le groupe J'aime la Galette : 3 ateliers à Broons (22), Alençon (61) et Angers (49), 0 additif, 0 conservateur. Histoire, valeurs, équipe et savoir-faire artisanal.";

// --- JSON-LD LocalBusiness for each production site ---
$localBusinessSchemas = [];
foreach ($pointsCarte as $point) {
    if (!empty($point['nom'])) {
        $lb = [
            '@context' => 'https://schema.org',
            '@type' => 'FoodEstablishment',
            'name' => $point['nom'],
            'description' => 'Atelier de fabrication de crêpes et galettes bretonnes',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $point['adresse'] ?? '',
                'addressLocality' => $point['ville'] ?? '',
                'postalCode' => $point['code_postal'] ?? '',
                'addressCountry' => 'FR',
            ],
        ];
        if (!empty($point['telephone'])) {
            $lb['telephone'] = $point['telephone'];
        }
        if (!empty($point['email'])) {
            $lb['email'] = $point['email'];
        }
        $localBusinessSchemas[] = $lb;
    }
}

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<!-- Feuille de style spécifique à la page Le Groupe -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/le-groupe/le-groupe.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <?php include PARTIALS . 'histoire.php'; ?>

    <?php include PARTIALS . 'carte.php'; ?>

    <?php include PARTIALS . 'valeurs.php'; ?>

    <?php include PARTIALS . 'engagements.php'; ?>

    <?php include PARTIALS . 'actions-rse.php'; ?>

    <?php include PARTIALS . 'animation.php'; ?>

    <?php include PARTIALS . 'livraison.php'; ?>

    <!-- JSON-LD LocalBusiness -->
    <?php if (!empty($localBusinessSchemas)): ?>
    <?php foreach ($localBusinessSchemas as $lbs): ?>
    <script type="application/ld+json"><?= json_encode($lbs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php include PARTIALS . 'contact.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';