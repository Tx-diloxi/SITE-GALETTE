<?php
// Page 404 - J'aime la Galette
declare(strict_types=1);

// Envoie le code HTTP 404 pour les crawlers et navigateurs
http_response_code(404);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/page.php';

// Données globales du footer
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// SEO
$pageTitle = "404 – Page non trouvée | J'aime la Galette";
$pageDesc  = "La page que vous cherchez n'existe pas ou a été déplacée. Retournez à l'accueil pour découvrir nos crêpes et galettes bretonnes.";
$noIndex = 'noindex, follow';

include PARTIALS . 'head.php';
include PARTIALS . 'header.php';

// Chargement unique du style 404
static $page404StyleLoaded = false;
if (!$page404StyleLoaded): $page404StyleLoaded = true; ?>
<link rel="stylesheet" href="/assets/css/partials/404/404.css">
<?php endif; ?>

<main>
    <!-- Section page non trouvée -->
    <section id="page_404" class="section-404">
        <div class="conteneur_404">
            <!-- Code erreur 404 -->
            <h1 class="code_404">404</h1>
            <!-- Titre du message d'erreur -->
            <h2 class="titre_404">Oups ! Page introuvable</h2>
            <!-- Message d'explication -->
            <p class="message_404">
                La page que vous cherchez s'est perdue dans la cuisine ou n'a jamais existé.
            </p>
            <!-- Bouton de retour à l'accueil -->
            <div class="bouton_accueil_404">
                <a href="/">
                    <button>Retour à l'accueil</button>
                </a>
            </div>
        </div>
    </section>
</main>
<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
?>