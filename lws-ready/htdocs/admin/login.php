<?php
/**
 * Page de connexion à l'administration
 */
declare(strict_types=1);

// -----------------------------------------------
// INITIALISATION DE LA SESSION
// -----------------------------------------------
session_name('jalg_admin');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// -----------------------------------------------
// REDIRECTION SI DÉJÀ CONNECTÉ
// -----------------------------------------------
if (!empty($_SESSION['auth_logged_in'])) {
    if ($_SESSION['auth_role'] === 'admin') {
        header('Location: /admin/index.php');
    } else {
        header('Location: /admin/commercial/visite-mystere/index.php');
    }
    exit;
}

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/helpers/page.php';

$error = '';
$expired = $_GET['expired'] ?? null;
if ($expired) {
    $error = 'Votre session a expiré. Veuillez vous reconnecter.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../app/config/admin.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $maxAttempts = (int)(getenv('LOGIN_MAX_ATTEMPTS') ?: 5);
    $lockoutMinutes = (int)(getenv('LOGIN_LOCKOUT_MINUTES') ?: 15);

    $attempts = $_SESSION['login_attempts'] ?? 0;
    $lockout_time = $_SESSION['login_lockout'] ?? 0;

    if ($lockout_time > time()) {
        $remaining = ceil(($lockout_time - time()) / 60);
        $error = "Trop de tentatives. Réessayez dans $remaining minute(s).";
    } else {
        $authenticated = false;

        if ($username === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
            session_regenerate_id(true);
            $_SESSION['auth_logged_in'] = true;
            $_SESSION['auth_role'] = 'admin';
            $_SESSION['auth_user'] = $username;
            $_SESSION['auth_user_id'] = null;
            $_SESSION['auth_last_activity'] = time();
            unset($_SESSION['login_attempts'], $_SESSION['login_lockout']);
            header('Location: /admin/index.php');
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, nom, prenom, mot_de_passe FROM commercial WHERE login = :login AND en_ligne = TRUE LIMIT 1");
        $stmt->execute([':login' => $username]);
        $commercial = $stmt->fetch();

        if ($commercial && password_verify($password, $commercial['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['auth_logged_in'] = true;
            $_SESSION['auth_role'] = 'commercial';
            $_SESSION['auth_user'] = $commercial['prenom'] . ' ' . $commercial['nom'];
            $_SESSION['auth_user_id'] = (int) $commercial['id'];
            $_SESSION['auth_last_activity'] = time();
            unset($_SESSION['login_attempts'], $_SESSION['login_lockout']);
            header('Location: /admin/commercial/visite-mystere/index.php');
            exit;
        }

        $attempts++;
        $_SESSION['login_attempts'] = $attempts;
        if ($attempts >= $maxAttempts) {
            $_SESSION['login_lockout'] = time() + ($lockoutMinutes * 60);
            $error = "Trop de tentatives. Réessayez dans $lockoutMinutes minute(s).";
        } else {
            $error = 'Identifiants incorrects.';
        }
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – J'aime la Galette</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,600;0,700;1,900&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/page/admin/admin.css">
</head>
<body class="admin-login-body">
    <div class="admin-login-container">
        <div class="admin-login-card">
            <img src="/assets/images/logo_jaimelagalette.png" alt="J'aime la Galette" class="admin-login-logo">
            <h1>Connexion</h1>
            <p class="admin-login-subtitle">Administration &amp; Commercial</p>
            <?php if ($error): ?>
                <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <form method="post" class="admin-login-form">
                <div class="admin-field">
                    <label for="username">Identifiant</label>
                    <input type="text" id="username" name="username" required autocomplete="username" autofocus>
                </div>
                <div class="admin-field">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--full">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
