<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: engagements
 *
 * Admin section editor for the Engagements section (text fields + cards)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire engagements ---
// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère le token CSRF pour vérifier la validité du formulaire
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie si le token CSRF est valide
    if (!admin_csrf_verify($token)) {
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour de la section engagements ---
        if ($_POST['action'] === 'engagements_section') {
            // Récupère et nettoie les champs du formulaire
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');
            $cta_label  = trim($_POST['cta_label'] ?? '');
            $cta_lien   = trim($_POST['cta_lien'] ?? '');

            // Met à jour les champs de la section engagements (id = 1)
            $stmt = $pdo->prepare("UPDATE partial_Engagement SET sous_titre = :st, titre = :t, cta_label = :cl, cta_lien = :cln WHERE id = 1");
            $stmt->execute([
                ':st'  => $sous_titre,
                ':t'   => $titre,
                ':cl'  => $cta_label !== '' ? $cta_label : null,
                ':cln' => $cta_lien !== '' ? $cta_lien : null,
            ]);

            // Redirige vers la page d'édition avec un indicateur de succès
            admin_redirect_page_saved();
        }

        // --- Ajout d'une nouvelle carte engagement ---
        if ($_POST['action'] === 'engagements_add') {
            // Récupère les champs du formulaire d'ajout
            $titre       = trim($_POST['card_titre'] ?? '');
            $description = trim($_POST['card_description'] ?? '');
            $alt         = trim($_POST['card_alt'] ?? '');
            $image       = null;

            // Vérifie si un fichier image a été téléchargé
            $image = admin_upload_or_keep('card_image', $image);

            // Insère la nouvelle carte en base de données
            $stmt = $pdo->prepare("INSERT INTO card_Engagement (partial_engagement_id, image, alt, titre, description) VALUES (1, :i, :a, :t, :d)");
            $stmt->execute([
                ':i' => $image,
                ':a' => $alt,
                ':t' => $titre,
                ':d' => $description,
            ]);

            admin_redirect_page_saved();
        }

        // --- Modification d'une carte engagement existante ---
        if ($_POST['action'] === 'engagements_edit') {
            // Récupère l'identifiant et les champs du formulaire d'édition
            $card_id      = (int) ($_POST['card_id'] ?? 0);
            $titre       = trim($_POST['card_titre'] ?? '');
            $description = trim($_POST['card_description'] ?? '');
            $alt         = trim($_POST['card_alt'] ?? '');
            $image       = $_POST['card_image_current'] ?? null;

            // Vérifie si une nouvelle image a été téléchargée
            $image = admin_upload_or_keep('card_image', $image);

            // Met à jour la carte existante
            $stmt = $pdo->prepare("UPDATE card_Engagement SET image = :i, alt = :a, titre = :t, description = :d WHERE id = :cid AND partial_engagement_id = 1");
            $stmt->execute([
                ':i'   => $image,
                ':a'   => $alt,
                ':t'   => $titre,
                ':d'   => $description,
                ':cid' => $card_id,
            ]);

            admin_redirect_page_saved();
        }

        // --- Suppression d'une carte engagement ---
        if ($_POST['action'] === 'engagements_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime la carte de la base de données
            $stmt = $pdo->prepare("DELETE FROM card_Engagement WHERE id = :id AND partial_engagement_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect_page_saved();
        }
    }
}
// Fin du traitement du formulaire

// Récupère toutes les cartes engagement depuis la base de données
$cards = $pdo->query("SELECT * FROM card_Engagement WHERE partial_engagement_id = 1 ORDER BY id")->fetchAll();
?>

<!-- Formulaire d'édition des textes de la section engagements -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <input type="hidden" name="action" value="engagements_section">
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
        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer la section</button>
    </form>
</fieldset>

<!-- Liste des cartes engagements -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Cartes engagements</h4>
<?php if ($cards): ?>
<!-- Vérifie s'il y a des cartes à afficher -->
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Image</th><th>Titre</th><th>Description</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque carte pour afficher une ligne -->
    <?php foreach ($cards as $c): ?>
        <tr>
            <!-- Affiche l'image si elle existe -->
            <td><?php if (!empty($c['image'])): ?><img src="<?= htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb"><?php endif; ?></td>
            <td><?= htmlspecialchars($c['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($c['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire d'édition -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire d'édition caché par défaut -->
                <div style="display:none;margin-top:0.5rem;" class="admin-card" style="padding:1rem;">
                    <!-- Formulaire de modification d'une carte -->
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;">
                        <input type="hidden" name="action" value="engagements_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="card_image_current" value="<?= htmlspecialchars($c['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ titre de la carte -->
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="card_titre" value="<?= htmlspecialchars($c['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ description de la carte -->
                        <div class="admin-field">
                            <label>Description</label>
                            <textarea name="card_description"><?= htmlspecialchars($c['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ upload d'image -->
                        <div class="admin-field">
                            <label>Image</label>
                            <input type="file" name="card_image" accept="image/*">
                            <?php if (!empty($c['image'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                        </div>
                        <!-- Champ texte alternatif -->
                        <div class="admin-field">
                            <label>Alt</label>
                            <input type="text" name="card_alt" value="<?= htmlspecialchars($c['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression d'une carte -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette carte ?');">
                        <input type="hidden" name="action" value="engagements_delete">
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
        <input type="hidden" name="action" value="engagements_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ titre de la nouvelle carte -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="card_titre" required>
        </div>
        <!-- Champ description -->
        <div class="admin-field">
            <label>Description</label>
            <textarea name="card_description"></textarea>
        </div>
        <!-- Champ import d'image -->
        <div class="admin-field">
            <label>Image</label>
            <input type="file" name="card_image" accept="image/*">
        </div>
        <!-- Champ texte alternatif -->
        <div class="admin-field">
            <label>Alt</label>
            <input type="text" name="card_alt">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
