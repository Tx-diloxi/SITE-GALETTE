<?php
/**
 * Admin FAQ management page
 *
 * Allows editing the FAQ section header (sous-titre, titre, contenu)
 * and provides quick links to categories and questions management.
 */
declare(strict_types=1); // Active le mode strict de typage en PHP

// --- Chargement des dépendances et authentification ---
require_once __DIR__ . '/../../../app/config/database.php'; // Connexion BDD
require_once __DIR__ . '/../../../app/config/admin.php';   // Fonctions d'administration
admin_check_auth(); // Vérifie que l'utilisateur est connecté en tant qu'admin

// --- Initialisation ---
$message = ''; // Message de notification (succès/erreur)

// --- Récupération de la section FAQ existante ---
$section = $pdo->query("SELECT * FROM partial_FAQ LIMIT 1")->fetch(); // Charge la première ligne de la table

// --- Traitement du formulaire d'enregistrement ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_section'])) {
    // Vérifie si le token CSRF est valide
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $message = admin_alert('Token CSRF invalide.', 'error');
    } else {
        // Récupère et nettoie les champs du formulaire
        $sous_titre = trim($_POST['sous_titre'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        // Vérifie si une section existe déjà (mise à jour) ou non (création)
        if ($section) {
            // Met à jour la section existante
            $upd = $pdo->prepare("UPDATE partial_FAQ SET sous_titre = ?, titre = ?, contenu = ? WHERE id = ?");
            $upd->execute([$sous_titre, $titre, $contenu, $section['id']]);
        } else {
            // Crée une nouvelle section
            $ins = $pdo->prepare("INSERT INTO partial_FAQ (sous_titre, titre, contenu) VALUES (?, ?, ?)");
            $ins->execute([$sous_titre, $titre, $contenu]);
        }
        // Recharge la section après modification
        $section = $pdo->query("SELECT * FROM partial_FAQ LIMIT 1")->fetch();
        $message = admin_alert('Section FAQ enregistrée.');
    }
    // Fin du traitement du formulaire
}

// --- Inclusion du haut de page ---
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils de la page -->
    <div class="admin-toolbar">
        <h1>FAQ – Administration</h1>
    </div>

    <!-- Affichage du message de notification -->
    <?= $message ?>

    <!-- --- Formulaire de modification de l'en-tête FAQ --- -->
    <div class="admin-card">
        <h2>Section d'en-tête FAQ</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <div class="admin-field">
                <label>Sous-titre</label>
                <input type="text" name="sous_titre" value="<?= htmlspecialchars($section['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
            </div>
            <div class="admin-field">
                <label>Titre</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
            </div>
            <div class="admin-field">
                <label>Contenu</label>
                <textarea name="contenu"><?= htmlspecialchars($section['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button type="submit" name="save_section" value="1" class="admin-btn admin-btn--primary">Enregistrer</button>
        </form>
    </div>

    <!-- --- Liens rapides vers la gestion des catégories et questions --- -->
    <div class="admin-card" style="display:flex;gap:1.5rem;flex-wrap:wrap">
        <div>
            <h2>Catégories</h2>
            <p style="font-size:0.875rem;color:var(--gris-text);margin-bottom:0.75rem">Gérer les catégories de questions</p>
            <a href="categories.php" class="admin-btn admin-btn--primary">Gérer les catégories</a>
        </div>
        <div>
            <h2>Questions</h2>
            <p style="font-size:0.875rem;color:var(--gris-text);margin-bottom:0.75rem">Gérer les questions et réponses</p>
            <a href="questions.php" class="admin-btn admin-btn--primary">Gérer les questions</a>
        </div>
    </div>
</main>
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
