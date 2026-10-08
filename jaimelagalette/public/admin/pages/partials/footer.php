<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: footer
 *
 * Admin section editor for the Footer (general fields + links + legal links)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire du Footer ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour des champs généraux du footer ---
        if ($_POST['action'] === 'footer_fields') {
            // Récupère les champs texte
            $alt_logo    = trim($_POST['alt_logo'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $copyright   = trim($_POST['copyright'] ?? '');
            // Conserve le logo existant par défaut
            $logo        = $section['logo'] ?? null;

            // Vérifie si un nouveau logo a été uploadé
            $logo = admin_upload_or_keep('logo', $logo);

            // Met à jour les champs du footer en base
            $stmt = $pdo->prepare("UPDATE footer SET logo = :l, alt_logo = :al, description = :d, copyright = :c WHERE id = 1");
            $stmt->execute([
                ':l'  => $logo,
                ':al' => $alt_logo,
                ':d'  => $description,
                ':c'  => $copyright,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de footer_fields

        // --- Ajout d'un lien dans le footer ---
        if ($_POST['action'] === 'footer_lien_add') {
            $categorie = trim($_POST['categorie'] ?? '');
            $label     = trim($_POST['label'] ?? '');
            $lien      = trim($_POST['lien'] ?? '');
            $ordre     = (int) ($_POST['ordre'] ?? 0);

            // Insère le nouveau lien en base
            $stmt = $pdo->prepare("INSERT INTO footer_lien (footer_id, categorie, label, lien, ordre) VALUES (1, :c, :l, :li, :o)");
            $stmt->execute([
                ':c'  => $categorie,
                ':l'  => $label,
                ':li' => $lien,
                ':o'  => $ordre,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de footer_lien_add

        // --- Modification d'un lien du footer ---
        if ($_POST['action'] === 'footer_lien_edit') {
            $item_id   = (int) ($_POST['item_id'] ?? 0);
            $categorie = trim($_POST['categorie'] ?? '');
            $label    = trim($_POST['label'] ?? '');
            $lien     = trim($_POST['lien'] ?? '');
            $ordre    = (int) ($_POST['ordre'] ?? 0);

            // Met à jour le lien ciblé par son ID
            $stmt = $pdo->prepare("UPDATE footer_lien SET categorie = :c, label = :l, lien = :li, ordre = :o WHERE id = :iid AND footer_id = 1");
            $stmt->execute([
                ':c'   => $categorie,
                ':l'   => $label,
                ':li'  => $lien,
                ':o'   => $ordre,
                ':iid' => $item_id,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de footer_lien_edit

        // --- Suppression d'un lien du footer ---
        if ($_POST['action'] === 'footer_lien_delete') {
            $item_id = (int) ($_POST['item_id'] ?? 0);
            // Supprime le lien ciblé par son ID
            $stmt = $pdo->prepare("DELETE FROM footer_lien WHERE id = :id AND footer_id = 1");
            $stmt->execute([':id' => $item_id]);

            admin_redirect_page_saved();
        }
        // Fin de footer_lien_delete

        // --- Ajout d'un lien légal ---
        if ($_POST['action'] === 'footer_legal_add') {
            $label = trim($_POST['label'] ?? '');
            $lien  = trim($_POST['lien'] ?? '');
            $ordre = (int) ($_POST['ordre'] ?? 0);

            // Insère le nouveau lien légal en base
            $stmt = $pdo->prepare("INSERT INTO footer_legal (footer_id, label, lien, ordre) VALUES (1, :l, :li, :o)");
            $stmt->execute([
                ':l'  => $label,
                ':li' => $lien,
                ':o'  => $ordre,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de footer_legal_add

        // --- Modification d'un lien légal ---
        if ($_POST['action'] === 'footer_legal_edit') {
            $item_id = (int) ($_POST['item_id'] ?? 0);
            $label  = trim($_POST['label'] ?? '');
            $lien   = trim($_POST['lien'] ?? '');
            $ordre  = (int) ($_POST['ordre'] ?? 0);

            // Met à jour le lien légal ciblé par son ID
            $stmt = $pdo->prepare("UPDATE footer_legal SET label = :l, lien = :li, ordre = :o WHERE id = :iid AND footer_id = 1");
            $stmt->execute([
                ':l'   => $label,
                ':li'  => $lien,
                ':o'   => $ordre,
                ':iid' => $item_id,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de footer_legal_edit

        // --- Suppression d'un lien légal ---
        if ($_POST['action'] === 'footer_legal_delete') {
            $item_id = (int) ($_POST['item_id'] ?? 0);
            // Supprime le lien légal ciblé par son ID
            $stmt = $pdo->prepare("DELETE FROM footer_legal WHERE id = :id AND footer_id = 1");
            $stmt->execute([':id' => $item_id]);

            admin_redirect_page_saved();
        }
        // Fin de footer_legal_delete
    }
    // Fin de la validation du token CSRF
}

// Récupère les liens du footer et les liens légaux depuis la base
$liens   = $pdo->query("SELECT * FROM footer_lien WHERE footer_id = 1 ORDER BY categorie, ordre, id")->fetchAll();
$legals  = $pdo->query("SELECT * FROM footer_legal WHERE footer_id = 1 ORDER BY ordre, id")->fetchAll();
// Regroupe les liens par catégorie pour l'affichage
$categories = [];
foreach ($liens as $l) {
    $categories[$l['categorie']][] = $l;
}
// Fin du regroupement par catégorie
?>

<!-- Édition des champs généraux du footer -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Champs du footer</legend>
    <form method="post" enctype="multipart/form-data">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="footer_fields">
        <!-- Champ caché : token CSRF anti-falsification -->
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Upload du logo -->
        <div class="admin-field">
            <label>Logo</label>
            <input type="file" name="logo" accept="image/*">
            <!-- Affiche l'aperçu du logo existant -->
            <?php if (!empty($section['logo'])): ?>
                <span class="admin-image-current"><img src="<?= htmlspecialchars($section['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
            <?php endif; ?>
            <!-- Fin de l'affichage du logo existant -->
        </div>

        <!-- Champ : Texte alternatif du logo -->
        <div class="admin-field">
            <label>Alt logo</label>
            <input type="text" name="alt_logo" value="<?= htmlspecialchars($section['alt_logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <!-- Champ : Description du footer -->
        <div class="admin-field">
            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($section['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Champ : Texte de copyright -->
        <div class="admin-field">
            <label>Copyright</label>
            <input type="text" name="copyright" value="<?= htmlspecialchars($section['copyright'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
    </form>
</fieldset>

<!-- Liste des liens du footer -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Liens du footer</h4>
<!-- Vérifie s'il y a des liens à afficher -->
<?php if ($liens): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Catégorie</th><th>Label</th><th>Lien</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque lien du footer -->
    <?php foreach ($liens as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['categorie'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($l['label'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($l['lien'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $l['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="footer_lien_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars((string) $l['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Catégorie du lien -->
                        <div class="admin-field">
                            <label>Catégorie</label>
                            <input type="text" name="categorie" value="<?= htmlspecialchars($l['categorie'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Label / texte du lien -->
                        <div class="admin-field">
                            <label>Label</label>
                            <input type="text" name="label" value="<?= htmlspecialchars($l['label'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : URL du lien -->
                        <div class="admin-field">
                            <label>Lien</label>
                            <input type="text" name="lien" value="<?= htmlspecialchars($l['lien'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $l['ordre'] ?>">
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression du lien -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer ce lien ?');">
                        <input type="hidden" name="action" value="footer_lien_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars((string) $l['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les liens -->
    </tbody>
</table>
<?php else: ?>
<!-- Message affiché s'il n'y a aucun lien -->
<p class="admin-empty">Aucun lien pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des liens -->

<!-- Formulaire d'ajout d'un nouveau lien -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter un lien</legend>
    <form method="post">
        <input type="hidden" name="action" value="footer_lien_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Catégorie du nouveau lien -->
        <div class="admin-field">
            <label>Catégorie</label>
            <input type="text" name="categorie" required>
        </div>
        <!-- Champ : Label du nouveau lien -->
        <div class="admin-field">
            <label>Label</label>
            <input type="text" name="label" required>
        </div>
        <!-- Champ : URL du nouveau lien -->
        <div class="admin-field">
            <label>Lien</label>
            <input type="text" name="lien" required>
        </div>
        <!-- Champ : Ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>

<!-- Liste des liens légaux -->
<h4 style="margin:1.5rem 0 0.75rem;font-size:0.875rem;">Liens légaux</h4>
<!-- Vérifie s'il y a des liens légaux à afficher -->
<?php if ($legals): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Label</th><th>Lien</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque lien légal -->
    <?php foreach ($legals as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['label'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($l['lien'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $l['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="footer_legal_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars((string) $l['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Label du lien légal -->
                        <div class="admin-field">
                            <label>Label</label>
                            <input type="text" name="label" value="<?= htmlspecialchars($l['label'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : URL du lien légal -->
                        <div class="admin-field">
                            <label>Lien</label>
                            <input type="text" name="lien" value="<?= htmlspecialchars($l['lien'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $l['ordre'] ?>">
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression du lien légal -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer ce lien légal ?');">
                        <input type="hidden" name="action" value="footer_legal_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars((string) $l['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les liens légaux -->
    </tbody>
</table>
<?php else: ?>
<!-- Message affiché s'il n'y a aucun lien légal -->
<p class="admin-empty">Aucun lien légal pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des liens légaux -->

<!-- Formulaire d'ajout d'un nouveau lien légal -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter un lien légal</legend>
    <form method="post">
        <input type="hidden" name="action" value="footer_legal_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Label du nouveau lien légal -->
        <div class="admin-field">
            <label>Label</label>
            <input type="text" name="label" required>
        </div>
        <!-- Champ : URL du nouveau lien légal -->
        <div class="admin-field">
            <label>Lien</label>
            <input type="text" name="lien" required>
        </div>
        <!-- Champ : Ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
