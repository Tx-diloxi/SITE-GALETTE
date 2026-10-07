<?php
/**
 * Admin - Gestion des produits
 * Page de confirmation de suppression d'un produit
 */
declare(strict_types=1);

// ---- Initialisation ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    admin_redirect('admin/produits/index.php');
}

$stmt = $pdo->prepare("SELECT p.*, m.nom AS marque_nom FROM produit p LEFT JOIN marque m ON p.marque_id = m.id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$produit = $stmt->fetch();

if (!$produit) {
    admin_redirect('admin/produits/index.php');
}

// ---- Traitement du formulaire de confirmation ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM produit WHERE id = :id");
        $stmt->execute([':id' => $id]);
        admin_redirect('admin/produits/index.php?deleted=1');
    }
}

// ---- Affichage de la page ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Supprimer un produit</h1>
        <a href="/admin/produits/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <h2>Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer le produit suivant ?</p>
        <table class="admin-table" style="margin-top:1rem;">
            <tr>
                <th>ID</th>
                <td><?= (int)$produit['id'] ?></td>
            </tr>
            <tr>
                <th>Nom</th>
                <td><?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>Marque</th>
                <td><?= htmlspecialchars($produit['marque_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>En ligne</th>
                <td><?= $produit['en_ligne'] ? 'Oui' : 'Non' ?></td>
            </tr>
        </table>
        <p style="margin-top:1rem;color:#856404;background:#fff3cd;padding:0.75rem;border-radius:4px;">
            Cette action supprimera également les données associées (À propos). Cette action est irréversible.
        </p>
        <form method="POST" style="margin-top:1rem;">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <div class="admin-btn-group">
                <button type="submit" class="admin-btn admin-btn--danger">Confirmer la suppression</button>
                <a href="/admin/produits/index.php" class="admin-btn admin-btn--secondary">Annuler</a>
            </div>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
