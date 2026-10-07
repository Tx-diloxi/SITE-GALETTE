<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$error = '';
$success = '';

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

        if (empty($nom) || empty($prenom) || empty($email) || empty($login) || empty($password)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email invalide.';
        } else {
            $check = $pdo->prepare("SELECT id FROM commercial WHERE login = :login OR email = :email");
            $check->execute([':login' => $login, ':email' => $email]);
            if ($check->fetch()) {
                $error = 'Ce login ou cet email existe déjà.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO commercial (nom, prenom, email, login, mot_de_passe, telephone, en_ligne) VALUES (:nom, :prenom, :email, :login, :mot_de_passe, :telephone, :en_ligne)");
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':login' => $login,
                    ':mot_de_passe' => $hash,
                    ':telephone' => $telephone ?: null,
                    ':en_ligne' => $en_ligne ? 1 : 0,
                ]);
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
            <h1>Ajouter un commercial</h1>
            <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <div class="admin-field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" required>
                </div>
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" name="nom" id="nom" required>
                </div>
                <div class="admin-field">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="admin-field">
                    <label for="login">Identifiant *</label>
                    <input type="text" name="login" id="login" required>
                </div>
                <div class="admin-field">
                    <label for="mot_de_passe">Mot de passe *</label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" required>
                </div>
                <div class="admin-field">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone">
                </div>
                <div class="admin-field">
                    <label>
                        <input type="checkbox" name="en_ligne" value="1" checked> Actif
                    </label>
                </div>
                <div style="display:flex;gap:12px;margin-top:20px">
                    <button type="submit" class="admin-btn admin-btn--primary">Créer</button>
                    <a href="/admin/commerciaux/index.php" class="admin-btn">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
