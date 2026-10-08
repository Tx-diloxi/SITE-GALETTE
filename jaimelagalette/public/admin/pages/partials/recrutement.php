<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: recrutement
 *
 * Admin section editor for the Recrutement section (title + cards)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Recrutement ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour du titre de la section ---
        if ($_POST['action'] === 'recrutement_section') {
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');

            // Met à jour les champs titre de la section recrutement
            $stmt = $pdo->prepare("UPDATE partial_Recrutement SET sous_titre = :st, titre = :t WHERE id = 1");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de recrutement_section

        // --- Ajout d'une nouvelle carte recrutement ---
        if ($_POST['action'] === 'recrutement_add') {
            $titre       = trim($_POST['card_titre'] ?? '');
            $description = trim($_POST['card_description'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);
            $alt         = trim($_POST['card_alt'] ?? '');
            $image       = null;

            // Vérifie si une image a été uploadée
            $image = admin_upload_or_keep('card_image', $image);

            // Insère la nouvelle carte en base
            $stmt = $pdo->prepare("INSERT INTO card_Recrutement (partial_recrutement_id, image, alt, titre, description, ordre) VALUES (1, :i, :a, :t, :d, :o)");
            $stmt->execute([
                ':i' => $image,
                ':a' => $alt,
                ':t' => $titre,
                ':d' => $description,
                ':o' => $ordre,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de recrutement_add

        // --- Modification d'une carte recrutement existante ---
        if ($_POST['action'] === 'recrutement_edit') {
            $card_id      = (int) ($_POST['card_id'] ?? 0);
            $titre       = trim($_POST['card_titre'] ?? '');
            $description = trim($_POST['card_description'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);
            $alt         = trim($_POST['card_alt'] ?? '');
            // Conserve l'image existante par défaut
            $image       = $_POST['card_image_current'] ?? null;

            // Vérifie si une nouvelle image a été uploadée
            $image = admin_upload_or_keep('card_image', $image);

            // Met à jour la carte ciblée par son ID
            $stmt = $pdo->prepare("UPDATE card_Recrutement SET image = :i, alt = :a, titre = :t, description = :d, ordre = :o WHERE id = :cid AND partial_recrutement_id = 1");
            $stmt->execute([
                ':i'   => $image,
                ':a'   => $alt,
                ':t'   => $titre,
                ':d'   => $description,
                ':o'   => $ordre,
                ':cid' => $card_id,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de recrutement_edit

        // --- Suppression d'une carte recrutement ---
        if ($_POST['action'] === 'recrutement_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime la carte ciblée par son ID
            $stmt = $pdo->prepare("DELETE FROM card_Recrutement WHERE id = :id AND partial_recrutement_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect_page_saved();
        }
        // Fin de recrutement_delete
    }
    // Fin de la validation du token CSRF
}

// Récupère toutes les cartes recrutement pour les afficher
$cards = $pdo->query("SELECT * FROM card_Recrutement WHERE partial_recrutement_id = 1 ORDER BY ordre, id")->fetchAll();
?>

<!-- Édition du titre de la section Recrutement -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="recrutement_section">
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
        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer la section</button>
    </form>
</fieldset>

<!-- Liste des cartes recrutement -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Cartes recrutement</h4>
<!-- Vérifie s'il y a des cartes à afficher -->
<?php if ($cards): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Image</th><th>Titre</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque carte recrutement -->
    <?php foreach ($cards as $c): ?>
        <tr>
            <!-- Affiche l'image si elle existe -->
            <td><?php if (!empty($c['image'])): ?><img src="<?= htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb"><?php endif; ?></td>
            <td><?= htmlspecialchars($c['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $c['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="recrutement_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ caché : image actuelle pour la conserver si non remplacée -->
                        <input type="hidden" name="card_image_current" value="<?= htmlspecialchars($c['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Titre de la carte -->
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="card_titre" value="<?= htmlspecialchars($c['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Description de la carte -->
                        <div class="admin-field">
                            <label>Description</label>
                            <textarea name="card_description"><?= htmlspecialchars($c['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ : Ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $c['ordre'] ?>">
                        </div>
                        <!-- Champ : Upload de l'image -->
                        <div class="admin-field">
                            <label>Image</label>
                            <input type="file" name="card_image" accept="image/*">
                            <!-- Affiche l'aperçu de l'image existante -->
                            <?php if (!empty($c['image'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                            <!-- Fin de l'affichage de l'image existante -->
                        </div>
                        <!-- Champ : Texte alternatif de l'image -->
                        <div class="admin-field">
                            <label>Alt</label>
                            <input type="text" name="card_alt" value="<?= htmlspecialchars($c['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette carte ?');">
                        <input type="hidden" name="action" value="recrutement_delete">
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
<!-- Message affiché s'il n'y a aucune carte -->
<p class="admin-empty">Aucune carte pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des cartes -->

<!-- Formulaire d'ajout d'une nouvelle carte recrutement -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une carte</legend>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="recrutement_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Titre de la nouvelle carte -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="card_titre" required>
        </div>
        <!-- Champ : Description de la nouvelle carte -->
        <div class="admin-field">
            <label>Description</label>
            <textarea name="card_description"></textarea>
        </div>
        <!-- Champ : Ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <!-- Champ : Upload de l'image -->
        <div class="admin-field">
            <label>Image</label>
            <input type="file" name="card_image" accept="image/*">
        </div>
        <!-- Champ : Texte alternatif de l'image -->
        <div class="admin-field">
            <label>Alt</label>
            <input type="text" name="card_alt">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
