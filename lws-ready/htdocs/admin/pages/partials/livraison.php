<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: livraison
 *
 * Admin section editor for the Livraison section
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire livraison ---
// Vérifie si le formulaire de livraison a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'livraison') {
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
        $alt_fond   = trim($_POST['alt_fond'] ?? '');
        $image_fond = $section['image_fond'] ?? null;

        // Vérifie si une nouvelle image de fond a été téléchargée
        if (!empty($_FILES['image_fond']['name'])) {
            $uploaded = admin_upload_image($_FILES['image_fond']);
            if ($uploaded) $image_fond = $uploaded;
        }

        // Met à jour tous les champs de la section livraison (id = 1)
        $stmt = $pdo->prepare("UPDATE partial_Livraison SET sous_titre = :st, titre = :t, contenu = :c, cta_label = :cl, cta_lien = :cln, image_fond = :if, alt_fond = :af WHERE id = 1");
        $stmt->execute([
            ':st'  => $sous_titre,
            ':t'   => $titre,
            ':c'   => $contenu,
            ':cl'  => $cta_label !== '' ? $cta_label : null,
            ':cln' => $cta_lien !== '' ? $cta_lien : null,
            ':if'  => $image_fond,
            ':af'  => $alt_fond,
        ]);

        // Redirige vers la page d'édition avec un indicateur de succès
        admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
    }
}
// Fin du traitement du formulaire
?>
<!-- Formulaire d'édition de la section livraison -->
<form method="post" enctype="multipart/form-data">
    <!-- Champ caché pour identifier l'action -->
    <input type="hidden" name="action" value="livraison">
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

    <!-- Champ upload de l'image de fond -->
    <div class="admin-field">
        <label>Image de fond</label>
        <input type="file" name="image_fond" accept="image/*">
        <?php if (!empty($section['image_fond'])): ?>
            <!-- Affiche l'image actuelle -->
            <span class="admin-image-current"><img src="<?= htmlspecialchars($section['image_fond'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
        <?php endif; ?>
    </div>

    <!-- Champ texte alternatif pour l'image de fond -->
    <div class="admin-field">
        <label>Alt fond</label>
        <input type="text" name="alt_fond" value="<?= htmlspecialchars($section['alt_fond'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
</form>
