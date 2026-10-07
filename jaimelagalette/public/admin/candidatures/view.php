<?php
/**
 * Admin - Candidatures : détail d'une candidature.
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
    admin_redirect('index.php');
}

// -----------------------------------------------
// RECHERCHE DE LA CANDIDATURE
// -----------------------------------------------

$stmt = $pdo->prepare("
    SELECT a.*, o.titre AS offre_titre
    FROM applications a
    LEFT JOIN offre_Emploi o ON a.offre_emploi_id = o.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    admin_redirect('index.php');
}

// -----------------------------------------------
// MARQUAGE COMME "LUE"
// -----------------------------------------------

$mark_stmt = $pdo->prepare("UPDATE applications SET lue = TRUE WHERE id = ? AND lue = FALSE");
$mark_stmt->execute([$id]);

// -----------------------------------------------
// AFFICHAGE
// -----------------------------------------------

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Candidature</h1>
        <div class="admin-btn-group">
            <a href="index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            <form action="delete.php" method="POST" data-confirm-form="Supprimer cette candidature ?" style="display:inline">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                <button type="submit" class="admin-btn admin-btn--danger">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <table class="admin-table" style="margin-bottom:1.5rem">
            <tbody>
                <tr><th style="width:180px">Nom complet</th><td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Email</th><td><a href="mailto:<?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?></a></td></tr>
                <tr><th>Type</th><td><?= $c['offre_emploi_id'] ? 'Candidature sur offre' : 'Candidature spontanée' ?></td></tr>
                <tr><th>Poste souhaité</th><td><?= htmlspecialchars($c['poste_souhaite'], ENT_QUOTES, 'UTF-8') ?><?php if ($c['offre_titre']): ?> <span class="admin-badge admin-badge--info">Offre : <?= htmlspecialchars($c['offre_titre'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></td></tr>
                <tr><th>Date de candidature</th><td><?= htmlspecialchars($c['cree_le'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>CV</th><td><?php if ($c['cv_path']): ?><a href="/assets/uploads/cv/<?= htmlspecialchars($c['cv_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">Télécharger le CV</a><?php else: ?><span class="admin-empty">Aucun CV</span><?php endif; ?></td></tr>
                <tr><th>Statut</th><td><?= $c['lue'] ? 'Lu' : 'Non lu' ?></td></tr>
            </tbody>
        </table>

        <h2 style="margin-bottom:0.75rem">Lettre de motivation</h2>
        <div style="white-space:pre-wrap;font-size:0.875rem;line-height:1.7;background:var(--blanc-casse);padding:1rem;border-radius:var(--border-radius-btn)">
            <?= nl2br(htmlspecialchars($c['message'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
