<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';

admin_check_auth();

$page = $_GET['page'] ?? 'home';

$pages = [
    'home'        => ['label' => 'Accueil',      'url' => '/'],
    'le-groupe'   => ['label' => 'Le Groupe',    'url' => '/le-groupe'],
    'nos-produits'=> ['label' => 'Nos Produits', 'url' => '/nos-produits'],
    'savoir-faire'=> ['label' => 'Savoir-faire', 'url' => '/savoir-faire'],
    'rse'         => ['label' => 'RSE',           'url' => '/rse'],
    'recrutement' => ['label' => 'Recrutement',  'url' => '/recrutement'],
    'faq'         => ['label' => 'FAQ',          'url' => '/faq'],
    'footer'      => ['label' => 'Footer',       'url' => '/'],
    'mentions-legales' => ['label' => 'Mentions légales', 'url' => '/mentions-legales'],
    'confidentialite'  => ['label' => 'Confidentialité',  'url' => '/politique-confidentialite'],
    'cookies'          => ['label' => 'Cookies',          'url' => '/cookies'],
];

$section_map = [
    'home'        => ['hero', 'apropos_1', 'chiffres', 'valeurs', 'carte', 'engagements', 'partenaire', 'contact_section'],
    'le-groupe'   => ['intro_le-groupe', 'histoire', 'carte', 'valeurs', 'engagements', 'animation', 'livraison', 'contact_section'],
    'nos-produits'=> ['intro_nos-produits', 'transparence', 'label', 'contact_section'],
    'savoir-faire'=> ['intro_savoir-faire', 'savoirfaire', 'contact_section'],
    'rse'         => ['intro_rse', 'rse', 'apropos_2', 'partenaire', 'contact_section'],
    'recrutement' => ['intro_recrutement', 'recrutement', 'processus', 'offres'],
    'faq'         => ['intro_faq', 'faq'],
    'footer'      => ['footer'],
    'mentions-legales' => ['intro_mentions-legales', 'legal_mentions-legales'],
    'confidentialite'  => ['intro_confidentialite', 'legal_confidentialite'],
    'cookies'          => ['intro_cookies', 'legal_cookies'],
];

$section_labels = [
    'hero'              => 'Hero',
    'intro'             => 'Introduction',
    'apropos'           => 'À propos',
    'chiffres'          => 'Chiffres',
    'valeurs'           => 'Valeurs',
    'carte'             => 'Carte & points',
    'engagements'       => 'Engagements',
    'partenaire'        => 'Partenaires',
    'contact_section'   => 'Contact',
    'histoire'          => 'Histoire',
    'animation'         => 'Animation',
    'livraison'         => 'Livraison',
    'transparence'      => 'Transparence',
    'label'             => 'Labels',
    'savoirfaire'       => 'Savoir-faire',
    'recrutement'       => 'Pourquoi nous rejoindre',
    'processus'         => 'Processus',
    'offres'            => 'Offres d\'emploi',
    'faq'               => 'FAQ section titre',
    'rse'               => 'Actions RSE',
    'footer'            => 'Footer',
    'legal'             => 'Contenu légal',
];

if (!array_key_exists($page, $pages)) {
    $page = 'home';
}

$page_url = $pages[$page]['url'];
$sections = $section_map[$page] ?? [];
$saved = isset($_GET['saved']);

$active_section = $_GET['section'] ?? ($sections[0] ?? '');
$only_section = $_GET['only'] ?? '';

if ($only_section && in_array($only_section, $sections, true)) {
    $sections = [$only_section];
    $active_section = $only_section;
}

