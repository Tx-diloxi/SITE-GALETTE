<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int) ($_GET['id'] ?? 0);
$livreur = $pdo->prepare("SELECT id, nom, prenom FROM livreur WHERE id = :id");
$livreur->execute([':id' => $id]);
$livreur = $livreur->fetch();

if (!$livreur) {
    admin_redirect('livreurs/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token invalide.';
    } else {
        $pdo->prepare("DELETE FROM livreur WHERE id = :id")->execute([':id' => $id]);
        admin_redirect('livreurs/index.php');
    }
}

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card" style="max-width:500px;margin:40px auto;text-align:center">
            <h2>Confirmer la suppression</h2>
            <p>Supprimer <?= htmlspecialchars($livreur['prenom'] . ' ' . $livreur['nom'], ENT_QUOTES, 'UTF-8') ?> ? Les inspections associées seront aussi supprimées.</p>
            <?php if (isset($error)): ?><div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form method="post" style="display:flex;gap:12px;justify-content:center;margin-top:20px">
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <button type="submit" class="admin-btn admin-btn--danger">Oui, supprimer</button>
                <a href="/admin/livreurs/index.php" class="admin-btn">Annuler</a>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
