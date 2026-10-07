<?php
/**
 * Admin - Gestion des marques : création d'une nouvelle marque.
 */
declare(strict_types=1);

// ----
// Initialisation
// ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$error = null;
$slug = '';

// --- Traitement du formulaire ---
// Vérifie si le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie la validité du token CSRF pour éviter les attaques
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } elseif (empty(trim($_POST['nom'] ?? ''))) {
        $error = 'Le nom est requis.';
    } else {
        // Génération du slug à partir du nom si non fourni
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : '';
        if (empty($slug)) {
            // Nettoie le nom : supprime les accents, remplace les espaces par des tirets
            $slug = mb_strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['nom']))), 'UTF-8');
            $slug = trim($slug, '-');
        }

        // Vérifie l'unicité du slug dans la table marque
        $check = $pdo->prepare("SELECT COUNT(*) FROM marque WHERE slug = :slug");
        $check->execute([':slug' => $slug]);
        if ($check->fetchColumn() > 0) {
            $error = 'Ce slug est déjà utilisé.';
        }
    }

    // Si aucune erreur, procède à l'insertion
    if (empty($error)) {
        // Télécharge le logo si un fichier a été envoyé
        $logo_url = admin_upload_image($_FILES['logo'] ?? []);

        // Démarre une transaction pour insérer dans deux tables
        $pdo->beginTransaction();
        try {
            // Requête SQL : insère une nouvelle marque
            $stmt = $pdo->prepare("
                INSERT INTO marque (nom, slug, logo, alt_logo, description, couleur_hex, en_ligne, ordre)
                VALUES (:nom, :slug, :logo, :alt_logo, :description, :couleur_hex, :en_ligne, :ordre)
            ");
            $stmt->execute([
                ':nom' => $_POST['nom'],
                ':slug' => $slug,
                ':logo' => $logo_url ?? '',
                ':alt_logo' => $_POST['alt_logo'] ?? '',
                ':description' => $_POST['description'] ?? '',
                ':couleur_hex' => $_POST['couleur_hex'] ?? '#EE7325',
                ':en_ligne' => (int)(!empty($_POST['en_ligne'])),
                ':ordre' => (int)($_POST['ordre'] ?? 0),
            ]);
            $marque_id = (int)$pdo->lastInsertId();

            // Requête SQL : crée une entrée dans partial_Intro pour la page produit associée
            $stmt = $pdo->prepare("
                INSERT INTO partial_Intro (sous_titre, titre, contenu, nom_page, image_fond, alt_fond)
                VALUES ('', :titre, :contenu, :nom_page, '', '')
            ");
            $stmt->execute([
                ':titre' => $_POST['nom'],
                ':contenu' => $_POST['description'] ?? '',
                ':nom_page' => 'nos-produits/' . $slug,
            ]);

            // Valide la transaction (les deux insertions sont faites)
            $pdo->commit();

            $created = true;
        } catch (Exception $e) {
            // En cas d'erreur, annule toute la transaction
            $pdo->rollBack();
            error_log('Erreur création marque : ' . $e->getMessage());
            $error = 'Erreur lors de la création.';
        }
    }
}

// ----
// Affichage de la page
// ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils avec le titre et le lien retour -->
    <div class="admin-toolbar">
        <h1>Nouvelle marque</h1>
        <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
    </div>

    <!-- Affiche un message d'erreur si la création a échoué -->
    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Si la marque a été créée avec succès, affiche la confirmation et la checklist -->
    <?php if (!empty($created)): ?>
        <div class="admin-alert admin-alert--success">Marque créée avec succès !</div>
        <div class="admin-card">
            <h2>Checklist de création</h2>
            <!-- Rappelle les étapes déjà faites automatiquement et celles à faire manuellement -->
            <ul class="admin-checklist">
                <li class="admin-check-done">✅ Table marque créée</li>
                <li class="admin-check-done">✅ partial_Intro créé (nom_page = 'nos-produits/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>')</li>
                <li class="admin-check-pending">⚠️ Créer manuellement : pages/nos-produits/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>/index.php (copier le template)</li>
                <li class="admin-check-pending">⚠️ Ajouter la route dans public/index.php</li>
            </ul>
            <div class="admin-btn-group" style="margin-top:1rem;">
                <a href="/admin/marques/edit.php?id=<?= (int)$marque_id ?>" class="admin-btn admin-btn--primary">Modifier la marque</a>
                <a href="/admin/marques/create.php" class="admin-btn admin-btn--secondary">Ajouter une autre marque</a>
                <a href="/admin/marques/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Formulaire de création d'une marque -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

            <div class="admin-card">
                <!-- Champ nom (obligatoire) -->
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" name="nom" id="slug-source" value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <!-- Champ slug (généré auto, modifiable) -->
                <div class="admin-field">
                    <label for="slug">Slug</label>
                    <input type="text" name="slug" id="slug-target" value="<?= htmlspecialchars($_POST['slug'] ?? $slug, ENT_QUOTES, 'UTF-8') ?>">
                    <small style="color:#888;">Généré automatiquement depuis le nom. Doit être unique.</small>
                </div>
                <!-- Upload du logo -->
                <div class="admin-field">
                    <label for="logo">Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/*" data-preview>
                </div>
                <!-- Texte alternatif pour le logo -->
                <div class="admin-field">
                    <label for="alt_logo">Alt logo</label>
                    <input type="text" name="alt_logo" id="alt_logo" value="<?= htmlspecialchars($_POST['alt_logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <!-- Description de la marque -->
                <div class="admin-field">
                    <label for="description">Description</label>
                    <textarea name="description" id="description"><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <!-- Couleur associée à la marque -->
                <div class="admin-field">
                    <label for="couleur_hex">Couleur</label>
                    <input type="color" name="couleur_hex" id="couleur_hex" value="<?= htmlspecialchars($_POST['couleur_hex'] ?? '#EE7325', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <!-- Ordre d'affichage -->
                <div class="admin-field">
                    <label for="ordre">Ordre</label>
                    <input type="number" name="ordre" id="ordre" value="<?= (int)($_POST['ordre'] ?? 0) ?>" min="0">
                </div>
                <!-- Case à cocher pour mettre en ligne -->
                <div class="admin-field">
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="marques-add-toggle" <?= !empty($_POST['en_ligne']) ? 'checked' : '' ?>>
                        <label class="switch" for="marques-add-toggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary">Créer la marque</button>
        </form>
    <?php endif; ?>
</main>
<!-- Inclusion du pied de page de l'administration -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
