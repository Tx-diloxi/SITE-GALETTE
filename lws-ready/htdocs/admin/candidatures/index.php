<?php
/**
 * Admin - Candidatures : liste des candidatures reçues.
 */
declare(strict_types=1);

// -----------------------------------------------
// INITIALISATION ET VÉRIFICATION D'ACCÈS
// -----------------------------------------------

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// -----------------------------------------------
// PARAMÈTRES DE PAGINATION ET FILTRE
// -----------------------------------------------

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$filter = $_GET['filter'] ?? 'tous';

// -----------------------------------------------
// VÉRIFICATION DE LA COLONNE "lue"
// -----------------------------------------------

$a_colonne_lue = false;
try {
    $pdo->query("SELECT lue FROM applications LIMIT 1");
    $a_colonne_lue = true;
} catch (PDOException $e) {
    $a_colonne_lue = false;
}

// -----------------------------------------------
// CONSTRUCTION DE LA CLAUSE WHERE
// -----------------------------------------------

$where = '';
$params = [];
if ($a_colonne_lue && $filter === 'non_lus') {
    $where = 'WHERE lue = FALSE';
}

// -----------------------------------------------
// SÉLECTION DES COLONNES
// -----------------------------------------------

$select_cols = $a_colonne_lue ? 'id, prenom, nom, poste_souhaite, cree_le, cv_path, lue' : 'id, prenom, nom, poste_souhaite, cree_le, cv_path';

// -----------------------------------------------
// COMPTAGE ET PAGINATION
// -----------------------------------------------

$total = $pdo->prepare("SELECT COUNT(*) FROM applications $where");
$total->execute($params);
$total_count = (int)$total->fetchColumn();
$total_pages = max(1, (int)ceil($total_count / $per_page));
$offset = ($page - 1) * $per_page;

// -----------------------------------------------
// RÉCUPÉRATION DES CANDIDATURES
// -----------------------------------------------

$stmt = $pdo->prepare("SELECT $select_cols FROM applications $where ORDER BY cree_le DESC LIMIT " . (int)$per_page . " OFFSET " . (int)$offset);
$stmt->execute($params);
$candidatures = $stmt->fetchAll();

// -----------------------------------------------
// AFFICHAGE
// -----------------------------------------------

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Candidatures</h1>
    </div>

    <div class="admin-filters">
        <div class="admin-field">
            <label>Filtre</label>
            <select onchange="window.location.href='?filter='+this.value">
                <option value="tous" <?= $filter === 'tous' ? 'selected' : '' ?>>Tous</option>
                <option value="non_lus" <?= $filter === 'non_lus' ? 'selected' : '' ?>>Non lus</option>
            </select>
        </div>
        <div class="admin-field">
            <label>&nbsp;</label>
            <span class="admin-empty"><?= htmlspecialchars((string)$total_count, ENT_QUOTES, 'UTF-8') ?> candidature<?= $total_count > 1 ? 's' : '' ?></span>
        </div>
    </div>

    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Poste souhaité</th>
                    <th>Date</th>
                    <th>CV</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($candidatures)): ?>
                <tr><td colspan="5" class="admin-empty">Aucune candidature.</td></tr>
                <?php endif; ?>
                <?php foreach ($candidatures as $c): ?>
                <tr onclick="window.location.href='view.php?id=<?= htmlspecialchars((string)(int)$c['id'], ENT_QUOTES, 'UTF-8') ?>'" style="cursor:pointer">
                    <td><strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($c['poste_souhaite'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['cree_le'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($c['cv_path']): ?>
                        <a href="/assets/uploads/cv/<?= htmlspecialchars($c['cv_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" onclick="event.stopPropagation()">Télécharger</a>
                        <?php else: ?>
                        <span class="admin-empty">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (($c['lue'] ?? false)): ?>
                        <span class="admin-badge admin-badge--success">Lu</span>
                        <?php else: ?>
                        <span class="admin-badge admin-badge--danger">Non lu</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="admin-pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= htmlspecialchars((string)$i, ENT_QUOTES, 'UTF-8') ?>&filter=<?= rawurlencode($filter) ?>" class="<?= $i === $page ? 'admin-pagination--active' : '' ?>"><?= htmlspecialchars((string)$i, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
