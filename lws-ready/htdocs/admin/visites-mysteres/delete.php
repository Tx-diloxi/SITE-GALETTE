<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
require_once __DIR__ . '/../../../app/config/commercial.php';
admin_check_auth();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$inspection = $pdo->prepare("SELECT id FROM inspection WHERE id = :id");
$inspection->execute([':id' => $id]);
if (!$inspection->fetch()) {
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token invalide.';
    } else {
        try {
            $pdo->prepare("DELETE FROM inspection WHERE id = :id")->execute([':id' => $id]);
            $uploadDir = rtrim(INSPECTION_UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $id;
            if (is_dir($uploadDir)) {
                array_map('unlink', glob($uploadDir . '/*'));
                rmdir($uploadDir);
            }
            header('Location: /admin/visites-mysteres/list.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur lors de la suppression.';
        }
    }
}

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card" style="max-width:500px;margin:40px auto;text-align:center">
            <h2>Confirmer la suppression</h2>
            <p>Êtes-vous sûr de vouloir supprimer l'inspection #<?= $id ?> ? Cette action est irréversible.</p>
            <?php if (isset($error)): ?>
                <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <form method="post" style="display:flex;gap:12px;justify-content:center;margin-top:20px">
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <button type="submit" class="admin-btn admin-btn--danger">Oui, supprimer</button>
                <a href="/admin/visites-mysteres/view.php?id=<?= $id ?>" class="admin-btn">Annuler</a>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
