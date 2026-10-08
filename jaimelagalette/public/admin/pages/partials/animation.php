<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: animation
 *
 * Admin section editor for the Animation section (text fields + cards)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire animation ---
// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère le token CSRF pour vérifier la validité du formulaire
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie si le token CSRF est valide
    if (!admin_csrf_verify($token)) {
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour de la section animation ---
        if ($_POST['action'] === 'animation_section') {
            // Récupère et nettoie les champs du formulaire
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');
            $contenu    = trim($_POST['contenu'] ?? '');

            // Met à jour les champs de la section animation (id = 1)
            $stmt = $pdo->prepare("UPDATE partial_Animation SET sous_titre = :st, titre = :t, contenu = :c WHERE id = 1");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
                ':c'  => $contenu,
            ]);

            // Redirige vers la page d'édition avec un indicateur de succès
            admin_redirect_page_saved();
        }

        // --- Ajout d'une nouvelle carte animation ---
        if ($_POST['action'] === 'animation_add') {
            // Récupère les champs du formulaire d'ajout
            $nom   = trim($_POST['nom'] ?? '');
            $alt   = trim($_POST['alt'] ?? '');
            $image = null;

            // Vérifie si un fichier image a été téléchargé
            $image = admin_upload_or_keep('image', $image);

            // Insère la nouvelle carte en base de données
            $stmt = $pdo->prepare("INSERT INTO card_Animation (partial_animation_id, nom, image, alt) VALUES (1, :n, :i, :a)");
            $stmt->execute([
                ':n' => $nom,
                ':i' => $image,
                ':a' => $alt,
            ]);

            admin_redirect_page_saved();
        }

        // --- Modification d'une carte animation existante ---
        if ($_POST['action'] === 'animation_edit') {
            // Récupère l'identifiant et les champs du formulaire d'édition
            $card_id = (int) ($_POST['card_id'] ?? 0);
            $nom    = trim($_POST['nom'] ?? '');
            $alt    = trim($_POST['alt'] ?? '');
            $image  = $_POST['image_current'] ?? null;

            // Vérifie si une nouvelle image a été téléchargée
            $image = admin_upload_or_keep('image', $image);

            // Met à jour la carte existante
            $stmt = $pdo->prepare("UPDATE card_Animation SET nom = :n, image = :i, alt = :a WHERE id = :cid AND partial_animation_id = 1");
            $stmt->execute([
                ':n'   => $nom,
                ':i'   => $image,
                ':a'   => $alt,
                ':cid' => $card_id,
            ]);

            admin_redirect_page_saved();
        }

        // --- Suppression d'une carte animation ---
        if ($_POST['action'] === 'animation_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime la carte de la base de données
            $stmt = $pdo->prepare("DELETE FROM card_Animation WHERE id = :id AND partial_animation_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect_page_saved();
        }
    }
}
// Fin du traitement du formulaire

// Récupère toutes les cartes animation depuis la base de données
$cards = $pdo->query("SELECT * FROM card_Animation WHERE partial_animation_id = 1 ORDER BY id")->fetchAll();
?>

<!-- Formulaire d'édition des textes de la section animation -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <input type="hidden" name="action" value="animation_section">
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
        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer la section</button>
    </form>
</fieldset>

<!-- Liste des cartes animation -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Cartes animation</h4>
<?php if ($cards): ?>
<!-- Vérifie s'il y a des cartes à afficher -->
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Image</th><th>Nom</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque carte pour afficher une ligne -->
    <?php foreach ($cards as $c): ?>
        <tr>
            <!-- Affiche l'image si elle existe -->
            <td><?php if (!empty($c['image'])): ?><img src="<?= htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb"><?php endif; ?></td>
            <td><?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire d'édition -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire d'édition caché par défaut -->
                <div style="display:none;margin-top:0.5rem;">
                    <!-- Formulaire de modification d'une carte -->
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="animation_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="image_current" value="<?= htmlspecialchars($c['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ nom de la carte -->
                        <div class="admin-field">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ upload d'image -->
                        <div class="admin-field">
                            <label>Image</label>
                            <input type="file" name="image" accept="image/*">
                            <?php if (!empty($c['image'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                        </div>
                        <!-- Champ texte alternatif -->
                        <div class="admin-field">
                            <label>Alt</label>
                            <input type="text" name="alt" value="<?= htmlspecialchars($c['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression d'une carte -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette carte ?');">
                        <input type="hidden" name="action" value="animation_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les cartes -->
    </tbody>
</table>
<?php else: ?>
<!-- Message si aucune carte n'existe -->
<p class="admin-empty">Aucune carte pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des cartes -->

<!-- Formulaire d'ajout d'une nouvelle carte -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une carte</legend>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="animation_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ nom de la nouvelle carte -->
        <div class="admin-field">
            <label>Nom</label>
            <input type="text" name="nom" required>
        </div>
        <!-- Champ import d'image -->
        <div class="admin-field">
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <!-- Champ texte alternatif -->
        <div class="admin-field">
            <label>Alt</label>
            <input type="text" name="alt">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
