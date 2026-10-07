<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: label
 *
 * Admin section editor for the Labels section (text fields + label cards)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire label ---
// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère le token CSRF pour vérifier la validité du formulaire
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie si le token CSRF est valide
    if (!admin_csrf_verify($token)) {
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour de la section label ---
        if ($_POST['action'] === 'label_section') {
            // Récupère et nettoie les champs du formulaire
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');

            // Met à jour les champs de la section label (id = 1)
            $stmt = $pdo->prepare("UPDATE partial_Label SET sous_titre = :st, titre = :t WHERE id = 1");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
            ]);

            // Redirige vers la page d'édition avec un indicateur de succès
            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }

        // --- Ajout d'un nouveau label ---
        if ($_POST['action'] === 'label_add') {
            // Récupère les champs du formulaire d'ajout
            $logo  = null;
            $alt   = trim($_POST['alt'] ?? '');
            $lien  = trim($_POST['lien'] ?? '');
            $ordre = (int) ($_POST['ordre'] ?? 0);

            // Vérifie si un fichier logo a été téléchargé
            if (!empty($_FILES['logo']['name'])) {
                $uploaded = admin_upload_image($_FILES['logo']);
                if ($uploaded) $logo = $uploaded;
            }

            // Insère le nouveau label en base de données
            $stmt = $pdo->prepare("INSERT INTO card_Label (partial_label_id, logo, alt, lien, ordre) VALUES (1, :l, :a, :li, :o)");
            $stmt->execute([
                ':l'  => $logo,
                ':a'  => $alt,
                ':li' => $lien !== '' ? $lien : null,
                ':o'  => $ordre,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }

        // --- Modification d'un label existant ---
        if ($_POST['action'] === 'label_edit') {
            // Récupère l'identifiant et les champs du formulaire d'édition
            $card_id = (int) ($_POST['card_id'] ?? 0);
            $alt    = trim($_POST['alt'] ?? '');
            $lien   = trim($_POST['lien'] ?? '');
            $ordre  = (int) ($_POST['ordre'] ?? 0);
            $logo   = $_POST['logo_current'] ?? null;

            // Vérifie si un nouveau logo a été téléchargé
            if (!empty($_FILES['logo']['name'])) {
                $uploaded = admin_upload_image($_FILES['logo']);
                if ($uploaded) $logo = $uploaded;
            }

            // Met à jour le label existant
            $stmt = $pdo->prepare("UPDATE card_Label SET logo = :l, alt = :a, lien = :li, ordre = :o WHERE id = :cid AND partial_label_id = 1");
            $stmt->execute([
                ':l'   => $logo,
                ':a'   => $alt,
                ':li'  => $lien !== '' ? $lien : null,
                ':o'   => $ordre,
                ':cid' => $card_id,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }

        // --- Suppression d'un label ---
        if ($_POST['action'] === 'label_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime le label de la base de données
            $stmt = $pdo->prepare("DELETE FROM card_Label WHERE id = :id AND partial_label_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
    }
}
// Fin du traitement du formulaire

// Récupère tous les labels depuis la base de données, triés par ordre puis par id
$cards = $pdo->query("SELECT * FROM card_Label WHERE partial_label_id = 1 ORDER BY ordre, id")->fetchAll();
?>

<!-- Formulaire d'édition des textes de la section label -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <input type="hidden" name="action" value="label_section">
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
        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer la section</button>
    </form>
</fieldset>

<!-- Liste des labels -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Labels</h4>
<?php if ($cards): ?>
<!-- Vérifie s'il y a des labels à afficher -->
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Logo</th><th>Alt</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque label pour afficher une ligne -->
    <?php foreach ($cards as $c): ?>
        <tr>
            <!-- Affiche le logo si présent -->
            <td><?php if (!empty($c['logo'])): ?><img src="<?= htmlspecialchars($c['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb"><?php endif; ?></td>
            <td><?= htmlspecialchars($c['alt'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $c['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire d'édition -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire d'édition caché par défaut -->
                <div style="display:none;margin-top:0.5rem;">
                    <!-- Formulaire de modification d'un label -->
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="label_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="logo_current" value="<?= htmlspecialchars($c['logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ upload du logo -->
                        <div class="admin-field">
                            <label>Logo</label>
                            <input type="file" name="logo" accept="image/*">
                            <?php if (!empty($c['logo'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($c['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                        </div>
                        <!-- Champ texte alternatif -->
                        <div class="admin-field">
                            <label>Alt</label>
                            <input type="text" name="alt" value="<?= htmlspecialchars($c['alt'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ lien URL -->
                        <div class="admin-field">
                            <label>Lien</label>
                            <input type="text" name="lien" value="<?= htmlspecialchars($c['lien'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Champ ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $c['ordre'] ?>">
                        </div>
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression d'un label -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer ce label ?');">
                        <input type="hidden" name="action" value="label_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les labels -->
    </tbody>
</table>
<?php else: ?>
<!-- Message si aucun label n'existe -->
<p class="admin-empty">Aucun label pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des labels -->

<!-- Formulaire d'ajout d'un nouveau label -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter un label</legend>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="label_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ logo du nouveau label -->
        <div class="admin-field">
            <label>Logo</label>
            <input type="file" name="logo" accept="image/*" required>
        </div>
        <!-- Champ texte alternatif -->
        <div class="admin-field">
            <label>Alt</label>
            <input type="text" name="alt" required>
        </div>
        <!-- Champ lien URL -->
        <div class="admin-field">
            <label>Lien</label>
            <input type="text" name="lien">
        </div>
        <!-- Champ ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
