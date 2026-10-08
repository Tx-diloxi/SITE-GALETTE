<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int) ($_GET['id'] ?? 0);
$livreur = $pdo->prepare("SELECT * FROM livreur WHERE id = :id");
$livreur->execute([':id' => $id]);
$livreur = $livreur->fetch();

if (!$livreur) {
    admin_redirect('admin/livreurs/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token invalide.';
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $secteur = trim($_POST['secteur'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $en_ligne = !empty($_POST['en_ligne']);

        if (empty($nom) || empty($prenom)) {
            $error = 'Veuillez remplir le nom et le prénom.';
        } else {
            $stmt = $pdo->prepare("UPDATE livreur SET nom=:nom, prenom=:prenom, secteur=:secteur, telephone=:telephone, en_ligne=:en_ligne WHERE id=:id");
            $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':secteur' => $secteur ?: null, ':telephone' => $telephone ?: null, ':en_ligne' => $en_ligne ? 1 : 0, ':id' => $id]);
            admin_redirect('admin/livreurs/index.php');
        }
    }
}

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card" style="max-width:600px;margin:0 auto">
            <h1>Modifier un livreur</h1>
            <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <div class="admin-field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($livreur['prenom'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($livreur['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="secteur">Secteur</label>
                    <input type="text" name="secteur" id="secteur" value="<?= htmlspecialchars($livreur['secteur'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($livreur['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label>
                        <input type="checkbox" name="en_ligne" value="1" <?= $livreur['en_ligne'] ? 'checked' : '' ?>> Actif
                    </label>
                </div>
                <div style="display:flex;gap:12px;margin-top:20px">
                    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
                    <a href="/admin/livreurs/index.php" class="admin-btn">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
