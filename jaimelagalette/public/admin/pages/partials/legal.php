<?php
declare(strict_types=1);

if (!isset($section) || !is_array($section)) {
    return;
}

$nom_page = $nom_page ?? '';
$legal_id = (int) ($section['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        if ($_POST['action'] === 'legal_section_add' && $legal_id > 0) {
            $titre   = trim($_POST['section_titre'] ?? '');
            $contenu = trim($_POST['section_contenu'] ?? '');
            $ordre   = (int) ($_POST['ordre'] ?? 0);

            $stmt = $pdo->prepare("INSERT INTO legal_Section (partial_legal_id, titre, contenu, ordre) VALUES (:pid, :t, :c, :o)");
            $stmt->execute([
                ':pid' => $legal_id,
                ':t'   => $titre,
                ':c'   => $contenu,
                ':o'   => $ordre,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }

        if ($_POST['action'] === 'legal_section_edit') {
            $section_id = (int) ($_POST['section_id'] ?? 0);
            $titre   = trim($_POST['section_titre'] ?? '');
            $contenu = trim($_POST['section_contenu'] ?? '');
            $ordre   = (int) ($_POST['ordre'] ?? 0);

            $stmt = $pdo->prepare("UPDATE legal_Section SET titre = :t, contenu = :c, ordre = :o WHERE id = :id AND partial_legal_id = :pid");
            $stmt->execute([
                ':t'   => $titre,
                ':c'   => $contenu,
                ':o'   => $ordre,
                ':id'  => $section_id,
                ':pid' => $legal_id,
            ]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }

        if ($_POST['action'] === 'legal_section_delete') {
            $section_id = (int) ($_POST['section_id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM legal_Section WHERE id = :id AND partial_legal_id = :pid");
            $stmt->execute([':id' => $section_id, ':pid' => $legal_id]);

            admin_redirect('admin/pages/edit.php?page=' . rawurlencode($_GET['page'] ?? 'home') . '&saved=1');
        }
    }
}

$sections = $legal_id > 0
    ? $pdo->prepare("SELECT * FROM legal_Section WHERE partial_legal_id = :pid ORDER BY ordre, id")
    : null;
if ($sections) {
    $sections->execute([':pid' => $legal_id]);
    $sections = $sections->fetchAll();
} else {
    $sections = [];
}
?>

<h4 style="margin-bottom:0.75rem;font-size:0.875rem;">Sections de contenu légal</h4>

<?php if ($legal_id === 0): ?>
<p class="admin-empty">La page légale n'existe pas encore en base. Sauvegardez d'abord l'introduction pour créer l'entrée.</p>
<?php return; endif; ?>

<?php if ($sections): ?>
<table class="admin-table" style="margin-bottom:1rem;">
    <thead>
        <tr><th>Titre</th><th>Ordre</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($sections as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $s['ordre'] ?></td>
            <td>
                <button class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.nextElementSibling.style.display='block';this.style.display='none';">Modifier</button>
                <div style="display:none;margin-top:0.5rem;">
                    <form method="post" style="display:flex;flex-direction:column;gap:0.5rem;border:1px solid #e0e0e0;padding:1rem;border-radius:8px;">
                        <input type="hidden" name="action" value="legal_section_edit">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="section_id" value="<?= htmlspecialchars((string) $s['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="admin-field">
                            <label>Titre</label>
                            <input type="text" name="section_titre" value="<?= htmlspecialchars($s['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="admin-field">
                            <label>Contenu (HTML)</label>
                            <textarea name="section_contenu" style="min-height:200px;"><?= htmlspecialchars($s['contenu'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="admin-field">
                            <label>Ordre</label>
                            <input type="number" name="ordre" value="<?= (int) $s['ordre'] ?>">
                        </div>
                        <div class="admin-btn-group">
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Sauvegarder</button>
                            <button type="button" class="admin-btn admin-btn--small admin-btn--secondary" onclick="this.closest('div[style]').style.display='none';this.closest('tr').querySelector('button').style.display='';">Annuler</button>
                        </div>
                    </form>
                    <form method="post" style="margin-top:0.5rem;" onsubmit="return confirm('Supprimer cette section ?');">
                        <input type="hidden" name="action" value="legal_section_delete">
                        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                        <input type="hidden" name="section_id" value="<?= htmlspecialchars((string) $s['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="admin-empty">Aucune section pour le moment.</p>
<?php endif; ?>

<fieldset style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
    <legend style="font-weight:600;font-size:0.875rem;">Ajouter une section</legend>
    <form method="post">
        <input type="hidden" name="action" value="legal_section_add">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
        <div class="admin-field">
            <label>Titre</label>
            <input type="text" name="section_titre" required>
        </div>
        <div class="admin-field">
            <label>Contenu (HTML)</label>
            <textarea name="section_contenu" style="min-height:200px;" required></textarea>
        </div>
        <div class="admin-field">
            <label>Ordre</label>
            <input type="number" name="ordre" value="0">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Ajouter</button>
    </form>
</fieldset>
