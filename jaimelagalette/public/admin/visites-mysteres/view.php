<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$inspection = $pdo->prepare("
    SELECT i.*, l.nom AS livreur_nom, l.prenom AS livreur_prenom, l.secteur,
           c.nom AS commercial_nom, c.prenom AS commercial_prenom, c.email AS commercial_email
    FROM inspection i
    JOIN livreur l ON l.id = i.livreur_id
    JOIN commercial c ON c.id = i.commercial_id
    WHERE i.id = :id
");
$inspection->execute([':id' => $id]);
$inspection = $inspection->fetch();

if (!$inspection) {
    header('Location: list.php');
    exit;
}

$reponses = $pdo->prepare("
    SELECT r.note, r.commentaire, q.question, q.categorie
    FROM inspection_reponse r
    JOIN inspection_question q ON q.id = r.question_id
    WHERE r.inspection_id = :id
    ORDER BY q.ordre
");
$reponses->execute([':id' => $id]);
$reponses = $reponses->fetchAll();

$photos = $pdo->prepare("SELECT chemin FROM inspection_photo WHERE inspection_id = :id");
$photos->execute([':id' => $id]);
$photos = $photos->fetchAll();

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h1 style="margin:0">Détail de l'inspection #<?= $id ?></h1>
                <a href="/admin/visites-mysteres/list.php" class="admin-btn">← Retour</a>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
                <div>
                    <strong>Livreur :</strong>
                    <?= htmlspecialchars($inspection['livreur_prenom'] . ' ' . $inspection['livreur_nom'], ENT_QUOTES, 'UTF-8') ?>
                    <?= $inspection['secteur'] ? ' — ' . htmlspecialchars($inspection['secteur'], ENT_QUOTES, 'UTF-8') : '' ?>
                </div>
                <div><strong>Commercial :</strong> <?= htmlspecialchars($inspection['commercial_prenom'] . ' ' . $inspection['commercial_nom'], ENT_QUOTES, 'UTF-8') ?></div>
                <div><strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($inspection['cree_le'])), ENT_QUOTES, 'UTF-8') ?></div>
                <div><strong>Note globale :</strong> <?= $inspection['note_generale'] !== null ? htmlspecialchars((string) $inspection['note_generale'] . '/20', ENT_QUOTES, 'UTF-8') : 'Non notée' ?></div>
            </div>

            <?php if ($inspection['commentaire']): ?>
            <div style="margin-bottom:24px">
                <strong>Commentaire général :</strong>
                <p style="margin:8px 0 0;white-space:pre-wrap"><?= nl2br(htmlspecialchars($inspection['commentaire'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="admin-card" style="margin-top:20px">
            <h2>Réponses aux questions</h2>
            <?php if (empty($reponses)): ?>
                <p class="admin-empty">Aucune réponse enregistrée.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Question</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reponses as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['categorie'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($r['question'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <span style="color:<?= $s <= $r['note'] ? '#EE7325' : '#ddd' ?>;font-size:18px">★</span>
                                <?php endfor; ?>
                            </td>
                            <td><?= htmlspecialchars($r['commentaire'] ?? '', ENT_QUOTES, 'UTF-8') ?: '—' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <?php if (!empty($photos)): ?>
        <div class="admin-card" style="margin-top:20px">
            <h2>Photos (<?= count($photos) ?>)</h2>
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px">
                <?php foreach ($photos as $p): ?>
                <a href="<?= htmlspecialchars($p['chemin'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                    <img src="<?= htmlspecialchars($p['chemin'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo inspection" style="width:180px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #ddd">
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div style="margin-top:20px">
            <a href="/admin/visites-mysteres/delete.php?id=<?= $id ?>" class="admin-btn admin-btn--danger" onclick="return confirm('Supprimer cette inspection ainsi que toutes ses données ?')">Supprimer cette inspection</a>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
