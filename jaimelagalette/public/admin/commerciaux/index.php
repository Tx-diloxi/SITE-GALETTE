<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$commerciaux = $pdo->query("SELECT id, nom, prenom, email, login, telephone, en_ligne, cree_le FROM commercial ORDER BY nom, prenom")->fetchAll();

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-dashboard">
        <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h1 style="margin:0">Commerciaux</h1>
                <a href="/admin/commerciaux/create.php" class="admin-btn admin-btn--primary">+ Ajouter</a>
            </div>

            <?php if (empty($commerciaux)): ?>
                <p class="admin-empty">Aucun commercial.</p>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Login</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commerciaux as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($c['login'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($c['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?: '—' ?></td>
                                <td><?= $c['en_ligne'] ? '<span style="color:#2e7d32">Actif</span>' : '<span style="color:#c62828">Inactif</span>' ?></td>
                                <td>
                                    <a href="/admin/commerciaux/edit.php?id=<?= $c['id'] ?>" class="admin-btn admin-btn--small">Éditer</a>
                                    <a href="/admin/commerciaux/delete.php?id=<?= $c['id'] ?>" class="admin-btn admin-btn--small admin-btn--danger" onclick="return confirm('Supprimer ce commercial ?')">Suppr.</a>
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
