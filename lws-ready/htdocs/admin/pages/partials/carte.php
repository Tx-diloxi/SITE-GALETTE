<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: carte
 *
 * Admin section editor for the Carte section (title + map points)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Carte ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour du titre et de l'image de la section ---
        if ($_POST['action'] === 'carte_section') {
            // Récupère les champs texte
            $sous_titre   = trim($_POST['sous_titre'] ?? '');
            $titre        = trim($_POST['titre'] ?? '');
            $alt_mascotte = trim($_POST['alt_mascotte'] ?? '');
            // Conserve l'image mascotte existante par défaut
            $image_mascotte = $section['image_mascotte'] ?? null;

            // Vérifie si une nouvelle image mascotte a été uploadée
            if (!empty($_FILES['image_mascotte']['name'])) {
                $uploaded = admin_upload_image($_FILES['image_mascotte']);
                if ($uploaded) $image_mascotte = $uploaded;
            }

            // Met à jour les données de la section carte en base
            $stmt = $pdo->prepare("UPDATE partial_Carte SET sous_titre = :st, titre = :t, image_mascotte = :im, alt_mascotte = :am WHERE id = 1");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
                ':im' => $image_mascotte,
                ':am' => $alt_mascotte,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
        // Fin de carte_section

        // --- Sauvegarde d'un point sur la carte ---
        if ($_POST['action'] === 'carte_point_save') {
            $point_id = (int) ($_POST['point_id'] ?? 0);
            $est_ouvert = isset($_POST['est_ouvert']) ? 1 : 0;
            $lat = $_POST['latitude'] !== '' ? (float) $_POST['latitude'] : null;
            $lng = $_POST['longitude'] !== '' ? (float) $_POST['longitude'] : null;

            // Met à jour les coordonnées et le statut d'ouverture du point
            $stmt = $pdo->prepare("UPDATE point_Carte SET est_ouvert = :eo, latitude = :lat, longitude = :lng WHERE id = :pid AND partial_carte_id = 1");
            $stmt->execute([
                ':eo'  => $est_ouvert,
                ':lat' => $lat,
                ':lng' => $lng,
                ':pid' => $point_id,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
        // Fin de carte_point_save
    }
    // Fin de la validation du token CSRF
}

// Récupère tous les points de la carte pour les afficher
$points = $pdo->query("SELECT * FROM point_Carte WHERE partial_carte_id = 1 ORDER BY id")->fetchAll();
?>

<!-- Édition du titre de la section Carte -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post" enctype="multipart/form-data">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="carte_section">
        <!-- Champ caché : token CSRF anti-falsification -->
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Sous-titre de la section -->
        <div class="admin-field">
            <label>Sous-titre</label>
            <input type="text" name="sous_titre" value="<?= htmlspecialchars($section['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <!-- Champ : Titre de la section -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <!-- Champ : Upload de l'image mascotte -->
        <div class="admin-field">
            <label>Image mascotte</label>
            <input type="file" name="image_mascotte" accept="image/*">
            <!-- Affiche l'aperçu de l'image mascotte existante -->
            <?php if (!empty($section['image_mascotte'])): ?>
                <span class="admin-image-current"><img src="<?= htmlspecialchars($section['image_mascotte'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
            <?php endif; ?>
            <!-- Fin de l'affichage de l'image existante -->
        </div>
        <!-- Champ : Texte alternatif de l'image mascotte -->
        <div class="admin-field">
            <label>Alt mascotte</label>
            <input type="text" name="alt_mascotte" value="<?= htmlspecialchars($section['alt_mascotte'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer la section</button>
    </form>
</fieldset>

<!-- Liste des points sur la carte -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Points sur la carte</h4>
<!-- Vérifie s'il y a des points à afficher -->
<?php if ($points): ?>
<table class="admin-table">
    <thead>
        <tr><th>Nom</th><th>Ville</th><th>Ouvert</th><th>Latitude</th><th>Longitude</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque point pour afficher une ligne -->
    <?php foreach ($points as $p): ?>
        <tr>
            <form method="post">
                <!-- Champ caché : identifiant d'action -->
                <input type="hidden" name="action" value="carte_point_save">
                <!-- Champ caché : token CSRF anti-falsification -->
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <!-- Champ caché : identifiant du point -->
                <input type="hidden" name="point_id" value="<?= htmlspecialchars((string) $p['id'], ENT_QUOTES, 'UTF-8') ?>">
                <td><?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['ville'], ENT_QUOTES, 'UTF-8') ?></td>
                <!-- Champ : Statut d'ouverture (toggle) -->
                <td>
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="est_ouvert" value="1" id="carte-toggle-<?= $p['id'] ?>" <?= $p['est_ouvert'] ? 'checked' : '' ?>>
                        <label class="switch" for="carte-toggle-<?= $p['id'] ?>">
                            <span class="slider"></span>
                        </label>
                    </div>
                </td>
                <!-- Champ : Latitude -->
                <td><input type="text" name="latitude" value="<?= htmlspecialchars((string) $p['latitude'], ENT_QUOTES, 'UTF-8') ?>" style="width:100px;" placeholder="48.3622"></td>
                <!-- Champ : Longitude -->
                <td><input type="text" name="longitude" value="<?= htmlspecialchars((string) $p['longitude'], ENT_QUOTES, 'UTF-8') ?>" style="width:100px;" placeholder="-2.2707"></td>
                <!-- Bouton de sauvegarde pour le point -->
                <td><button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button></td>
            </form>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les points -->
    </tbody>
</table>
<?php else: ?>
<!-- Message affiché s'il n'y a aucun point -->
<p class="admin-empty">Aucun point.</p>
<?php endif; ?>
<!-- Fin de l'affichage des points -->
