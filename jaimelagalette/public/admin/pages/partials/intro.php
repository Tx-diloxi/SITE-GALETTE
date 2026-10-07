<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: intro
 *
 * Admin section editor for the Introduction section
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Introduction ---

// Assure que $nom_page a une valeur par défaut
$nom_page = $nom_page ?? '';

// Vérifie si le formulaire a été soumis pour la section intro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'intro') {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // Récupère et nettoie les champs texte du formulaire
        $sous_titre   = trim($_POST['sous_titre'] ?? '');
        $titre        = trim($_POST['titre'] ?? '');
        $citation     = trim($_POST['citation'] ?? '');
        $contenu      = trim($_POST['contenu'] ?? '');
        $alt_fond     = trim($_POST['alt_fond'] ?? '');
        $alt_mascotte = trim($_POST['alt_mascotte'] ?? '');

        // Conserve les images existantes par défaut
        $image_fond    = $section['image_fond'] ?? null;
        $image_mascotte = $section['image_mascotte'] ?? null;

        // Vérifie si une nouvelle image de fond a été uploadée
        if (!empty($_FILES['image_fond']['name'])) {
            $uploaded = admin_upload_image($_FILES['image_fond']);
            if ($uploaded) $image_fond = $uploaded;
        }
        // Vérifie si une nouvelle image mascotte a été uploadée
        if (!empty($_FILES['image_mascotte']['name'])) {
            $uploaded = admin_upload_image($_FILES['image_mascotte']);
            if ($uploaded) $image_mascotte = $uploaded;
        }

        // Vérifie si l'enregistrement existe déjà (update) ou non (insert)
        if ($id > 0) {
            // Met à jour l'introduction existante
            $stmt = $pdo->prepare("UPDATE partial_Intro SET sous_titre = :st, titre = :t, citation = :c, contenu = :co, image_fond = :if, alt_fond = :af, image_mascotte = :im, alt_mascotte = :am WHERE id = :id");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
                ':c'  => $citation,
                ':co' => $contenu,
                ':if' => $image_fond,
                ':af' => $alt_fond,
                ':im' => $image_mascotte,
                ':am' => $alt_mascotte,
                ':id' => $id,
            ]);
        } else {
            // Crée une nouvelle introduction
            $stmt = $pdo->prepare("INSERT INTO partial_Intro (sous_titre, titre, citation, contenu, nom_page, image_fond, alt_fond, image_mascotte, alt_mascotte) VALUES (:st, :t, :c, :co, :np, :if, :af, :im, :am)");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
                ':c'  => $citation,
                ':co' => $contenu,
                ':np' => $nom_page,
                ':if' => $image_fond,
                ':af' => $alt_fond,
                ':im' => $image_mascotte,
                ':am' => $alt_mascotte,
            ]);
        }
        // Fin de la condition update/insert

        // Redirige vers la page d'édition avec un indicateur de succès
        admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
    }
    // Fin de la validation du token CSRF
}
// Fin du traitement du formulaire
?>

<!-- Formulaire d'édition de la section Introduction -->
<form method="post" enctype="multipart/form-data">
    <!-- Champ caché : identifiant d'action -->
    <input type="hidden" name="action" value="intro">
    <!-- Champ caché : token CSRF anti-falsification -->
    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

    <!-- Champ : Sous-titre -->
    <div class="admin-field">
        <label>Sous-titre</label>
        <input type="text" name="sous_titre" value="<?= htmlspecialchars($section['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <!-- Champ : Titre principal -->
    <div class="admin-field">
        <label>Titre</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ : Citation mise en avant -->
    <div class="admin-field">
        <label>Citation</label>
        <textarea name="citation"><?= htmlspecialchars($section['citation'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <!-- Champ : Contenu texte -->
    <div class="admin-field">
        <label>Contenu</label>
        <textarea name="contenu"><?= htmlspecialchars($section['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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

    <!-- Champ : Upload de l'image mascotte -->
    <div class="admin-field">
        <label>Image mascotte</label>
        <input type="file" name="image_mascotte" accept="image/*">
        <!-- Affiche l'aperçu de l'image mascotte existante -->
        <?php if (!empty($section['image_mascotte'])): ?>
            <span class="admin-image-current"><img src="<?= htmlspecialchars($section['image_mascotte'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
        <?php endif; ?>
        <!-- Fin de l'affichage de l'image mascotte existante -->
    </div>

    <!-- Champ : Texte alternatif de l'image mascotte -->
    <div class="admin-field">
        <label>Alt mascotte</label>
        <input type="text" name="alt_mascotte" value="<?= htmlspecialchars($section['alt_mascotte'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <!-- Bouton de soumission -->
    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
</form>
