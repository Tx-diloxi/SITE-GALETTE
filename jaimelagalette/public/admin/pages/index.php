<?php
/**
 * Admin - Liste des pages
 *
 * Affiche la liste des pages modifiables du site.
 */

declare(strict_types=1);

// ---- Inclusion des dépendances ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';

// ---- Vérification d'authentification ----
admin_check_auth();

// ---- Liste des pages modifiables ----
$pages = [
    'home'        => ['label' => 'Accueil',              'url' => '/'],
    'le-groupe'   => ['label' => 'Le Groupe',            'url' => '/le-groupe'],
    'nos-produits'=> ['label' => 'Nos Produits',         'url' => '/nos-produits'],
    'savoir-faire'=> ['label' => 'Savoir-faire',         'url' => '/savoir-faire'],
    'rse'         => ['label' => 'RSE',                  'url' => '/rse'],
    'recrutement' => ['label' => 'Recrutement',          'url' => '/recrutement'],
    'faq'         => ['label' => 'FAQ',                  'url' => '/faq'],
    'footer'      => ['label' => 'Footer',               'url' => '/'],
    'mentions-legales' => ['label' => 'Mentions légales', 'url' => '/mentions-legales'],
    'confidentialite'  => ['label' => 'Confidentialité',  'url' => '/politique-confidentialite'],
    'cookies'          => ['label' => 'Cookies',          'url' => '/cookies'],
];

// ---- Affichage ----
// Inclusion de l'en-tête de la page d'administration
require_once __DIR__ . '/../layout/header.php';
?>
<!-- Contenu principal de la page -->
<main class="admin-main">
    <!-- Barre d'outils avec le titre -->
    <div class="admin-toolbar">
        <h1>Pages</h1>
    </div>

    <!-- Carte contenant la liste des pages -->
    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Boucle sur chaque page pour afficher une ligne -->
                <?php foreach ($pages as $slug => $infos): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($infos['label'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><a href="<?= htmlspecialchars($infos['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars($infos['url'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td>
                        <!-- Lien vers la page d'édition de la section -->
                        <a href="edit.php?page=<?= rawurlencode($slug) ?>" class="admin-btn admin-btn--small admin-btn--primary">Modifier</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <!-- Fin de la boucle sur les pages -->
            </tbody>
        </table>
    </div>
</main>
<!-- ---- -->
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
