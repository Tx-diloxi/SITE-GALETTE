<?php
// Page FAQ - J'aime la Galette
declare(strict_types=1);

// Inclut les fichiers de configuration et helpers
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

// --- Données globales ---
$footer      = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
$footerLiens = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre")->fetchAll();
$footerLegal = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre")->fetchAll();

// --- Récupération des données FAQ ---

// Infos principales de la FAQ
$stmt = $pdo->query("SELECT * FROM partial_FAQ WHERE id = 1");
$faqData = $stmt->fetch();

// Catégories de questions
$stmtCategories = $pdo->query("SELECT * FROM categorie_FAQ WHERE partial_faq_id = 1 ORDER BY ordre ASC");
$categories = $stmtCategories->fetchAll();

// Questions pour chaque catégorie
$questionsParCategorie = [];

$stmtQuestions = $pdo->prepare("
    SELECT * FROM question_FAQ 
    WHERE categorie_faq_id = ? AND en_ligne = TRUE 
    ORDER BY ordre ASC
");
foreach ($categories as $categorie) {
    $stmtQuestions->execute([$categorie['id']]);
    $questionsParCategorie[$categorie['id']] = $stmtQuestions->fetchAll();
}

// SEO
$pageTitle = "FAQ – Questions fréquentes | J'aime la Galette";
$pageDesc  = "Toutes les réponses à vos questions sur les crêpes et galettes J'aime la Galette : additifs, livraison, points de vente, RSE, recrutement. Besoin d'aide ? Notre chatbot vous répond.";

include PARTIALS . 'head.php';

// Génération du JSON-LD FAQPage pour les rich snippets SEO
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [],
];
foreach ($questionsParCategorie as $catId => $questions) {
    foreach ($questions as $q) {
        $faqSchema['mainEntity'][] = [
            '@type' => 'Question',
            'name' => $q['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $q['reponse'],
            ],
        ];
    }
}
?>
<script type="application/ld+json"><?= json_encode($faqSchema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?></script>

<?php include PARTIALS . 'header.php'; ?>

<!-- Feuille de style spécifique à la page FAQ -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/page/faq/faq.css">

<main>

    <?php include PARTIALS . 'intro.php'; ?>

    <!-- Section FAQ principale -->
    <section id="faq">

        <!-- Titres de la section FAQ -->
        <div class="titre">
            <h3><?= htmlspecialchars($faqData['sous_titre'] ?? 'On vous dit tout', ENT_QUOTES, 'UTF-8') ?></h3>
            <h2><?= htmlspecialchars($faqData['titre'] ?? 'FOIRE AUX QUESTIONS', ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($faqData['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Barre de recherche -->
        <div class="faq-search">
            <div class="faq-search-wrapper">
                <!-- Icône de recherche -->
                <svg class="faq-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                    height="20">
                    <circle cx="11" cy="11" r="8" fill="none" stroke="currentColor" stroke-width="2" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" fill="none" stroke="currentColor" stroke-width="2" />
                </svg>
                <!-- Champ de recherche -->
                <input type="text" id="faq-search-input" class="faq-search-input"
                    placeholder="Rechercher une question (ex: additifs, livraison...)"
                    aria-label="Rechercher une question">
                <!-- Bouton d'effacement -->
                <button id="faq-search-clear" class="faq-search-clear" aria-label="Effacer la recherche">✕</button>
            </div>
            <!-- Compteur de résultats -->
            <p id="faq-search-result" class="faq-search-result"></p>
        </div>

        <!-- Grille des catégories et questions -->
        <div class="faq-grid" id="faq-grid">
            <?php
            // Boucle sur chaque catégorie de la FAQ
            foreach ($categories as $categorie): 
                $questions = $questionsParCategorie[$categorie['id']] ?? [];
                if (empty($questions)) continue;
            ?>
            <!-- Bloc d'une catégorie FAQ -->
            <div class="faq-categorie"
                data-categorie="<?= htmlspecialchars($categorie['titre'], ENT_QUOTES, 'UTF-8') ?>">
                <!-- Titre de la catégorie -->
                <h2 class="faq-categorie-titre"><?= htmlspecialchars($categorie['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
                <!-- Liste des questions de la catégorie -->
                <div class="faq-questions">
                    <?php
                    // Boucle sur chaque question de la catégorie
                    foreach ($questions as $question): ?>
                    <!-- Élément question/réponse -->
                    <div class="faq-item"
                        data-question="<?= htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8') ?>"
                        data-reponse="<?= htmlspecialchars($question['reponse'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Bouton de la question (accordéon) -->
                        <button class="faq-question" aria-expanded="false">
                            <span
                                class="faq-question-text"><?= htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8') ?></span>
                            <!-- Icône de chevron -->
                            <svg class="faq-question-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                width="20" height="20">
                                <polyline points="6 9 12 15 18 9" fill="none" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </button>
                        <!-- Panneau de réponse -->
                        <div class="faq-reponse" hidden>
                            <p><?= nl2br(htmlspecialchars($question['reponse'], ENT_QUOTES, 'UTF-8')) ?></p>
                        </div>
                    </div>
                    <?php
                    // Fin de boucle des questions
                    endforeach; ?>
                </div>
            </div>
            <?php
            // Fin de boucle des catégories
            endforeach; ?>
        </div>

        <!-- Message affiché quand aucun résultat de recherche -->
        <div id="faq-no-results" class="faq-no-results" style="display: none;">
            <p>Aucune question ne correspond à votre recherche.</p>
            <p>N'hésitez pas à poser votre question à notre assistant virtuel !</p>
        </div>

    </section>

</main>

<?php
include PARTIALS . 'footer.php';
include PARTIALS . 'chatbot-widget.php';
?>

<script>
// Script de la FAQ : accordéon et recherche en direct
(function() {
    // Éléments du DOM
    const searchInput = document.getElementById('faq-search-input');
    const searchClear = document.getElementById('faq-search-clear');
    const searchResult = document.getElementById('faq-search-result');
    const noResultsDiv = document.getElementById('faq-no-results');
    const faqGrid = document.getElementById('faq-grid');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqCategories = document.querySelectorAll('.faq-categorie');

    // Initialise le comportement d'accordéon pour les questions
    function initAccordion() {
        const questions = document.querySelectorAll('.faq-question');

        questions.forEach(question => {
            question.addEventListener('click', function() {
                const expanded = this.getAttribute('aria-expanded') === 'true';
                const reponse = this.nextElementSibling;

                this.setAttribute('aria-expanded', !expanded);
                reponse.hidden = expanded;
            });
        });
    }

    // Filtrer les questions selon le terme de recherche
    function searchQuestions() {
        const searchTerm = searchInput.value.toLowerCase().trim();

        if (searchTerm === '') {
            // Réinitialise l'affichage si le champ est vide
            faqCategories.forEach(cat => cat.style.display = '');
            faqItems.forEach(item => item.style.display = '');
            noResultsDiv.style.display = 'none';
            searchResult.textContent = '';
            return;
        }

        let visibleItems = 0;
        let visibleCategories = new Set();

        // Parcourt chaque question pour vérifier si elle correspond
        faqItems.forEach(item => {
            const question = item.getAttribute('data-question').toLowerCase();
            const reponse = item.getAttribute('data-reponse').toLowerCase();

            if (question.includes(searchTerm) || reponse.includes(searchTerm)) {
                item.style.display = '';
                visibleItems++;
                const categorie = item.closest('.faq-categorie');
                if (categorie) {
                    visibleCategories.add(categorie);
                }
            } else {
                item.style.display = 'none';
            }
        });

        // Affiche ou masque les catégories selon les résultats
        faqCategories.forEach(cat => {
            if (visibleCategories.has(cat)) {
                cat.style.display = '';
            } else {
                cat.style.display = 'none';
            }
        });

        // Affiche le message d'absence de résultat
        if (visibleItems === 0) {
            noResultsDiv.style.display = 'block';
            searchResult.textContent = '';
        } else {
            noResultsDiv.style.display = 'none';
            searchResult.textContent =
                `${visibleItems} résultat${visibleItems > 1 ? 's' : ''} trouvé${visibleItems > 1 ? 's' : ''}`;
        }
    }

    // Efface la recherche et réinitialise l'affichage
    function clearSearch() {
        searchInput.value = '';
        searchQuestions();
        searchInput.focus();
    }

    // Événements sur les éléments de recherche
    if (searchInput) {
        searchInput.addEventListener('input', searchQuestions);
    }
    if (searchClear) {
        searchClear.addEventListener('click', clearSearch);
    }

    // Lance l'initialisation de l'accordéon
    initAccordion();
})();
</script>