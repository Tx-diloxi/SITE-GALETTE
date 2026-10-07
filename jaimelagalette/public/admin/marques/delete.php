<?php
/**
 * Admin - Gestion des marques : suppression d'une marque.
 */
declare(strict_types=1);

// ----
// Initialisation
// ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// ----
// Récupération de la marque
// ----
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    admin_redirect('admin/marques/index.php');
}

// Requête SQL : charge les données de la marque à supprimer
$stmt = $pdo->prepare("SELECT * FROM marque WHERE id = :id");
$stmt->execute([':id' => $id]);
$marque = $stmt->fetch();

// Redirige si la marque n'existe pas
if (!$marque) {
    admin_redirect('admin/marques/index.php');
}

// Vérifie les dépendances : produits liés à cette marque
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produit WHERE marque_id = :id");
$stmt->execute([':id' => $id]);
$nb_produits = (int)$stmt->fetchColumn();

// Vérifie les dépendances : cartes produit liées à cette marque
$stmt = $pdo->prepare("SELECT COUNT(*) FROM card_Produit WHERE marque_id = :id");
$stmt->execute([':id' => $id]);
$nb_cards = (int)$stmt->fetchColumn();

// La marque est supprimable seulement si aucune dépendance n'existe
$has_dependencies = $nb_produits > 0 || $nb_cards > 0;

// --- Traitement du formulaire de confirmation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } elseif ($has_dependencies) {
        $error = 'Impossible de supprimer cette marque : ' . $nb_produits . ' produit(s) et ' . $nb_cards . ' carte(s) y sont lié(s). Réassignez-les d\'abord.';
    } else {
        // Requête SQL : supprime la marque
        $stmt = $pdo->prepare("DELETE FROM marque WHERE id = :id");
        $stmt->execute([':id' => $id]);
        // Redirige vers la liste avec un message de confirmation
        admin_redirect('admin/marques/index.php?deleted=1');
    }
}

// --- Affichage de la page ---
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils avec le titre et le lien retour -->
    <div class="admin-toolbar">
        <h1>Supprimer une marque</h1>
        <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
    </div>

    <!-- Affiche les erreurs éventuelles -->
    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Blocage si la marque a encore des produits ou cartes liés -->
    <?php if ($has_dependencies): ?>
        <div class="admin-card">
            <h2>Impossible de supprimer</h2>
            <div class="admin-alert admin-alert--warning">
                <strong><?= (int)($marque['nb_produits'] ?? $nb_produits) ?> produit(s)</strong> et
                <strong><?= (int)$nb_cards ?> carte(s) produit</strong> sont encore liés à cette marque.
            </div>
            <p>Réassignez d'abord ces éléments à une autre marque ou supprimez-les, puis réessayez.</p>
            <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
        </div>
    <?php else: ?>
        <!-- Confirmation de suppression avec récapitulatif de la marque -->
        <div class="admin-card">
            <h2>Confirmer la suppression</h2>
            <p>Êtes-vous sûr de vouloir supprimer la marque suivante ?</p>
            <table class="admin-table" style="margin-top:1rem;">
                <tr>
                    <th>ID</th>
                    <td><?= (int)$marque['id'] ?></td>
                </tr>
                <tr>
                    <th>Nom</th>
                    <td><?= htmlspecialchars($marque['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th>Slug</th>
                    <td><code><?= htmlspecialchars($marque['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
                </tr>
                <tr>
                    <th>En ligne</th>
                    <td><?= $marque['en_ligne'] ? 'Oui' : 'Non' ?></td>
                </tr>
            </table>
            <form method="POST" style="margin-top:1rem;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="admin-btn-group">
                    <button type="submit" class="admin-btn admin-btn--danger">Confirmer la suppression</button>
                    <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Annuler</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</main>
<!-- Inclusion du pied de page de l'administration -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