$section_anchors = [
    'hero' => 'hero',
    'intro' => 'intro',
    'apropos' => 'a_propos',
    'chiffres' => 'chiffres_groupe',
    'histoire' => 'histoire',
    'carte' => 'carte',
    'engagements' => 'engagements',
    'animation' => 'animation',
    'livraison' => 'livraison',
    'savoirfaire' => 'savoir-faire',
    'recrutement' => 'recrutement-pourquoi',
    'processus' => 'processus-recrutement',
    'offres' => 'offres-emploi',
    'valeurs' => 'valeurs',
    'partenaire' => 'partenaires',
    'transparence' => 'transparence',
    'label' => 'partenaires',
    'faq' => 'faq',
    'rse' => 'actions_rse',
    'contact_section' => 'contact',
    'footer' => '',
    'legal' => 'legal',
];
if ($active_section) {
    $base_active = $active_section === 'contact_section' ? 'contact_section' : explode('_', $active_section)[0];
    if (isset($section_anchors[$base_active])) {
        $page_url .= '#' . $section_anchors[$base_active];
    }
}

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <div>
            <a href="index.php" class="admin-btn admin-btn--small admin-btn--secondary">&larr; Retour</a>
            <h1 style="display:inline;margin-left:1rem;"><?= htmlspecialchars($pages[$page]['label'], ENT_QUOTES, 'UTF-8') ?></h1>
        </div>
        <div>
            <a href="<?= htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="admin-btn admin-btn--small admin-btn--primary" id="btn-preview-page">Voir la page</a>
        </div>
    </div>

    <?php if ($saved): ?>
    <div class="admin-alert admin-alert--success">Sauvegardé avec succès.</div>
    <?php endif; ?>

    <div class="admin-split">
        <div class="admin-form-col">
            <?php foreach ($sections as $sec): ?>
            <?php
                if ($sec === 'contact_section') {
                    $base_sec = 'contact_section';
                } else {
                    $base_sec = explode('_', $sec)[0];
                }
                $label = $section_labels[$base_sec] ?? $sec;
                $partial_file = __DIR__ . '/partials/' . $base_sec . '.php';
                $secId = str_replace('_', '-', $sec);
                $isOpen = ($active_section === $sec);
            ?>
            <?php $base_anchor = $section_anchors[$base_sec] ?? ''; ?>
            <div class="admin-section">
                <button class="admin-section-header" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" data-section="<?= htmlspecialchars($secId, ENT_QUOTES, 'UTF-8') ?>" data-anchor="<?= htmlspecialchars($base_anchor, ENT_QUOTES, 'UTF-8') ?>">
                    <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="admin-section-toggle">&#9660;</span>
                </button>
                <div class="admin-section-body <?= $isOpen ? 'admin-section-body--open' : '' ?>">
                    <?php if (file_exists($partial_file)): ?>
                        <?php
                        $section_param = $sec;
                        $section_id = null;
                        $nom_page = null;

                        if (str_starts_with($sec, 'intro_')) {
                            $nom_page = substr($sec, 6);
                            $base_sec = 'intro';
                        } elseif (str_starts_with($sec, 'legal_')) {
                            $nom_page = substr($sec, 6);
                            $base_sec = 'legal';
                        } elseif (str_starts_with($sec, 'apropos_')) {
                            $section_id = (int) substr($sec, 8);
                            $base_sec = 'apropos';
                        }

                        $sectionQueries = [
                            'hero' => ['table' => 'hero', 'id' => 1],
                            'intro' => ['table' => 'partial_Intro', 'where' => 'nom_page = :np'],
                            'legal' => ['table' => 'partial_Legal', 'where' => 'nom_page = :np'],
                            'apropos' => ['table' => 'partial_Apropos', 'where' => 'id = :id'],
                            'chiffres' => ['table' => 'partial_Chiffre_Groupe', 'id' => 1],
                            'valeurs' => ['table' => 'partial_Valeur', 'id' => 1],
                            'carte' => ['table' => 'partial_Carte', 'id' => 1],
                            'engagements' => ['table' => 'partial_Engagement', 'id' => 1],
                            'partenaire' => ['table' => 'partial_Partenaire', 'id' => 1],
                            'contact_section' => ['table' => 'partial_Contact', 'id' => 1],
                            'histoire' => ['table' => 'partial_Histoire', 'id' => 1],
                            'animation' => ['table' => 'partial_Animation', 'id' => 1],
                            'livraison' => ['table' => 'partial_Livraison', 'id' => 1],
                            'transparence' => ['table' => 'partial_Transparence', 'id' => 1],
                            'label' => ['table' => 'partial_Label', 'id' => 1],
                            'savoirfaire' => ['table' => 'partial_SavoirFaire', 'id' => 1],
                            'recrutement' => ['table' => 'partial_Recrutement', 'id' => 1],
                            'processus' => ['table' => 'partial_Processus', 'id' => 1],
                            'offres' => ['table' => 'partial_Offre', 'id' => 1],
                            'faq' => ['table' => 'partial_FAQ', 'id' => 1],
                            'rse' => ['empty' => true],
                            'footer' => ['table' => 'footer', 'id' => 1],
                        ];

                        $sectionData = [];
                        $queryDef = $sectionQueries[$base_sec] ?? null;

                        if ($queryDef && !isset($queryDef['empty'])) {
                            if (isset($queryDef['id'])) {
                                $stmt = $pdo->query("SELECT * FROM {$queryDef['table']} WHERE id = {$queryDef['id']}");
                                $sectionData = $stmt->fetch();
                                $section_id = $queryDef['id'];
                            } elseif ($queryDef['where'] === 'nom_page = :np') {
                                $stmt = $pdo->prepare("SELECT * FROM {$queryDef['table']} WHERE {$queryDef['where']}");
                                $stmt->execute([':np' => $nom_page]);
                                $sectionData = $stmt->fetch();
                                $section_id = $sectionData ? (int) $sectionData['id'] : 0;
                            } elseif ($queryDef['where'] === 'id = :id') {
                                $stmt = $pdo->prepare("SELECT * FROM {$queryDef['table']} WHERE {$queryDef['where']}");
                                $stmt->execute([':id' => $section_id ?? 1]);
                                $sectionData = $stmt->fetch();
                                $section_id = $sectionData ? (int) $sectionData['id'] : 0;
                            }
                        }

                        $sectionData = $sectionData ?: [];
                        $section_id = $section_id ?: 0;
                        ?>
                        <?php
                            $section = $sectionData;
                            $id = $section_id;
                            require $partial_file;
                        ?>
                    <?php else: ?>
                        <p class="admin-empty">Formulaire non trouvé : <?= htmlspecialchars($base_sec, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>


        <div class="admin-preview">
            <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;">
                <input type="text" id="preview-url" value="<?= htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8') ?>" readonly style="flex:1;padding:0.5rem;border:1px solid #ccc;border-radius:4px;font-size:0.875rem;">
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="document.getElementById('preview-frame').src = document.getElementById('preview-url').value;">Actualiser</button>
            </div>
            <iframe src="<?= htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8') ?>" id="preview-frame"></iframe>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
