<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$total = $pdo->query("SELECT COUNT(*) FROM inspection")->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));

$inspections = $pdo->prepare("
    SELECT i.id, i.note_generale, i.commentaire, i.cree_le,
           l.nom AS livreur_nom, l.prenom AS livreur_prenom,
           c.nom AS commercial_nom, c.prenom AS commercial_prenom
    FROM inspection i
    JOIN livreur l ON l.id = i.livreur_id
    JOIN commercial c ON c.id = i.commercial_id
    ORDER BY i.cree_le DESC
    LIMIT :limit OFFSET :offset
");
$inspections->execute([':limit' => $perPage, ':offset' => $offset]);
$inspections = $inspections->fetchAll();

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <h1>Visites mystères</h1>

        <div class="admin-table-controls">
            <span class="admin-table-count"><?= $total ?> inspection(s)</span>
            <a href="export.php" class="admin-btn admin-btn--small">Exporter en CSV</a>
        </div>

        <?php if (empty($inspections)): ?>
            <div class="admin-card">
                <p class="admin-empty">Aucune inspection pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Livreur</th>
                            <th>Commercial</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inspections as $i): ?>
                        <tr>
                            <td class="admin-cell-date"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($i['cree_le'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($i['livreur_prenom'] . ' ' . $i['livreur_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($i['commercial_prenom'] . ' ' . $i['commercial_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="admin-cell-note"><?= $i['note_generale'] !== null ? htmlspecialchars((string) $i['note_generale'] . '/20', ENT_QUOTES, 'UTF-8') : '—' ?></td>
                            <td class="admin-cell-comment"><?= htmlspecialchars(mb_substr($i['commentaire'] ?? '', 0, 80), ENT_QUOTES, 'UTF-8') ?><?= isset($i['commentaire']) && mb_strlen($i['commentaire']) > 80 ? '…' : '' ?></td>
                            <td class="admin-cell-actions">
                                <a href="/admin/visites-mysteres/view.php?id=<?= $i['id'] ?>" class="admin-btn admin-btn--small">Voir</a>
                                <a href="/admin/visites-mysteres/delete.php?id=<?= $i['id'] ?>" class="admin-btn admin-btn--small admin-btn--danger" onclick="return confirm('Supprimer cette inspection ?')">Suppr.</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="?page=<?= $p ?>" class="admin-btn admin-btn--small <?= $p === $page ? 'admin-btn--primary' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
