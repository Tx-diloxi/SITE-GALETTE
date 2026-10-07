<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$livreurs = $pdo->query("SELECT id, nom, prenom, secteur, telephone, en_ligne, cree_le FROM livreur ORDER BY nom, prenom")->fetchAll();

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h1 style="margin:0">Livreurs</h1>
                <a href="/admin/livreurs/create.php" class="admin-btn admin-btn--primary">+ Ajouter</a>
            </div>

            <?php if (empty($livreurs)): ?>
                <p class="admin-empty">Aucun livreur.</p>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Secteur</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($livreurs as $l): ?>
                            <tr>
                                <td><?= htmlspecialchars($l['prenom'] . ' ' . $l['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($l['secteur'] ?? '', ENT_QUOTES, 'UTF-8') ?: '—' ?></td>
                                <td><?= htmlspecialchars($l['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?: '—' ?></td>
                                <td><?= $l['en_ligne'] ? '<span style="color:#2e7d32">Actif</span>' : '<span style="color:#c62828">Inactif</span>' ?></td>
                                <td>
                                    <a href="/admin/livreurs/edit.php?id=<?= $l['id'] ?>" class="admin-btn admin-btn--small">Éditer</a>
                                    <a href="/admin/livreurs/delete.php?id=<?= $l['id'] ?>" class="admin-btn admin-btn--small admin-btn--danger" onclick="return confirm('Supprimer ce livreur ?')">Suppr.</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
