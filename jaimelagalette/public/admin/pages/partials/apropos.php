<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: apropos
 *
 * Admin section editor for the À propos section
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// Récupère l'identifiant de la section à éditer
$section_id = $id;

// --- Traitement du formulaire "à propos" ---
// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apropos') {
    // Récupère le token CSRF pour vérifier la validité du formulaire
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie si le token CSRF est valide
    if (!admin_csrf_verify($token)) {
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // Récupère et nettoie les champs du formulaire
        $sous_titre = trim($_POST['sous_titre'] ?? '');
        $titre      = trim($_POST['titre'] ?? '');
        $contenu    = trim($_POST['contenu'] ?? '');
        $cta_label  = trim($_POST['cta_label'] ?? '');
        $cta_lien   = trim($_POST['cta_lien'] ?? '');
        $alt        = trim($_POST['alt'] ?? '');
        $image      = $section['image'] ?? null;

        // Vérifie si une nouvelle image a été téléchargée
        $image = admin_upload_or_keep('image', $image);

        // Met à jour tous les champs de la section "à propos"
        $stmt = $pdo->prepare("UPDATE partial_Apropos SET sous_titre = :st, titre = :t, contenu = :c, cta_label = :cl, cta_lien = :cln, image = :i, alt = :a WHERE id = :id");
        $stmt->execute([
            ':st'  => $sous_titre,
            ':t'   => $titre,
            ':c'   => $contenu,
            ':cl'  => $cta_label !== '' ? $cta_label : null,
            ':cln' => $cta_lien !== '' ? $cta_lien : null,
            ':i'   => $image,
            ':a'   => $alt,
            ':id'  => $section_id,
        ]);

        // Redirige vers la page d'édition avec un indicateur de succès
        admin_redirect_page_saved();
    }
}
// Fin du traitement du formulaire
?>
<!-- Formulaire d'édition de la section "à propos" -->
<form method="post" enctype="multipart/form-data">
    <!-- Champ caché pour identifier l'action -->
    <input type="hidden" name="action" value="apropos">
    <!-- Champ caché pour le token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

    <!-- Champ sous-titre -->
    <div class="admin-field">
        <label>Sous-titre</label>
        <input type="text" name="sous_titre" value="<?= htmlspecialchars($section['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ titre -->
    <div class="admin-field">
        <label>Titre</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ contenu (texte long) -->
    <div class="admin-field">
        <label>Contenu</label>
        <textarea name="contenu"><?= htmlspecialchars($section['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <!-- Champ texte du bouton CTA -->
    <div class="admin-field">
        <label>CTA label</label>
        <input type="text" name="cta_label" value="<?= htmlspecialchars($section['cta_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <!-- Champ lien du bouton CTA -->
    <div class="admin-field">
        <label>CTA lien</label>
        <input type="text" name="cta_lien" value="<?= htmlspecialchars($section['cta_lien'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <!-- Champ upload d'image -->
    <div class="admin-field">
        <label>Image</label>
        <input type="file" name="image" accept="image/*">
        <?php if (!empty($section['image'])): ?>
            <!-- Affiche l'image actuelle -->
            <span class="admin-image-current"><img src="<?= htmlspecialchars($section['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
        <?php endif; ?>
    </div>

    <!-- Champ texte alternatif pour l'image -->
    <div class="admin-field">
        <label>Alt</label>
        <input type="text" name="alt" value="<?= htmlspecialchars($section['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
</form>
