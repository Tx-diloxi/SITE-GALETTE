<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: offres
 *
 * Admin section editor for the Offres d'emploi section (title + job offers)
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section Offres d'emploi ---

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // --- Mise à jour du titre de la section ---
        if ($_POST['action'] === 'offres_section') {
            $sous_titre = trim($_POST['sous_titre'] ?? '');
            $titre      = trim($_POST['titre'] ?? '');

            // Met à jour les champs titre de la section offres
            $stmt = $pdo->prepare("UPDATE partial_Offre SET sous_titre = :st, titre = :t WHERE id = 1");
            $stmt->execute([
                ':st' => $sous_titre,
                ':t'  => $titre,
            ]);

            $redirect_page = 'admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home');
if (!empty($_GET['section'])) $redirect_page .= '&section=' . rawurlencode($_GET['section']);
if (!empty($_GET['only']))    $redirect_page .= '&only=' . rawurlencode($_GET['only']);
$redirect_page .= '&saved=1';
admin_redirect($redirect_page);
        }
        // Fin de offres_section

        // --- Ajout d'une nouvelle offre d'emploi ---
        if ($_POST['action'] === 'offres_add') {
            // Récupère les champs du formulaire
            $titre       = trim($_POST['titre'] ?? '');
            $contrat     = trim($_POST['contrat'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $lieu        = trim($_POST['lieu'] ?? '');
            $point_carte_id = !empty($_POST['point_carte_id']) ? (int)$_POST['point_carte_id'] : null;
            $cta_label   = trim($_POST['cta_label'] ?? '');
            $date_pub    = trim($_POST['date_publication'] ?? '');
            $en_ligne    = isset($_POST['en_ligne']) ? 1 : 0;

            $stmt = $pdo->prepare("INSERT INTO offre_Emploi (partial_offre_id, point_carte_id, titre, contrat, description, lieu, cta_label, date_publication, en_ligne) VALUES (1, :pci, :t, :c, :d, :l, :cl, :dp, :el)");
            $stmt->execute([
                ':pci' => $point_carte_id,
                ':t'   => $titre,
                ':c'   => $contrat !== '' ? $contrat : null,
                ':d'   => $description,
                ':l'   => $lieu !== '' ? $lieu : null,
                ':cl'  => $cta_label !== '' ? $cta_label : null,
                ':dp'  => $date_pub !== '' ? $date_pub : null,
                ':el'  => $en_ligne,
            ]);

            $redirect_page = 'admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home');
if (!empty($_GET['section'])) $redirect_page .= '&section=' . rawurlencode($_GET['section']);
if (!empty($_GET['only']))    $redirect_page .= '&only=' . rawurlencode($_GET['only']);
$redirect_page .= '&saved=1';
admin_redirect($redirect_page);
        }
        // Fin de offres_add

        // --- Modification d'une offre existante ---
        if ($_POST['action'] === 'offres_edit') {
            $card_id      = (int) ($_POST['card_id'] ?? 0);
            $titre       = trim($_POST['titre'] ?? '');
            $contrat     = trim($_POST['contrat'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $lieu        = trim($_POST['lieu'] ?? '');
            $point_carte_id = !empty($_POST['point_carte_id']) ? (int)$_POST['point_carte_id'] : null;
            $cta_label   = trim($_POST['cta_label'] ?? '');
            $date_pub    = trim($_POST['date_publication'] ?? '');
            $en_ligne    = isset($_POST['en_ligne']) ? 1 : 0;

            $stmt = $pdo->prepare("UPDATE offre_Emploi SET point_carte_id = :pci, titre = :t, contrat = :c, description = :d, lieu = :l, cta_label = :cl, date_publication = :dp, en_ligne = :el WHERE id = :cid AND partial_offre_id = 1");
            $stmt->execute([
                ':pci' => $point_carte_id,
                ':t'   => $titre,
                ':c'   => $contrat !== '' ? $contrat : null,
                ':d'   => $description,
                ':l'   => $lieu !== '' ? $lieu : null,
                ':cl'  => $cta_label !== '' ? $cta_label : null,
                ':dp'  => $date_pub !== '' ? $date_pub : null,
                ':el'  => $en_ligne,
                ':cid' => $card_id,
            ]);

            $redirect_page = 'admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home');
if (!empty($_GET['section'])) $redirect_page .= '&section=' . rawurlencode($_GET['section']);
if (!empty($_GET['only']))    $redirect_page .= '&only=' . rawurlencode($_GET['only']);
$redirect_page .= '&saved=1';
admin_redirect($redirect_page);
        }
        // Fin de offres_edit

        // --- Suppression d'une offre ---
        if ($_POST['action'] === 'offres_delete') {
            $card_id = (int) ($_POST['card_id'] ?? 0);
            // Supprime l'offre ciblée par son ID
            $stmt = $pdo->prepare("DELETE FROM offre_Emploi WHERE id = :id AND partial_offre_id = 1");
            $stmt->execute([':id' => $card_id]);

            $redirect_page = 'admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home');
if (!empty($_GET['section'])) $redirect_page .= '&section=' . rawurlencode($_GET['section']);
if (!empty($_GET['only']))    $redirect_page .= '&only=' . rawurlencode($_GET['only']);
$redirect_page .= '&saved=1';
admin_redirect($redirect_page);
        }
        // Fin de offres_delete

        // --- Activation / désactivation d'une offre (toggle) ---
        if ($_POST['action'] === 'offres_toggle') {
            $card_id  = (int) ($_POST['card_id'] ?? 0);
            $en_ligne = isset($_POST['en_ligne']) ? 1 : 0;

            // Bascule le statut en_ligne de l'offre
            $stmt = $pdo->prepare("UPDATE offre_Emploi SET en_ligne = :el WHERE id = :cid AND partial_offre_id = 1");
            $stmt->execute([':el' => $en_ligne, ':cid' => $card_id]);

            $redirect_page = 'admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home');
if (!empty($_GET['section'])) $redirect_page .= '&section=' . rawurlencode($_GET['section']);
if (!empty($_GET['only']))    $redirect_page .= '&only=' . rawurlencode($_GET['only']);
$redirect_page .= '&saved=1';
admin_redirect($redirect_page);
        }
        // Fin de offres_toggle
    }
    // Fin de la validation du token CSRF
}

