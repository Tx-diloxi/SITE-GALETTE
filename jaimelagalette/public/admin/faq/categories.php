<?php
/**
 * Admin FAQ categories management page
 *
 * Allows adding, editing, and deleting FAQ categories.
 */
declare(strict_types=1); // Active le mode strict de typage en PHP

// --- Chargement des dépendances et authentification ---
require_once __DIR__ . '/../../../app/config/database.php'; // Connexion BDD
require_once __DIR__ . '/../../../app/config/admin.php';   // Fonctions d'administration
admin_check_auth(); // Vérifie que l'utilisateur est connecté en tant qu'admin

// --- Initialisation ---
$message = ''; // Message de notification

// --- Récupération de la section FAQ existante ---
$partialFaq = $pdo->query("SELECT id FROM partial_FAQ LIMIT 1")->fetch(); // Vérifie qu'une section FAQ existe
$partialFaqId = $partialFaq ? (int)$partialFaq['id'] : 0; // ID de la section (0 si inexistante)

// --- Traitement des actions du formulaire (ajout, modification, suppression) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie la validité du token CSRF
    if (!admin_csrf_verify($token)) {
        $message = admin_alert('Token CSRF invalide.', 'error');
    } elseif (isset($_POST['add']) && $partialFaqId > 0) {
        // --- Ajout d'une catégorie ---
        $titre = trim($_POST['titre'] ?? '');
        $ordre = (int)($_POST['ordre'] ?? 0);
        if ($titre !== '') {
            // Insère la nouvelle catégorie liée à la section FAQ
            $ins = $pdo->prepare("INSERT INTO categorie_FAQ (partial_faq_id, titre, ordre) VALUES (?, ?, ?)");
            $ins->execute([$partialFaqId, $titre, $ordre]);
            $message = admin_alert('Catégorie ajoutée.');
        }
    } elseif (isset($_POST['edit'])) {
        // --- Modification d'une catégorie existante ---
        $id = (int)($_POST['id'] ?? 0);
        $titre = trim($_POST['titre'] ?? '');
        $ordre = (int)($_POST['ordre'] ?? 0);
        if ($id > 0 && $titre !== '') {
            $upd = $pdo->prepare("UPDATE categorie_FAQ SET titre = ?, ordre = ? WHERE id = ?");
            $upd->execute([$titre, $ordre, $id]);
            $message = admin_alert('Catégorie modifiée.');
        }
    } elseif (isset($_POST['delete'])) {
        // --- Suppression d'une catégorie ---
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $del = $pdo->prepare("DELETE FROM categorie_FAQ WHERE id = ?");
            $del->execute([$id]);
            $message = admin_alert('Catégorie supprimée.');
        }
    }
    // Fin du traitement POST
}

// --- Chargement de toutes les catégories ---
$categories = $pdo->query("SELECT id, titre, ordre FROM categorie_FAQ ORDER BY ordre ASC, id ASC")->fetchAll();

// --- Inclusion du haut de page ---
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils -->
    <div class="admin-toolbar">
        <h1>Catégories FAQ</h1>
        <a href="index.php" class="admin-btn admin-btn--secondary">Retour</a>
    </div>

    <!-- Affichage du message de notification -->
    <?= $message ?>

    <!-- Vérifie si une section FAQ existe avant d'afficher le contenu -->
    <?php if ($partialFaqId <= 0): ?>
    <!-- Message d'erreur si aucune section FAQ n'a été créée -->
    <div class="admin-card">
        <p class="admin-empty">Aucune section FAQ trouvée. Créez d'abord une section sur la page d'accueil FAQ.</p>
    </div>
    <?php else: ?>

    <!-- --- Formulaire d'ajout de catégorie --- -->
    <div class="admin-card">
        <h2>Ajouter une catégorie</h2>
        <form method="POST" style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <div class="admin-field" style="flex:2;min-width:200px">
                <label>Titre</label>
                <input type="text" name="titre" required maxlength="255">
            </div>
            <div class="admin-field" style="flex:0 0 80px">
                <label>Ordre</label>
                <input type="number" name="ordre" value="0" min="0">
            </div>
            <button type="submit" name="add" value="1" class="admin-btn admin-btn--primary">Ajouter</button>
        </form>
    </div>

    <!-- --- Tableau listant toutes les catégories avec édition inline --- -->
    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th style="width:80px">Ordre</th>
                    <th style="width:200px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <!-- Message si aucune catégorie -->
                <tr><td colspan="3" class="admin-empty">Aucune catégorie.</td></tr>
                <?php endif; ?>
                <!-- Boucle sur chaque catégorie pour afficher une ligne éditable -->
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <form method="POST" style="display:contents">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)(int)$cat['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <td><input type="text" name="titre" value="<?= htmlspecialchars($cat['titre'], ENT_QUOTES, 'UTF-8') ?>" required style="width:100%;padding:4px 6px;border:1px solid #ddd;border-radius:4px;font-size:0.875rem"></td>
                        <td><input type="number" name="ordre" value="<?= htmlspecialchars((string)(int)$cat['ordre'], ENT_QUOTES, 'UTF-8') ?>" min="0" style="width:60px;padding:4px 6px;border:1px solid #ddd;border-radius:4px;font-size:0.875rem"></td>
                        <td>
                            <div class="admin-btn-group">
                                <button type="submit" name="edit" value="1" class="admin-btn admin-btn--small admin-btn--primary">Enregistrer</button>
                                <button type="submit" name="delete" value="1" class="admin-btn admin-btn--small admin-btn--danger" data-confirm="Supprimer cette catégorie ?">Supprimer</button>
                            </div>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
                <!-- Fin de la boucle sur les catégories -->
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <!-- Fin du bloc conditionnel -->
</main>
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
