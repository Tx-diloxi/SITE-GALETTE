<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int) ($_GET['id'] ?? 0);
$commercial = $pdo->prepare("SELECT * FROM commercial WHERE id = :id");
$commercial->execute([':id' => $id]);
$commercial = $commercial->fetch();

if (!$commercial) {
    admin_redirect('commerciaux/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token invalide.';
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';
        $telephone = trim($_POST['telephone'] ?? '');
        $en_ligne = !empty($_POST['en_ligne']);

        if (empty($nom) || empty($prenom) || empty($email) || empty($login)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email invalide.';
        } else {
            $check = $pdo->prepare("SELECT id FROM commercial WHERE (login = :login OR email = :email) AND id != :id");
            $check->execute([':login' => $login, ':email' => $email, ':id' => $id]);
            if ($check->fetch()) {
                $error = 'Ce login ou cet email existe déjà.';
            } else {
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE commercial SET nom=:nom, prenom=:prenom, email=:email, login=:login, mot_de_passe=:mot_de_passe, telephone=:telephone, en_ligne=:en_ligne WHERE id=:id");
                    $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':login' => $login, ':mot_de_passe' => $hash, ':telephone' => $telephone ?: null, ':en_ligne' => $en_ligne ? 1 : 0, ':id' => $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE commercial SET nom=:nom, prenom=:prenom, email=:email, login=:login, telephone=:telephone, en_ligne=:en_ligne WHERE id=:id");
                    $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':login' => $login, ':telephone' => $telephone ?: null, ':en_ligne' => $en_ligne ? 1 : 0, ':id' => $id]);
                }
                admin_redirect('commerciaux/index.php');
            }
        }
    }
}

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card" style="max-width:600px;margin:0 auto">
            <h1>Modifier un commercial</h1>
            <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <div class="admin-field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($commercial['prenom'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($commercial['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($commercial['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="login">Identifiant *</label>
                    <input type="text" name="login" id="login" value="<?= htmlspecialchars($commercial['login'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="mot_de_passe">Nouveau mot de passe <small>(laisser vide pour conserver)</small></label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe">
                </div>
                <div class="admin-field">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($commercial['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label>
                        <input type="checkbox" name="en_ligne" value="1" <?= $commercial['en_ligne'] ? 'checked' : '' ?>> Actif
                    </label>
                </div>
                <div style="display:flex;gap:12px;margin-top:20px">
                    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
                    <a href="/admin/commerciaux/index.php" class="admin-btn">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
