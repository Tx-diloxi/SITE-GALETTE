<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: processus
 *
 * Admin section editor for the Processus section (title + process steps)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Processus ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour du titre de la section ---
        if ($_POST['action'] === 'processus_section') {
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');

            // Met à jour les champs titre de la section processus
            $stmt = $pdo->prepare("UPDATE partial_Processus SET sous_titre = :st, titre = :t WHERE id = 1");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
        // Fin de processus_section

        // --- Ajout d'une nouvelle étape ---
        if ($_POST['action'] === 'processus_add') {
            $titre       = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);

            // Insère la nouvelle étape en base
            $stmt = $pdo->prepare("INSERT INTO etape_Processus (partial_processus_id, titre, description, ordre) VALUES (1, :t, :d, :o)");
            $stmt->execute([
                ':t' => $titre,
                ':d' => $description,
                ':o' => $ordre,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
        // Fin de processus_add

        // --- Modification d'une étape existante ---
        if ($_POST['action'] === 'processus_edit') {
            $card_id      = (int) ($_POST['card_id'] ?? 0);
            $titre       = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $ordre       = (int) ($_POST['ordre'] ?? 0);

            // Met à jour l'étape ciblée par son ID
            $stmt = $pdo->prepare("UPDATE etape_Processus SET titre = :t, description = :d, ordre = :o WHERE id = :cid AND partial_processus_id = 1");
            $stmt->execute([
                ':t'   => $titre,
                ':d'   => $description,
                ':o'   => $ordre,
                ':cid' => $card_id,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
        // Fin de processus_edit

        // --- Suppression d'une étape ---
        if ($_POST['action'] === 'processus_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime l'étape ciblée par son ID
            $stmt = $pdo->prepare("DELETE FROM etape_Processus WHERE id = :id AND partial_processus_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
        // Fin de processus_delete
    }
    // Fin de la validation du token CSRF
}

// Récupère toutes les étapes du processus pour les afficher
$etapes = $pdo->query("SELECT * FROM etape_Processus WHERE partial_processus_id = 1 ORDER BY ordre, id")->fetchAll();
?>

<!-- Édition du titre de la section Processus -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="processus_section">
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

<!-- Liste des étapes du processus -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Étapes du processus</h4>
<!-- Vérifie s'il y a des étapes à afficher -->
<?php if ($etapes): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Titre</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque étape -->
    <?php foreach ($etapes as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $e['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="processus_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $e['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Titre de l'étape -->
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars($e['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Description de l'étape -->
                        <div class="admin-field">
                            <label>Description</label>
                            <textarea name="description"><?= htmlspecialchars($e['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ : Ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $e['ordre'] ?>">
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette étape ?');">
                        <input type="hidden" name="action" value="processus_delete">
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
<!-- Message affiché s'il n'y a aucune étape -->
<p class="admin-empty">Aucune étape pour le moment.</p>
<?php endif; ?>
<!-- Fin de l'affichage des étapes -->

<!-- Formulaire d'ajout d'une nouvelle étape -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une étape</legend>
    <form method="post">
        <input type="hidden" name="action" value="processus_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Titre de la nouvelle étape -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="titre" required>
        </div>
        <!-- Champ : Description de la nouvelle étape -->
        <div class="admin-field">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>
        <!-- Champ : Ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
