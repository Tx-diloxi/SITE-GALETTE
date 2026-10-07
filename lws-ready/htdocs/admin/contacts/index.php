<?php
/**
 * Admin - Contacts : liste des messages de contact.
 */
declare(strict_types=1);

// -----------------------------------------------
// INITIALISATION ET VÉRIFICATION D'ACCÈS
// -----------------------------------------------

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// -----------------------------------------------
// PARAMÈTRES DE PAGINATION ET FILTRES
// -----------------------------------------------

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$filter_statut = $_GET['statut'] ?? '';
$filter_profil = $_GET['profil'] ?? '';

// -----------------------------------------------
// CONSTRUCTION DE LA CLAUSE WHERE
// -----------------------------------------------

$where = [];
$params = [];
if ($filter_statut !== '') {
    $where[] = 'statut = ?';
    $params[] = $filter_statut;
}
if ($filter_profil !== '') {
    $where[] = 'profil = ?';
    $params[] = $filter_profil;
}
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// -----------------------------------------------
// COMPTAGE ET PAGINATION
// -----------------------------------------------

$total = $pdo->prepare("SELECT COUNT(*) FROM formulaire_contact $where_clause");
$total->execute($params);
$total_count = (int)$total->fetchColumn();
$total_pages = max(1, (int)ceil($total_count / $per_page));
$offset = ($page - 1) * $per_page;

// -----------------------------------------------
// RÉCUPÉRATION DES MESSAGES
// -----------------------------------------------

$stmt = $pdo->prepare("SELECT id, nom, email, sujet, profil, statut, cree_le FROM formulaire_contact $where_clause ORDER BY cree_le DESC LIMIT " . (int)$per_page . " OFFSET " . (int)$offset);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

// -----------------------------------------------
// MAPPING STATUT VERS COULEUR
// -----------------------------------------------

$statut_badge = [
    'nouveau' => 'danger',
    'lu' => 'warning',
    'traite' => 'success',
    'archive' => 'info',
];

// -----------------------------------------------
// AFFICHAGE
// -----------------------------------------------

require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Messages contact</h1>
    </div>

    <form class="admin-filters" method="GET">
        <div class="admin-field">
            <label>Statut</label>
            <select name="statut" onchange="this.form.submit()">
                <option value="">Tous</option>
                <option value="nouveau" <?= $filter_statut === 'nouveau' ? 'selected' : '' ?>>Nouveau</option>
                <option value="lu" <?= $filter_statut === 'lu' ? 'selected' : '' ?>>Lu</option>
                <option value="traite" <?= $filter_statut === 'traite' ? 'selected' : '' ?>>Traité</option>
                <option value="archive" <?= $filter_statut === 'archive' ? 'selected' : '' ?>>Archivé</option>
            </select>
        </div>
        <div class="admin-field">
            <label>Profil</label>
            <select name="profil" onchange="this.form.submit()">
                <option value="">Tous</option>
                <option value="b2c" <?= $filter_profil === 'b2c' ? 'selected' : '' ?>>B2C</option>
                <option value="b2b" <?= $filter_profil === 'b2b' ? 'selected' : '' ?>>B2B</option>
            </select>
        </div>
        <div class="admin-field">
            <label>&nbsp;</label>
            <span class="admin-empty"><?= htmlspecialchars((string)$total_count, ENT_QUOTES, 'UTF-8') ?> message<?= $total_count > 1 ? 's' : '' ?></span>
        </div>
        <?php if ($filter_statut !== '' || $filter_profil !== ''): ?>
        <div class="admin-field">
            <label>&nbsp;</label>
            <a href="index.php" class="admin-btn admin-btn--small admin-btn--outline" style="color:var(--nuit);border-color:#ccc">Réinitialiser</a>
        </div>
        <?php endif; ?>
    </form>

    <div class="admin-card">

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Sujet</th>
                    <th>Profil</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                <tr><td colspan="7" class="admin-empty">Aucun message.</td></tr>
                <?php endif; ?>
                <?php foreach ($contacts as $m): ?>
                <tr onclick="window.location.href='view.php?id=<?= htmlspecialchars((string)(int)$m['id'], ENT_QUOTES, 'UTF-8') ?>'" style="cursor:pointer">
                    <td><strong><?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(mb_substr($m['sujet'], 0, 60), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($m['sujet']) > 60 ? '…' : '' ?></td>
                    <td><span class="admin-badge admin-badge--<?= $m['profil'] === 'b2b' ? 'info' : 'success' ?>"><?= htmlspecialchars($m['profil'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><span class="admin-badge admin-badge--<?= $statut_badge[$m['statut']] ?? 'secondary' ?>"><?= htmlspecialchars($m['statut'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= htmlspecialchars($m['cree_le'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td onclick="event.stopPropagation()">
                        <form method="POST" action="change_statut.php" style="display:flex;gap:0.25rem;align-items:center">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)(int)$m['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                            <select name="statut" class="admin-statut-select" style="padding:4px 6px;font-size:0.75rem;border:1px solid #ddd;border-radius:4px" onchange="this.form.submit()">
                                <option value="nouveau" <?= $m['statut'] === 'nouveau' ? 'selected' : '' ?>>Nouveau</option>
                                <option value="lu" <?= $m['statut'] === 'lu' ? 'selected' : '' ?>>Lu</option>
                                <option value="traite" <?= $m['statut'] === 'traite' ? 'selected' : '' ?>>Traité</option>
                                <option value="archive" <?= $m['statut'] === 'archive' ? 'selected' : '' ?>>Archivé</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="admin-pagination">
            <?php
            $qs = '';
            if ($filter_statut !== '') $qs .= '&statut=' . rawurlencode($filter_statut);
            if ($filter_profil !== '') $qs .= '&profil=' . rawurlencode($filter_profil);
            for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= htmlspecialchars((string)$i, ENT_QUOTES, 'UTF-8') . $qs ?>" class="<?= $i === $page ? 'admin-pagination--active' : '' ?>"><?= htmlspecialchars((string)$i, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
