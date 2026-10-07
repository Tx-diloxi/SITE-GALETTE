<?php
/**
 * Admin - Gestion des marques : liste des marques.
 */
declare(strict_types=1);

// ----
// Initialisation
// ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// ----
// Récupération des marques
// ----
$marques = $pdo->query("
    SELECT m.*,
        (SELECT COUNT(*) FROM produit p WHERE p.marque_id = m.id) AS nb_produits
    FROM marque m
    ORDER BY m.ordre, m.nom
")->fetchAll();

// ----
// Affichage de la page
// ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils avec le titre et le bouton d'ajout -->
    <div class="admin-toolbar">
        <h1>Marques</h1>
        <a href="/admin/marques/create.php" class="admin-btn admin-btn--primary">Nouvelle marque</a>
    </div>

    <!-- Messages de confirmation après sauvegarde ou suppression -->
    <?php if (isset($_GET['saved'])): ?>
        <div class="admin-alert admin-alert--success">Marque enregistrée.</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="admin-alert admin-alert--success">Marque supprimée.</div>
    <?php endif; ?>

    <div class="admin-card">
        <!-- Vérifie s'il y a des marques à afficher -->
        <?php if (empty($marques)): ?>
            <p class="admin-empty">Aucune marque trouvée.</p>
        <?php else: ?>
            <!-- Tableau listant toutes les marques -->
            <table class="admin-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>En ligne</th>
                        <th>Produits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Boucle sur chaque marque pour afficher ses données -->
                    <?php foreach ($marques as $m): ?>
                    <tr>
                        <td>
                            <!-- Affiche le logo de la marque ou un carré de couleur si pas de logo -->
                            <?php if (!empty($m['logo'])): ?>
                                <img src="<?= htmlspecialchars($m['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb">
                            <?php else: ?>
                                <span style="display:inline-block;width:48px;height:48px;background:<?= htmlspecialchars($m['couleur_hex'] ?? '#EE7325', ENT_QUOTES, 'UTF-8') ?>;border-radius:4px;"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>
                        <td><code><?= htmlspecialchars($m['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
                        <td>
                            <!-- Formulaire de bascule en ligne/hors ligne (toggle) -->
                            <form class="admin-toggle-form" action="/admin/marques/toggle.php" method="POST">
                                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="container">
                                    <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="marques-toggle-<?= $m['id'] ?>" <?= $m['en_ligne'] ? 'checked' : '' ?>>
                                    <label class="switch" for="marques-toggle-<?= $m['id'] ?>">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </form>
                        </td>
                        <!-- Affiche le nombre de produits liés à cette marque -->
                        <td><span class="admin-badge admin-badge--info"><?= (int)$m['nb_produits'] ?></span></td>
                        <td>
                            <!-- Boutons d'action : modifier ou supprimer la marque -->
                            <div class="admin-actions">
                                <a href="/admin/marques/edit.php?id=<?= (int)$m['id'] ?>" class="admin-btn admin-btn--small admin-btn--secondary">Modifier</a>
                                <a href="/admin/marques/delete.php?id=<?= (int)$m['id'] ?>" class="admin-btn admin-btn--small admin-btn--danger" data-confirm="Supprimer cette marque ?">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- Fin de boucle sur les marques -->
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
<!-- Inclusion du pied de page de l'administration -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
