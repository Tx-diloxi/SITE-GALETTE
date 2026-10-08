<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    admin_redirect('admin/contacts/index.php');
}

$stmt = $pdo->prepare("SELECT id, nom, sujet FROM formulaire_contact WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if (!$m) {
    admin_redirect('admin/contacts/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } else {
        $pdo->prepare("DELETE FROM formulaire_contact WHERE id = ?")->execute([$id]);
        admin_redirect('admin/contacts/index.php?deleted=1');
    }
}

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Supprimer un message</h1>
        <a href="view.php?id=<?= $id ?>" class="admin-btn admin-btn--secondary">Retour au message</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="admin-card" style="max-width:600px;margin:40px auto;text-align:center">
        <h2>Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer définitivement le message de <strong><?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?></strong> (sujet&nbsp;: «&nbsp;<?= htmlspecialchars($m['sujet'], ENT_QUOTES, 'UTF-8') ?>&nbsp;») ?</p>
        <p style="color:#856404;background:#fff3cd;padding:0.75rem;border-radius:4px;margin-top:1rem;">Cette action est irréversible.</p>
        <form method="POST" style="display:flex;gap:12px;justify-content:center;margin-top:1.5rem">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <button type="submit" class="admin-btn admin-btn--danger">Oui, supprimer définitivement</button>
            <a href="view.php?id=<?= $id ?>" class="admin-btn">Annuler</a>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
