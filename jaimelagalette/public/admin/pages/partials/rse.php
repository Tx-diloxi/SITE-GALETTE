<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: rse
 *
 * Admin section editor for the RSE section (RSE action cards)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section RSE ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Ajout d'une nouvelle action RSE ---
        if ($_POST['action'] === 'rse_add') {
            $titre       = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $contenu     = trim($_POST['contenu'] ?? '');
            $tags        = trim($_POST['tags'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);
            $alt         = trim($_POST['alt'] ?? '');
            $image       = null;

            // Vérifie si une image a été uploadée
            $image = admin_upload_or_keep('image', $image);

            // Insère la nouvelle action RSE en base
            $stmt = $pdo->prepare("INSERT INTO action_RSE (image, alt, titre, description, contenu, tags, ordre) VALUES (:i, :a, :t, :d, :c, :tg, :o)");
            $stmt->execute([
                ':i'  => $image,
                ':a'  => $alt,
                ':t'  => $titre,
                ':d'  => $description,
                ':c'  => $contenu,
                ':tg' => $tags !== '' ? $tags : null,
                ':o'  => $ordre,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de rse_add

        // --- Modification d'une action RSE existante ---
        if ($_POST['action'] === 'rse_edit') {
            $card_id      = (int) ($_POST['card_id'] ?? 0);
            $titre       = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $contenu     = trim($_POST['contenu'] ?? '');
            $tags        = trim($_POST['tags'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);
            $alt         = trim($_POST['alt'] ?? '');
            // Conserve l'image existante par défaut
            $image       = $_POST['image_current'] ?? null;

            // Vérifie si une nouvelle image a été uploadée
            $image = admin_upload_or_keep('image', $image);

            // Met à jour l'action RSE ciblée par son ID
            $stmt = $pdo->prepare("UPDATE action_RSE SET image = :i, alt = :a, titre = :t, description = :d, contenu = :c, tags = :tg, ordre = :o WHERE id = :cid");
            $stmt->execute([
                ':i'  => $image,
                ':a'  => $alt,
                ':t'  => $titre,
                ':d'  => $description,
                ':c'  => $contenu,
                ':tg' => $tags !== '' ? $tags : null,
                ':o'  => $ordre,
                ':cid' => $card_id,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de rse_edit

        // --- Suppression d'une action RSE ---
        if ($_POST['action'] === 'rse_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime l'action RSE ciblée par son ID
            $stmt = $pdo->prepare("DELETE FROM action_RSE WHERE id = :id");
            $stmt->execute([':id' => $card_id]);

            admin_redirect_page_saved();
        }
        // Fin de rse_delete
    }
    // Fin de la validation du token CSRF
}

// Récupère toutes les actions RSE pour les afficher
$actions = $pdo->query("SELECT * FROM action_RSE ORDER BY ordre, id")->fetchAll();
?>

<!-- Liste des actions RSE -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Actions RSE</h4>
<!-- Vérifie s'il y a des actions à afficher -->
<?php if ($actions): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Image</th><th>Titre</th><th>Description</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque action RSE -->
    <?php foreach ($actions as $a): ?>
        <tr>
            <!-- Affiche l'image si elle existe -->
            <td><?php if (!empty($a['image'])): ?><img src="<?= htmlspecialchars($a['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb"><?php endif; ?></td>
            <td><?= htmlspecialchars($a['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($a['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $a['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="rse_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $a['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ caché : image actuelle pour la conserver si non remplacée -->
                        <input type="hidden" name="image_current" value="<?= htmlspecialchars($a['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Titre de l'action -->
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars($a['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Description courte -->
                        <div class="admin-field">
                            <label>Description</label>
                            <input type="text" name="description" value="<?= htmlspecialchars($a['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Champ : Contenu détaillé -->
                        <div class="admin-field">
                            <label>Contenu</label>
                            <textarea name="contenu"><?= htmlspecialchars($a['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ : Tags (mots-clés) -->
                        <div class="admin-field">
                            <label>Tags</label>
                            <input type="text" name="tags" value="<?= htmlspecialchars($a['tags'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Champ : Ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $a['ordre'] ?>">
                        </div>
                        <!-- Champ : Upload de l'image -->
                        <div class="admin-field">
                            <label>Image</label>
                            <input type="file" name="image" accept="image/*">
                            <!-- Affiche l'aperçu de l'image existante -->
                            <?php if (!empty($a['image'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($a['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                            <!-- Fin de l'affichage de l'image existante -->
                        </div>
                        <!-- Champ : Texte alternatif de l'image -->
                        <div class="admin-field">
                            <label>Alt</label>
                            <input type="text" name="alt" value="<?= htmlspecialchars($a['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette action RSE ?');">
                        <input type="hidden" name="action" value="rse_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $a['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les actions RSE -->
    </tbody>
</table>
<?php else: ?>
<!-- Message affiché s'il n'y a aucune action -->
<p class="admin-empty">Aucune action RSE pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des actions RSE -->

<!-- Formulaire d'ajout d'une nouvelle action RSE -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une action RSE</legend>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="rse_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Titre de l'action -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="titre" required>
        </div>
        <!-- Champ : Description courte -->
        <div class="admin-field">
            <label>Description</label>
            <input type="text" name="description">
        </div>
        <!-- Champ : Contenu détaillé -->
        <div class="admin-field">
            <label>Contenu</label>
            <textarea name="contenu"></textarea>
        </div>
        <!-- Champ : Tags (mots-clés) -->
        <div class="admin-field">
            <label>Tags</label>
            <input type="text" name="tags">
        </div>
        <!-- Champ : Ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <!-- Champ : Upload de l'image -->
        <div class="admin-field">
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <!-- Champ : Texte alternatif de l'image -->
        <div class="admin-field">
            <label>Alt</label>
            <input type="text" name="alt">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
