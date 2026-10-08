<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: histoire
 *
 * Admin section editor for the Histoire section (text fields + timeline steps)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire histoire ---
// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère le token CSRF pour vérifier la validité du formulaire
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie si le token CSRF est valide
    if (!admin_csrf_verify($token)) {
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour de la section histoire ---
        if ($_POST['action'] === 'histoire_section') {
            // Récupère et nettoie les champs du formulaire
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');
            $cta_label  = trim($_POST['cta_label'] ?? '');
            $cta_lien   = trim($_POST['cta_lien'] ?? '');

            // Met à jour les champs de la section histoire (id = 1)
            $stmt = $pdo->prepare("UPDATE partial_Histoire SET sous_titre = :st, titre = :t, cta_label = :cl, cta_lien = :cln WHERE id = 1");
            $stmt->execute([
                ':st'  => $sous_titre,
                ':t'   => $titre,
                ':cl'  => $cta_label !== '' ? $cta_label : null,
                ':cln' => $cta_lien !== '' ? $cta_lien : null,
            ]);

            // Redirige vers la page d'édition avec un indicateur de succès
            admin_redirect_page_saved();
        }

        // --- Ajout d'une nouvelle étape historique ---
        if ($_POST['action'] === 'histoire_add') {
            // Récupère les champs du formulaire d'ajout
            $annee       = trim($_POST['annee'] ?? '');
            $nom         = trim($_POST['nom'] ?? '');
            $region      = trim($_POST['region'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $alt         = trim($_POST['alt'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);
            $image       = null;

            // Vérifie si un fichier image a été téléchargé
            $image = admin_upload_or_keep('image', $image);

            // Insère la nouvelle étape en base de données
            $stmt = $pdo->prepare("INSERT INTO etape_Histoire (partial_histoire_id, annee, nom, region, description, image, alt, ordre) VALUES (1, :a, :n, :r, :d, :i, :alt, :o)");
            $stmt->execute([
                ':a'   => $annee,
                ':n'   => $nom,
                ':r'   => $region !== '' ? $region : null,
                ':d'   => $description,
                ':i'   => $image,
                ':alt' => $alt,
                ':o'   => $ordre,
            ]);

            admin_redirect_page_saved();
        }

        // --- Modification d'une étape existante ---
        if ($_POST['action'] === 'histoire_edit') {
            // Récupère l'identifiant et les champs du formulaire d'édition
            $card_id      = (int) ($_POST['card_id'] ?? 0);
            $annee       = trim($_POST['annee'] ?? '');
            $nom         = trim($_POST['nom'] ?? '');
            $region      = trim($_POST['region'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $alt         = trim($_POST['alt'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);
            $image       = $_POST['image_current'] ?? null;

            // Vérifie si une nouvelle image a été téléchargée
            $image = admin_upload_or_keep('image', $image);

            // Met à jour l'étape existante
            $stmt = $pdo->prepare("UPDATE etape_Histoire SET annee = :a, nom = :n, region = :r, description = :d, image = :i, alt = :alt, ordre = :o WHERE id = :cid AND partial_histoire_id = 1");
            $stmt->execute([
                ':a'   => $annee,
                ':n'   => $nom,
                ':r'   => $region !== '' ? $region : null,
                ':d'   => $description,
                ':i'   => $image,
                ':alt' => $alt,
                ':o'   => $ordre,
                ':cid' => $card_id,
            ]);

            admin_redirect_page_saved();
        }

        // --- Suppression d'une étape historique ---
        if ($_POST['action'] === 'histoire_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime l'étape de la base de données
            $stmt = $pdo->prepare("DELETE FROM etape_Histoire WHERE id = :id AND partial_histoire_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect_page_saved();
        }
    }
}
// Fin du traitement du formulaire

// Récupère toutes les étapes de l'histoire depuis la base de données, triées par ordre puis par id
$etapes = $pdo->query("SELECT * FROM etape_Histoire WHERE partial_histoire_id = 1 ORDER BY ordre, id")->fetchAll();
?>

<!-- Formulaire d'édition des textes de la section histoire -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <input type="hidden" name="action" value="histoire_section">
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

<!-- Liste des étapes de l'histoire -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Étapes de l'histoire</h4>
<?php if ($etapes): ?>
<!-- Vérifie s'il y a des étapes à afficher -->
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Année</th><th>Nom</th><th>Région</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque étape pour afficher une ligne -->
    <?php foreach ($etapes as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['annee'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($e['region'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $e['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire d'édition -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire d'édition caché par défaut -->
                <div style="display:none;margin-top:0.5rem;">
                    <!-- Formulaire de modification d'une étape -->
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="histoire_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $e['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="image_current" value="<?= htmlspecialchars($e['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ année -->
                        <div class="admin-field">
                            <label>Année</label>
                            <input type="text" name="annee" value="<?= htmlspecialchars($e['annee'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ nom -->
                        <div class="admin-field">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ région -->
                        <div class="admin-field">
                            <label>Région</label>
                            <input type="text" name="region" value="<?= htmlspecialchars($e['region'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Champ description -->
                        <div class="admin-field">
                            <label>Description</label>
                            <textarea name="description"><?= htmlspecialchars($e['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $e['ordre'] ?>">
                        </div>
                        <!-- Champ upload d'image -->
                        <div class="admin-field">
                            <label>Image</label>
                            <input type="file" name="image" accept="image/*">
                            <?php if (!empty($e['image'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($e['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                        </div>
                        <!-- Champ texte alternatif -->
                        <div class="admin-field">
                            <label>Alt</label>
                            <input type="text" name="alt" value="<?= htmlspecialchars($e['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression d'une étape -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette étape ?');">
                        <input type="hidden" name="action" value="histoire_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $e['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les étapes -->
    </tbody>
</table>
<?php else: ?>
<!-- Message si aucune étape n'existe -->
<p class="admin-empty">Aucune étape pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des étapes -->

<!-- Formulaire d'ajout d'une nouvelle étape -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une étape</legend>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="histoire_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ année de la nouvelle étape -->
        <div class="admin-field">
            <label>Année</label>
            <input type="text" name="annee" required>
        </div>
        <!-- Champ nom -->
        <div class="admin-field">
            <label>Nom</label>
            <input type="text" name="nom" required>
        </div>
        <!-- Champ région -->
        <div class="admin-field">
            <label>Région</label>
            <input type="text" name="region">
        </div>
        <!-- Champ description -->
        <div class="admin-field">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>
        <!-- Champ ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
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