// Récupère toutes les offres d'emploi pour les afficher
$offres = $pdo->query("SELECT * FROM offre_Emploi WHERE partial_offre_id = 1 ORDER BY id")->fetchAll();
// Récupère la liste des sites pour le sélecteur email
$sites = $pdo->query("SELECT id, nom, email_rh, ville FROM point_Carte WHERE email_rh IS NOT NULL AND email_rh != '' ORDER BY nom")->fetchAll();
// Récupère les villes disponibles pour le lieu
$villes = $pdo->query("SELECT DISTINCT ville FROM point_Carte ORDER BY ville")->fetchAll();
?>

<!-- Édition du titre de la section Offres -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Titre de la section</legend>
    <form method="post">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="offres_section">
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

<!-- Liste des offres d'emploi existantes -->
<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Offres d'emploi</h4>
<!-- Vérifie s'il y a des offres à afficher -->
<?php if ($offres): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Titre</th><th>Contrat</th><th>Lieu</th><th>En ligne</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <!-- Boucle sur chaque offre pour afficher une ligne -->
    <?php foreach ($offres as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($o['contrat'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($o['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <!-- Formulaire toggle : active/désactive l'offre -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="offres_toggle">
                    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                    <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $o['id'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="offres-toggle-<?= $o['id'] ?>" onchange="this.form.submit()" <?= $o['en_ligne'] ? 'checked' : '' ?>>
                        <label class="switch" for="offres-toggle-<?= $o['id'] ?>">
                            <span class="slider"></span>
                        </label>
                    </div>
                </form>
            </td>
            <td>
                <!-- Bouton pour afficher le formulaire de modification -->
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <!-- Formulaire de modification (caché par défaut) -->
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="offres_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $o['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Champ : Titre de l'offre -->
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars($o['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Type de contrat -->
                        <div class="admin-field">
                            <label>Contrat</label>
                            <input type="text" name="contrat" value="<?= htmlspecialchars($o['contrat'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Description du poste -->
                        <div class="admin-field">
                            <label>Description</label>
                            <textarea name="description" required><?= htmlspecialchars($o['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <!-- Champ : Lieu du poste -->
                        <div class="admin-field">
                            <label>Lieu</label>
                            <select name="lieu" required>
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($villes as $v): ?>
                                <option value="<?= htmlspecialchars($v['ville'], ENT_QUOTES, 'UTF-8') ?>"
                                    <?= ($o['lieu'] ?? '') === $v['ville'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v['ville'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Champ : Site lié -->
                        <div class="admin-field">
                            <label>Site lié (email destinataire)</label>
                            <select name="point_carte_id" required>
                                <option value="">— Aucun —</option>
                                <?php foreach ($sites as $s): ?>
                                <option value="<?= (int)$s['id'] ?>"
                                    <?= ($o['point_carte_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nom'] . ' — ' . $s['ville'], ENT_QUOTES, 'UTF-8') ?>
                                    (<?= htmlspecialchars($s['email_rh'], ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Champ : Texte du bouton d'appel à l'action -->
                        <div class="admin-field">
                            <label>CTA label</label>
                            <input type="text" name="cta_label" value="<?= htmlspecialchars($o['cta_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Date de publication -->
                        <div class="admin-field">
                            <label>Date de publication</label>
                            <input type="date" name="date_publication" value="<?= htmlspecialchars($o['date_publication'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <!-- Champ : Statut en ligne -->
                        <div class="admin-field">
                            <div class="container">
                                <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="offres-edit-toggle-<?= $o['id'] ?>" <?= $o['en_ligne'] ? 'checked' : '' ?>>
                                <label class="switch" for="offres-edit-toggle-<?= $o['id'] ?>">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        <!-- Boutons d'action : sauvegarder ou annuler -->
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <!-- Formulaire de suppression -->
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette offre ?');">
                        <input type="hidden" name="action" value="offres_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="card_id" value="<?= htmlspecialchars((string) $o['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- Fin de la boucle sur les offres -->
    </tbody>
</table>
<?php else: ?>
<!-- Message affiché s'il n'y a aucune offre -->
<p class="admin-empty">Aucune offre pour le moment.</p>
<?php endif; ?>
<!-- Fin du affichage des offres -->

<!-- Formulaire d'ajout d'une nouvelle offre -->
<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une offre</legend>
    <form method="post">
        <!-- Champ caché : identifiant d'action -->
        <input type="hidden" name="action" value="offres_add">
        <!-- Champ caché : token CSRF anti-falsification -->
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <!-- Champ : Titre de la nouvelle offre -->
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="titre" required>
        </div>
        <!-- Champ : Type de contrat -->
        <div class="admin-field">
            <label>Contrat</label>
            <input type="text" name="contrat" required>
        </div>
        <!-- Champ : Description du poste -->
        <div class="admin-field">
            <label>Description</label>
            <textarea name="description" required></textarea>
        </div>
        <!-- Champ : Lieu du poste -->
        <div class="admin-field">
            <label>Lieu</label>
            <select name="lieu" required>
                <option value="">— Sélectionner —</option>
                <?php foreach ($villes as $v): ?>
                <option value="<?= htmlspecialchars($v['ville'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($v['ville'], ENT_QUOTES, 'UTF-8') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Champ : Site lié -->
        <div class="admin-field">
            <label>Site lié (email destinataire)</label>
            <select name="point_carte_id" required>
                <option value="">— Aucun —</option>
                <?php foreach ($sites as $s): ?>
                <option value="<?= (int)$s['id'] ?>">
                    <?= htmlspecialchars($s['nom'] . ' — ' . $s['ville'], ENT_QUOTES, 'UTF-8') ?>
                    (<?= htmlspecialchars($s['email_rh'], ENT_QUOTES, 'UTF-8') ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Champ : Texte du bouton d'appel à l'action -->
        <div class="admin-field">
            <label>CTA label</label>
            <input type="text" name="cta_label" required>
        </div>
        <!-- Champ : Date de publication -->
        <div class="admin-field">
            <label>Date de publication</label>
            <input type="date" name="date_publication" required>
        </div>
        <!-- Champ : Statut en ligne (coché par défaut) -->
        <div class="admin-field">
            <div class="container">
                <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="offres-add-toggle" checked>
                <label class="switch" for="offres-add-toggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
