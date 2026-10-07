<?php
// Page Atelier / Site de production - J'aime la Galette
declare(strict_types=1);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';
require_once HELPERS . 'horaires.php';
require_once HELPERS . 'seo.php';

// Parse l'URL pour extraire l'ID de l'atelier
$url = $_GET['url'] ?? '';
preg_match('#^atelier/(\d+)#', $url, $matches);
$atelierId = isset($matches[1]) ? (int)$matches[1] : 0;

if ($atelierId === 0) {
    http_response_code(404);
    require_once APP_ROOT . 'pages/404.php';
    exit;
}

// Récupère les données de l'atelier
$stmt = $pdo->prepare("SELECT * FROM point_Carte WHERE id = ? AND est_ouvert = TRUE");
$stmt->execute([$atelierId]);
$atelier = $stmt->fetch();

if (!$atelier) {
    http_response_code(404);
    require_once APP_ROOT . 'pages/404.php';
    exit;
}

// Récupère les horaires
$horaires = site_get_horaires($pdo, $atelierId);
$estOuvert = site_est_ouvert($pdo, $atelierId);
$horairesText = site_format_horaires_text($horaires);
$currentDay = (int)(new DateTime('now', new DateTimeZone('Europe/Paris')))->format('w');

// Synthétise $intro pour le partial intro.php
$intro = [
    'nom_page' => $atelier['nom'],
    'sous_titre' => $atelier['type_site'] ?? 'Site de production',
    'titre' => $atelier['nom'],
    'citation' => '',
    'contenu' => $atelier['adresse'] . ', ' . $atelier['code_postal'] . ' ' . $atelier['ville'],
    'image_mascotte' => null,
    'alt_mascotte' => '',
];

// Prépare $pointsCarte pour le partial carte.php (un seul point)
$carte = $pdo->query("SELECT * FROM partial_Carte WHERE id = 1")->fetch();
$pointsCarte = [$atelier];

// Récupère les offres d'emploi liées à cet atelier
$offresEmploi = $pdo->prepare("SELECT * FROM offre_Emploi WHERE point_carte_id = ? AND en_ligne = TRUE ORDER BY date_publication DESC");
$offresEmploi->execute([$atelierId]);
$offresEmploi = $offresEmploi->fetchAll();

// Section contact
$contact = $pdo->query("SELECT * FROM partial_Contact WHERE id = 1")->fetch();
$cardsContact = $pdo->query("SELECT * FROM card_Contact WHERE partial_contact_id = 1 ORDER BY id")->fetchAll();

// Données globales du footer
$footer = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// SEO
$typeLabel = $atelier['type_site'] ?? 'Site de production';
$pageTitle = htmlspecialchars($atelier['nom']) . " – " . $typeLabel . " | J'aime la Galette";
$pageDesc = $typeLabel . " " . htmlspecialchars($atelier['nom']) . " à " . htmlspecialchars($atelier['ville']) . " (" . htmlspecialchars($atelier['code_postal']) . "). Fabrication de crêpes et galettes bretonnes sans additifs ni conservateurs.";

// JSON-LD LocalBusiness
$localBusinessSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FoodEstablishment',
    'name' => $atelier['nom'],
    'description' => strip_tags($pageDesc),
    'url' => (($proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'www.jaimelagalette.com') . $_SERVER['REQUEST_URI']),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $atelier['adresse'] ?? '',
        'addressLocality' => $atelier['ville'] ?? '',
        'postalCode' => $atelier['code_postal'] ?? '',
        'addressCountry' => 'FR',
    ],
];
if (!empty($atelier['telephone'])) $localBusinessSchema['telephone'] = $atelier['telephone'];
if (!empty($atelier['email'])) $localBusinessSchema['email'] = $atelier['email'];

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/atelier/atelier.css">

<main>
    <?php include PARTIALS . 'intro.php'; ?>

    <section id="atelier-infos">
        <div class="titre">
            <h3>Contact & horaires</h3>
            <h2>Nous Contacter</h2>
        </div>
        <div class="grid-2">
            <?php if (!empty($atelier['telephone'])): ?>
            <div class="card">
                <span>Téléphone</span>
                <a
                    href="tel:<?= htmlspecialchars($atelier['telephone']) ?>"><?= htmlspecialchars($atelier['telephone']) ?></a>
            </div>
            <?php endif; ?>
            <?php if (!empty($atelier['email'])): ?>
            <div class="card">
                <span>Email</span>
                <a
                    href="mailto:<?= htmlspecialchars($atelier['email']) ?>"><?= htmlspecialchars($atelier['email']) ?></a>
            </div>
            <?php endif; ?>
            <?php if (!empty($horaires)): ?>
            <div class="card">
                <span>Horaires</span>
                <div class="horaires-list">
                    <?php foreach ($horaires as $h): ?>
                    <div class="horaire-row <?= (int)$h['jour'] === $currentDay ? 'aujourdhui' : '' ?>">
                        <span><?= htmlspecialchars($h['jour_label']) ?></span>
                        <span><?= htmlspecialchars($h['ouverture']) ?>–<?= htmlspecialchars($h['fermeture']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($atelier['email_rh'])): ?>
            <div class="card">
                <span>Recrutement</span>
                <a
                    href="mailto:<?= htmlspecialchars($atelier['email_rh']) ?>"><?= htmlspecialchars($atelier['email_rh']) ?></a>
            </div>
            <?php endif; ?>
        </div>
        <div class="atelier-status-badge <?= $estOuvert ? 'ouvert' : 'ferme' ?>">
            <?= $estOuvert ? 'Ouvert actuellement' : 'Fermé pour le moment' ?>
        </div>
        <!-- Image décorative de vague en bas de section -->
        <img src="/assets/images/vague4.svg" class="vague-transition" alt="Image d'une vague stylisée">

    </section>

    <?php include PARTIALS . 'carte.php'; ?>

    <?php if (!empty($offresEmploi)): ?>
    <section id="atelier-offres">
        <div class="titre">
            <h3>Offres d'emploi</h3>
            <h2>Rejoignez-nous</h2>
        </div>
        <div class="offres-grid">
            <?php foreach ($offresEmploi as $offre): ?>
            <div class="card">
                <span><?= htmlspecialchars($offre['titre']) ?></span>
                <p><?= htmlspecialchars($offre['contrat']) ?> – <?= htmlspecialchars($offre['lieu']) ?></p>
                <a href="/recrutement#offre-<?= (int)$offre['id'] ?>"><button>Voir l'offre</button></a>
            </div>
            <?php endforeach; ?>
        </div>
        <img src="/assets/images/vague3.svg" class="vague" alt="Vague décorative">
    </section>
    <?php endif; ?>

    <?php include PARTIALS . 'contact.php'; ?>

    <script type="application/ld+json">
    <?= json_encode($localBusinessSchema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>
</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';