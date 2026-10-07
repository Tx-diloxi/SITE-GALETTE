<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: hero
 *
 * Admin section editor for the Hero section
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Hero ---

// Vérifie si le formulaire a été soumis pour la section hero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hero') {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // Récupère et nettoie les champs du formulaire
        $titre    = trim($_POST['titre'] ?? '');
        $accroche = trim($_POST['accroche'] ?? '');
        $alt      = trim($_POST['alt'] ?? '');
        $alt_fond = trim($_POST['alt_fond'] ?? '');

        // Conserve les images existantes par défaut
        $image     = $section['image'] ?? null;
        $image_fond = $section['image_fond'] ?? null;

        // Vérifie si une nouvelle image a été uploadée
        if (!empty($_FILES['image']['name'])) {
            $uploaded = admin_upload_image($_FILES['image']);
            if ($uploaded) $image = $uploaded;
        }
        // Vérifie si une nouvelle image de fond a été uploadée
        if (!empty($_FILES['image_fond']['name'])) {
            $uploaded = admin_upload_image($_FILES['image_fond']);
            if ($uploaded) $image_fond = $uploaded;
        }

        // Met à jour les données de la section hero en base
        $stmt = $pdo->prepare("UPDATE hero SET titre = :t, accroche = :a, image = :i, alt = :alt, image_fond = :if, alt_fond = :af WHERE id = 1");
        $stmt->execute([
            ':t'   => $titre,
            ':a'   => $accroche,
            ':i'   => $image,
            ':alt'  => $alt,
            ':if'  => $image_fond,
            ':af'  => $alt_fond,
        ]);

        // Redirige vers la page d'édition avec un indicateur de succès
        admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
    }
    // Fin de la validation du token CSRF
}
// Fin du traitement du formulaire
?>

<!-- Formulaire d'édition de la section Hero -->
<form method="post" enctype="multipart/form-data">
    <!-- Champ caché : identifiant d'action -->
    <input type="hidden" name="action" value="hero">
    <!-- Champ caché : token CSRF anti-falsification -->
    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

    <!-- Champ : Titre principal -->
    <div class="admin-field">
        <label>Titre</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ : Accroche / sous-titre -->
    <div class="admin-field">
        <label>Accroche</label>
        <input type="text" name="accroche" value="<?= htmlspecialchars($section['accroche'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ : Upload de l'image principale -->
    <div class="admin-field">
        <label>Image</label>
        <input type="file" name="image" accept="image/*">
        <!-- Affiche l'aperçu de l'image existante -->
        <?php if (!empty($section['image'])): ?>
            <span class="admin-image-current"><img src="<?= htmlspecialchars($section['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
        <?php endif; ?>
        <!-- Fin de l'affichage de l'image existante -->
    </div>

    <!-- Champ : Texte alternatif de l'image -->
    <div class="admin-field">
        <label>Alt image</label>
        <input type="text" name="alt" value="<?= htmlspecialchars($section['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <!-- Champ : Upload de l'image de fond -->
    <div class="admin-field">
        <label>Image de fond</label>
        <input type="file" name="image_fond" accept="image/*">
        <!-- Affiche l'aperçu de l'image de fond existante -->
        <?php if (!empty($section['image_fond'])): ?>
            <span class="admin-image-current"><img src="<?= htmlspecialchars($section['image_fond'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
        <?php endif; ?>
        <!-- Fin de l'affichage de l'image de fond existante -->
    </div>

    <!-- Champ : Texte alternatif de l'image de fond -->
    <div class="admin-field">
        <label>Alt fond</label>
        <input type="text" name="alt_fond" value="<?= htmlspecialchars($section['alt_fond'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <!-- Bouton de soumission -->
    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
</form>
