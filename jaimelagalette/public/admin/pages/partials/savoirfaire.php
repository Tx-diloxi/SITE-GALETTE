<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: savoirfaire
 *
 * Admin section editor for the Savoir-faire section (title + cards)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Savoir-faire ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour du titre et du CTA de la section ---
        if ($_POST['action'] === 'savoirfaire_section') {
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');
            $cta_label  = trim($_POST['cta_label'] ?? '');
            $cta_lien   = trim($_POST['cta_lien'] ?? '');

            // Met à jour les champs de la section savoir-faire
            $stmt = $pdo->prepare("UPDATE partial_SavoirFaire SET sous_titre = :st, titre = :t, cta_label = :cl, cta_lien = :cln WHERE id = 1");
            $stmt->execute([
                ':st'  => $sous_titre,
                ':t'   => $titre,
                ':cl'  => $cta_label !== '' ? $cta_label : null,
                ':cln' => $cta_lien !== '' ? $cta_lien : null,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de savoirfaire_section

        // --- Ajout d'une nouvelle carte savoir-faire ---
        if ($_POST['action'] === 'savoirfaire_add') {
            $sous_titre   = trim($_POST['sous_titre'] ?? '');
            $titre        = trim($_POST['titre'] ?? '');
            $contenu      = trim($_POST['contenu'] ?? '');
            $alt_fond     = trim($_POST['alt_fond'] ?? '');
            $alt_label    = trim($_POST['alt_label'] ?? '');
            $ordre        = (int) ($_POST['ordre'] ?? 0);
            $image_fond   = null;
            $image_label  = null;

            // Vérifie si une image de fond a été uploadée
            $image_fond = admin_upload_or_keep('image_fond', $image_fond);
            // Vérifie si une image label a été uploadée
            $image_label = admin_upload_or_keep('image_label', $image_label);

            // Insère la nouvelle carte en base
            $stmt = $pdo->prepare("INSERT INTO card_SavoirFaire (partial_savoirfaire_id, sous_titre, titre, contenu, image_fond, alt_fond, image_label, alt_label, ordre) VALUES (1, :st, :t, :c, :if, :af, :il, :al, :o)");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
                ':c'  => $contenu,
                ':if' => $image_fond,
                ':af' => $alt_fond,
                ':il' => $image_label,
                ':al' => $alt_label,
                ':o'  => $ordre,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de savoirfaire_add

        // --- Modification d'une carte savoir-faire existante ---
        if ($_POST['action'] === 'savoirfaire_edit') {
            $card_id       = (int) ($_POST['card_id'] ?? 0);
            $sous_titre   = trim($_POST['sous_titre'] ?? '');
            $titre        = trim($_POST['titre'] ?? '');
            $contenu      = trim($_POST['contenu'] ?? '');
            $alt_fond     = trim($_POST['alt_fond'] ?? '');
            $alt_label    = trim($_POST['alt_label'] ?? '');
            $ordre        = (int) ($_POST['ordre'] ?? 0);
            // Conserve les images existantes par défaut
            $image_fond   = $_POST['image_fond_current'] ?? null;
            $image_label  = $_POST['image_label_current'] ?? null;

            // Vérifie si une nouvelle image de fond a été uploadée
            $image_fond = admin_upload_or_keep('image_fond', $image_fond);
            // Vérifie si une nouvelle image label a été uploadée
            $image_label = admin_upload_or_keep('image_label', $image_label);

            // Met à jour la carte ciblée par son ID
            $stmt = $pdo->prepare("UPDATE card_SavoirFaire SET sous_titre = :st, titre = :t, contenu = :c, image_fond = :if, alt_fond = :af, image_label = :il, alt_label = :al, ordre = :o WHERE id = :cid AND partial_savoirfaire_id = 1");
            $stmt->execute([
                ':st'  => $sous_titre,
                ':t'   => $titre,
                ':c'   => $contenu,
                ':if'  => $image_fond,
                ':af'  => $alt_fond,
                ':il'  => $image_label,
                ':al'  => $alt_label,
                ':o'   => $ordre,
                ':cid' => $card_id,
            ]);

            admin_redirect_page_saved();
        }
        // Fin de savoirfaire_edit

        // --- Suppression d'une carte savoir-faire ---
        if ($_POST['action'] === 'savoirfaire_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime la carte ciblée par son ID
            $stmt = $pdo->prepare("DELETE FROM card_SavoirFaire WHERE id = :id AND partial_savoirfaire_id = 1");
            $stmt->execute([':id' => $card_id]);

            admin_redirect_page_saved();
        }
        // Fin de savoirfaire_delete
    }
    // Fin de la validation du token CSRF
}

// Récupère toutes les cartes savoir-faire pour les afficher
$cards = $pdo->query("SELECT * FROM card_SavoirFaire WHERE partial_savoirfaire_id = 1 ORDER BY ordre, id")->fetchAll();
?>

<!-- Édition du titre et du CTA de la section Savoir-faire -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="savoirfaire_section">
        <!-- Champ caché : token CSRF anti-falsification -->
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Sous-titre de la section -->
        <div class="admin-field">
            <label>Sous-titre</label>
            <input type="text" name="sous_titre" value="<?= htmlspecialchars($section['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <!-- Champ : Titre de la section -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <!-- Champ : Texte du bouton d'appel à l'action -->
        <div class="admin-field">
            <label>CTA label</label>
            <input type="text" name="cta_label" value="<?= htmlspecialchars($section['cta_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <!-- Champ : Lien du bouton d'appel à l'action -->
        <div class="admin-field">
            <label>CTA lien</label>
            <input type="text" name="cta_lien" value="<?= htmlspecialchars($section['cta_lien'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Enregistrer la section</button>
    </form>
</fieldset>

<!-- Liste des cartes savoir-faire -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Cartes savoir-faire</h4>
<!-- Vérifie s'il y a des cartes à afficher -->
<?php if ($cards): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Titre</th><th>Sous-titre</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque carte savoir-faire -->
    <?php foreach ($cards as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($c['sous_titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $c['ordre'] ?></td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="savoirfaire_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $c['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champs cachés : images actuelles pour les conserver si non remplacées -->
                        <input type="hidden" name="image_fond_current" value="<?= htmlspecialchars($c['image_fond'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="image_label_current" value="<?= htmlspecialchars($c['image_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Sous-titre de la carte -->
                        <div class="admin-field">
                            <label>Sous-titre</label>
                            <input type="text" name="sous_titre" value="<?= htmlspecialchars($c['sous_titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Titre de la carte -->
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars($c['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Contenu texte -->
                        <div class="admin-field">
                            <label>Contenu</label>
                            <textarea name="contenu"><?= htmlspecialchars($c['contenu'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ : Ordre d'affichage -->
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $c['ordre'] ?>">
                        </div>
                        <!-- Champ : Upload de l'image de fond -->
                        <div class="admin-field">
                            <label>Image de fond</label>
                            <input type="file" name="image_fond" accept="image/*">
                            <!-- Affiche l'aperçu de l'image de fond existante -->
                            <?php if (!empty($c['image_fond'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($c['image_fond'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                            <!-- Fin de l'affichage de l'image de fond existante -->
                        </div>
                        <!-- Champ : Texte alternatif de l'image de fond -->
                        <div class="admin-field">
                            <label>Alt fond</label>
                            <input type="text" name="alt_fond" value="<?= htmlspecialchars($c['alt_fond'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Champ : Upload de l'image label -->
                        <div class="admin-field">
                            <label>Image label</label>
                            <input type="file" name="image_label" accept="image/*">
                            <!-- Affiche l'aperçu de l'image label existante -->
                            <?php if (!empty($c['image_label'])): ?>
                                <span class="admin-image-current"><img src="<?= htmlspecialchars($c['image_label'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-preview"></span>
                            <?php endif; ?>
                            <!-- Fin de l'affichage de l'image label existante -->
                        </div>
                        <!-- Champ : Texte alternatif de l'image label -->
                        <div class="admin-field">
                            <label>Alt label</label>
                            <input type="text" name="alt_label" value="<?= htmlspecialchars($c['alt_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette carte ?');">
                        <input type="hidden" name="action" value="savoirfaire_delete">
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

<!-- Formulaire d'ajout d'une nouvelle carte savoir-faire -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une carte</legend>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="savoirfaire_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Sous-titre de la carte -->
        <div class="admin-field">
            <label>Sous-titre</label>
            <input type="text" name="sous_titre" required>
        </div>
        <!-- Champ : Titre de la carte -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="titre" required>
        </div>
        <!-- Champ : Contenu texte -->
        <div class="admin-field">
            <label>Contenu</label>
            <textarea name="contenu"></textarea>
        </div>
        <!-- Champ : Ordre d'affichage -->
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <!-- Champ : Upload de l'image de fond -->
        <div class="admin-field">
            <label>Image de fond</label>
            <input type="file" name="image_fond" accept="image/*">
        </div>
        <!-- Champ : Texte alternatif de l'image de fond -->
        <div class="admin-field">
            <label>Alt fond</label>
            <input type="text" name="alt_fond">
        </div>
        <!-- Champ : Upload de l'image label -->
        <div class="admin-field">
            <label>Image label</label>
            <input type="file" name="image_label" accept="image/*">
        </div>
        <!-- Champ : Texte alternatif de l'image label -->
        <div class="admin-field">
            <label>Alt label</label>
            <input type="text" name="alt_label">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
