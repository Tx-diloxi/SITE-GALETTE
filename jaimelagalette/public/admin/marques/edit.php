<?php
/**
 * Admin - Gestion des marques : édition d'une marque.
 */
declare(strict_types=1);

// ----
// Initialisation
// ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// ----
// Récupération de la marque
// ----
$id = (int)($_GET['id'] ?? 0);
// Redirige vers la liste si l'ID est invalide
if ($id <= 0) {
    admin_redirect('admin/marques/index.php');
}

// Requête SQL : charge les données de la marque depuis la base
$stmt = $pdo->prepare("SELECT * FROM marque WHERE id = :id");
$stmt->execute([':id' => $id]);
$marque = $stmt->fetch();

// Redirige si la marque n'existe pas
if (!$marque) {
    admin_redirect('admin/marques/index.php');
}

$error = null;

// --- Traitement du formulaire ---
// Vérifie si le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie la validité du token CSRF
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } elseif (empty(trim($_POST['nom'] ?? ''))) {
        $error = 'Le nom est requis.';
    } else {
        $new_slug = !empty($_POST['slug']) ? $_POST['slug'] : $marque['slug'];

        // Vérifie que le slug est unique (sauf pour la marque en cours d'édition)
        $check = $pdo->prepare("SELECT COUNT(*) FROM marque WHERE slug = :slug AND id != :id");
        $check->execute([':slug' => $new_slug, ':id' => $id]);
        if ($check->fetchColumn() > 0) {
            $error = 'Ce slug est déjà utilisé par une autre marque.';
        }
    }

    // Si aucune erreur, procède à la mise à jour
    if (empty($error)) {
        // Télécharge un nouveau logo si fourni
        $logo_url = admin_upload_image($_FILES['logo'] ?? []);

        // Démarre une transaction pour mettre à jour les deux tables
        $pdo->beginTransaction();
        try {
            // Requête SQL : met à jour les champs de la marque
            $stmt = $pdo->prepare("
                UPDATE marque SET
                    nom = :nom, slug = :slug,
                    logo = COALESCE(NULLIF(:logo, ''), logo),
                    alt_logo = :alt_logo, description = :description,
                    couleur_hex = :couleur_hex, en_ligne = :en_ligne, ordre = :ordre
                WHERE id = :id
            ");
            $stmt->execute([
                ':nom' => $_POST['nom'],
                ':slug' => $new_slug,
                ':logo' => $logo_url ?? '',
                ':alt_logo' => $_POST['alt_logo'] ?? '',
                ':description' => $_POST['description'] ?? '',
                ':couleur_hex' => $_POST['couleur_hex'] ?? '#EE7325',
                ':en_ligne' => (int)(!empty($_POST['en_ligne'])),
                ':ordre' => (int)($_POST['ordre'] ?? 0),
                ':id' => $id,
            ]);

            // Si le slug a changé, met à jour la référence dans partial_Intro
            if ($new_slug !== $marque['slug']) {
                $stmt = $pdo->prepare("UPDATE partial_Intro SET nom_page = :nom_page WHERE nom_page = :old_nom_page");
                $stmt->execute([
                    ':nom_page' => 'nos-produits/' . $new_slug,
                    ':old_nom_page' => 'nos-produits/' . $marque['slug'],
                ]);
            }

            // Valide la transaction
            $pdo->commit();
            // Redirige vers la même page avec un message de confirmation
            admin_redirect('admin/marques/edit.php?id=' . $id . '&saved=1');
        } catch (Exception $e) {
            // En cas d'erreur, annule la transaction
            $pdo->rollBack();
            error_log('Erreur sauvegarde marque : ' . $e->getMessage());
            $error = 'Erreur lors de la sauvegarde.';
        }
    }
}

// ----
// Affichage de la page
// ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils avec le nom de la marque et les actions -->
    <div class="admin-toolbar">
        <h1>Modifier : <?= htmlspecialchars($marque['nom'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="admin-btn-group">
            <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            <a href="/admin/marques/delete.php?id=<?= (int)$id ?>" class="admin-btn admin-btn--danger">Supprimer</a>
        </div>
    </div>

    <!-- Message de confirmation après sauvegarde -->
    <?php if (isset($_GET['saved'])): ?>
        <div class="admin-alert admin-alert--success">Marque enregistrée.</div>
    <?php endif; ?>
    <!-- Message d'erreur en cas de problème -->
    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Formulaire d'édition avec upload de fichier possible -->
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-card">
            <!-- Champ nom -->
            <div class="admin-field">
                <label for="nom">Nom *</label>
                <input type="text" name="nom" id="slug-source" value="<?= htmlspecialchars($marque['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <!-- Champ slug (modifiable, met à jour partial_Intro si changé) -->
            <div class="admin-field">
                <label for="slug">Slug</label>
                <input type="text" name="slug" id="slug-target" value="<?= htmlspecialchars($marque['slug'], ENT_QUOTES, 'UTF-8') ?>">
                <small style="color:#888;">Unique. La modification du slug mettra à jour partial_Intro.nom_page.</small>
            </div>
            <!-- Gestion du logo : affiche le logo actuel ou un upload -->
            <div class="admin-field">
                <label>Logo actuel</label>
                <?php if (!empty($marque['logo'])): ?>
                    <div class="admin-image-field">
                        <div class="admin-image-current">
                            <img src="<?= htmlspecialchars($marque['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                        </div>
                        <div class="admin-field">
                            <label for="logo">Remplacer le logo</label>
                            <input type="file" name="logo" id="logo" accept="image/*" data-preview>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="file" name="logo" id="logo" accept="image/*" data-preview>
                <?php endif; ?>
            </div>
            <!-- Texte alternatif du logo -->
            <div class="admin-field">
                <label for="alt_logo">Alt logo</label>
                <input type="text" name="alt_logo" id="alt_logo" value="<?= htmlspecialchars($marque['alt_logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <!-- Description de la marque -->
            <div class="admin-field">
                <label for="description">Description</label>
                <textarea name="description" id="description"><?= htmlspecialchars($marque['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <!-- Couleur associée -->
            <div class="admin-field">
                <label for="couleur_hex">Couleur</label>
                <input type="color" name="couleur_hex" id="couleur_hex" value="<?= htmlspecialchars($marque['couleur_hex'] ?? '#EE7325', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <!-- Ordre d'affichage -->
            <div class="admin-field">
                <label for="ordre">Ordre</label>
                <input type="number" name="ordre" id="ordre" value="<?= (int)$marque['ordre'] ?>" min="0">
            </div>
            <!-- Case à cocher pour la mise en ligne -->
            <div class="admin-field">
                <div class="container">
                    <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="marques-edit-toggle" <?= $marque['en_ligne'] ? 'checked' : '' ?>>
                    <label class="switch" for="marques-edit-toggle">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="admin-btn-group">
            <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
            <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
        </div>
    </form>
</main>
<!-- Inclusion du pied de page de l'administration -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
