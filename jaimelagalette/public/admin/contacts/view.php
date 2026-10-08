<?php
/**
 * Admin - Contacts : détail d'un message de contact.
 */
declare(strict_types=1);

// -----------------------------------------------
// INITIALISATION ET VÉRIFICATION D'ACCÈS
// -----------------------------------------------

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// -----------------------------------------------
// RÉCUPÉRATION DE L'IDENTIFIANT
// -----------------------------------------------

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    admin_redirect('admin/contacts/index.php');
}

// -----------------------------------------------
// RECHERCHE DU MESSAGE
// -----------------------------------------------

$stmt = $pdo->prepare("SELECT * FROM formulaire_contact WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if (!$m) {
    admin_redirect('admin/contacts/index.php');
}

// -----------------------------------------------
// PASSAGE AUTOMATIQUE DE "nouveau" À "lu"
// -----------------------------------------------

if ($m['statut'] === 'nouveau') {
    $upd = $pdo->prepare("UPDATE formulaire_contact SET statut = 'lu' WHERE id = ? AND statut = 'nouveau'");
    $upd->execute([$id]);
    $m['statut'] = 'lu';
}

// -----------------------------------------------
// ENREGISTREMENT D'UNE NOTE INTERNE
// -----------------------------------------------

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $message = admin_alert('Token CSRF invalide.', 'error');
    } else {
        $note = trim($_POST['note_admin'] ?? '');
        $upd = $pdo->prepare("UPDATE formulaire_contact SET note_admin = ? WHERE id = ?");
        $upd->execute([$note, $id]);
        $m['note_admin'] = $note;
        $message = admin_alert('Note enregistrée.');
    }
}

// -----------------------------------------------
// MAPPING STATUT ET LIBELLÉS PROFIL
// -----------------------------------------------

$statut_badge = [
    'nouveau' => 'danger',
    'lu' => 'warning',
    'traite' => 'success',
    'archive' => 'info',
];

$profil_labels = ['b2c' => 'Particulier (B2C)', 'b2b' => 'Professionnel (B2B)'];

// -----------------------------------------------
// AFFICHAGE
// -----------------------------------------------

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Message de <?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="admin-btn-group">
            <a href="index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            <a href="delete.php?id=<?= $id ?>" class="admin-btn admin-btn--danger" onclick="return confirm('Supprimer définitivement ce message\u00a0?')">Supprimer</a>
        </div>
    </div>

    <?= $message ?>

    <div class="admin-card">
        <table class="admin-table" style="margin-bottom:1.5rem">
            <tbody>
                <tr><th style="width:180px">Nom</th><td><?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Email</th><td><a href="mailto:<?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?></a></td></tr>
                <tr><th>Sujet</th><td><?= htmlspecialchars($m['sujet'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Profil</th><td><span class="admin-badge admin-badge--<?= $m['profil'] === 'b2b' ? 'info' : 'success' ?>"><?= htmlspecialchars($profil_labels[$m['profil']] ?? $m['profil'], ENT_QUOTES, 'UTF-8') ?></span></td></tr>
                <?php if ($m['profil'] === 'b2b'): ?>
                <tr><th>Société</th><td><?= htmlspecialchars($m['societe'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Téléphone</th><td><?= htmlspecialchars($m['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Secteur B2B</th><td><?= htmlspecialchars($m['profil_b2b'] ?? '', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <?php endif; ?>
                <tr><th>RGPD accepté</th><td><?= $m['rgpd_accepte'] ? 'Oui' : 'Non' ?></td></tr>
                <tr><th>Date</th><td><?= htmlspecialchars($m['cree_le'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Statut</th><td><span class="admin-badge admin-badge--<?= $statut_badge[$m['statut']] ?? 'secondary' ?>"><?= htmlspecialchars($m['statut'], ENT_QUOTES, 'UTF-8') ?></span></td></tr>
                <?php if ($m['traite_le']): ?>
                <tr><th>Traité le</th><td><?= htmlspecialchars($m['traite_le'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 style="margin-bottom:0.75rem">Message</h2>
        <div style="white-space:pre-wrap;font-size:0.875rem;line-height:1.7;background:var(--blanc-casse);padding:1rem;border-radius:var(--border-radius-btn);margin-bottom:1.5rem">
            <?= nl2br(htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
    </div>

    <div class="admin-card">
        <h2>Changer le statut</h2>
        <div class="admin-btn-group">
            <?php
            $transitions = [
                'nouveau' => 'lu',
                'lu' => 'traite',
                'traite' => 'archive',
            ];
            $next = $transitions[$m['statut']] ?? null;
            ?>
            <?php foreach (['nouveau', 'lu', 'traite', 'archive'] as $s):
                if ($s === $m['statut']): ?>
                <span class="admin-btn admin-btn--small" style="opacity:0.5;cursor:default;background:var(--gris-charbon);color:white"><?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?></span>
                <?php elseif (isset($transitions[$m['statut']]) && $s === $transitions[$m['statut']]): ?>
                <form method="POST" action="change_statut.php" style="display:inline">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="statut" value="<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                    <button type="submit" class="admin-btn admin-btn--small admin-btn--primary">Marquer «&nbsp;<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>&nbsp;»</button>
                </form>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <div class="admin-card">
        <h2>Note interne</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <div class="admin-field">
                <textarea name="note_admin" style="min-height:100px"><?= htmlspecialchars($m['note_admin'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button type="submit" name="save_note" value="1" class="admin-btn admin-btn--primary">Enregistrer la note</button>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
