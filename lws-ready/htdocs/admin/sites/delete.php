<?php
/**
 * Admin - Suppression d'un site
 *
 * Page de confirmation avant suppression d'un site.
 */

declare(strict_types=1);

// ---- Inclusion des dépendances ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';

// ---- Vérification d'authentification ----
admin_check_auth();

// Récupère l'ID du site depuis l'URL
$id = (int)($_GET['id'] ?? 0);
// Redirige vers la liste si l'ID est invalide
if ($id <= 0) {
    admin_redirect('admin/sites/index.php');
}

// Récupère les informations du site à supprimer
$stmt = $pdo->prepare("SELECT * FROM point_Carte WHERE id = :id");
$stmt->execute([':id' => $id]);
$site = $stmt->fetch();

// Redirige si le site n'existe pas en base
if (!$site) {
    admin_redirect('admin/sites/index.php');
}

// ---- Traitement du formulaire ----
// Vérifie si le formulaire de confirmation a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie la validité du token CSRF
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } else {
        // Supprime le site de la base de données
        $stmt = $pdo->prepare("DELETE FROM point_Carte WHERE id = :id");
        $stmt->execute([':id' => $id]);
        // Redirige vers la liste avec un message de confirmation
        admin_redirect('admin/sites/index.php?deleted=1');
    }
}
// Fin du traitement du formulaire

// ---- Affichage ----
// Inclusion de l'en-tête de la page d'administration
require_once __DIR__ . '/../layout/header.php';
?>
<!-- Contenu principal de la page -->
<main class="admin-main">
    <!-- Barre d'outils avec le titre et le bouton de retour -->
    <div class="admin-toolbar">
        <h1>Supprimer un site</h1>
        <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
    </div>

    <!-- Affiche un message d'erreur si le token CSRF est invalide -->
    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Carte de confirmation avec les détails du site -->
    <div class="admin-card">
        <h2>Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer le site suivant ?</p>
        <!-- Tableau récapitulatif du site à supprimer -->
        <table class="admin-table" style="margin-top:1rem;">
            <tr>
                <th>ID</th>
                <td><?= htmlspecialchars((string)$site['id'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>Nom</th>
                <td><?= htmlspecialchars($site['nom'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>Ville</th>
                <td><?= htmlspecialchars($site['ville'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($site['code_postal'], ENT_QUOTES, 'UTF-8') ?>)</td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= htmlspecialchars($site['type_site'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>Ouvert</th>
                <td><?= $site['est_ouvert'] ? 'Oui' : 'Non' ?></td>
            </tr>
        </table>
        <!-- Formulaire de confirmation avec token CSRF -->
        <form method="POST" style="margin-top:1rem;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="admin-btn-group">
                <button type="submit" class="admin-btn admin-btn--danger">Confirmer la suppression</button>
                <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Annuler</a>
            </div>
        </form>
    </div>
</main>
<!-- ---- -->
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
