<?php
// Page d'accueil - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/page.php';

// --- Données globales (footer, header, hero) ---
$hero        = $pdo->query("SELECT * FROM hero WHERE id = 1")->fetch();
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Section À propos ---
$apropos     = $pdo->query("SELECT * FROM partial_Apropos WHERE id = 1")->fetch();

// --- Section Chiffres du groupe ---
$chiffres    = $pdo->query("SELECT * FROM partial_Chiffre_Groupe WHERE id = 1")->fetch();
$cardsChiffres = $pdo->query("SELECT * FROM card_Chiffre_Groupe WHERE partial_chiffre_groupe_id = 1 ORDER BY id")->fetchAll();

// --- Section Valeurs ---
$valeurs     = $pdo->query("SELECT * FROM partial_Valeur WHERE id = 1")->fetch();
$cardsValeurs = $pdo->query("SELECT * FROM card_Valeur WHERE partial_valeur_id = 1 ORDER BY id")->fetchAll();

// --- Section Produits ---
$produits    = $pdo->query("SELECT * FROM partial_Produit WHERE id = 1")->fetch();
$cardsProduits = $pdo->query("
    SELECT cp.*, m.nom as marque_nom, m.slug as marque_slug, m.logo as marque_logo, m.couleur_hex as marque_couleur
    FROM card_Produit cp
    LEFT JOIN marque m ON cp.marque_id = m.id
    WHERE cp.partial_produit_id = 1
    ORDER BY cp.id
")->fetchAll();

// --- Section Carte ---
$carte       = $pdo->query("SELECT * FROM partial_Carte WHERE id = 1")->fetch();
$pointsCarte = $pdo->query("SELECT * FROM point_Carte WHERE partial_carte_id = 1 AND est_ouvert = TRUE ORDER BY id")->fetchAll();

// --- Section Partenaires ---
$partenaire     = $pdo->query("SELECT * FROM partial_Partenaire WHERE id = 1")->fetch();
$cardsPartenaire = $pdo->query("SELECT * FROM partenaire WHERE partial_partenaire_id = 1 ORDER BY ordre")->fetchAll();

// --- Section Engagements ---
$engagement     = $pdo->query("SELECT * FROM partial_Engagement WHERE id = 1")->fetch();
$cardsEngagement = $pdo->query("SELECT * FROM card_Engagement WHERE partial_engagement_id = 1 ORDER BY id")->fetchAll();

// --- Section Contact ---
$contact     = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// --- SEO et composition de la page ---
$pageTitle = "J'aime la Galette – Fabricant de crêpes et galettes bretonnes B2B";
$pageDesc  = "Fabricant breton de crêpes et galettes sans additifs ni conservateurs, livrées en ultra-frais. 3 ateliers (Broons, Alençon, Angers) au service des GMS, restauration collective et traiteurs.";

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>

<main>

    <?php include PARTIALS . 'hero.php'; ?>

    <?php include PARTIALS . 'apropos.php'; ?>

    <?php include PARTIALS . 'chiffres-groupe.php'; ?>

    <?php include PARTIALS . 'valeurs.php'; ?>

    <?php include PARTIALS . 'cta-produits.php'; ?>

    <?php include PARTIALS . 'carte.php'; ?>

    <?php include PARTIALS . 'partenaires.php'; ?>

    <?php include PARTIALS . 'engagements.php'; ?>

    <?php include PARTIALS . 'contact.php'; ?>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';