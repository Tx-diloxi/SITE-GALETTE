<?php
/**
 * Admin - Gestion des produits
 * Page de listing avec pagination et filtres
 */
declare(strict_types=1);

// ---- Initialisation ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

// ---- Pagination ----
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// ---- Filtres ----
$where = [];
$params = [];

if (!empty($_GET['marque_id'])) {
    $where[] = 'p.marque_id = :marque_id';
    $params[':marque_id'] = (int)$_GET['marque_id'];
}
if (isset($_GET['statut']) && $_GET['statut'] !== '') {
    $where[] = 'p.en_ligne = :statut';
    $params[':statut'] = (int)(bool)$_GET['statut'];
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM produit p $where_clause");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$total_pages = max(1, (int)ceil($total / $per_page));

$stmt = $pdo->prepare("
    SELECT p.*, m.nom AS marque_nom, m.couleur_hex AS marque_couleur
    FROM produit p
    LEFT JOIN marque m ON p.marque_id = m.id
    $where_clause
    ORDER BY p.id DESC
    LIMIT " . (int)$per_page . " OFFSET " . (int)$offset . "
");
$stmt->execute($params);
$produits = $stmt->fetchAll();

$marques = $pdo->query("SELECT id, nom FROM marque ORDER BY nom")->fetchAll();

// ---- Affichage ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Produits</h1>
        <a href="/admin/produits/create.php" class="admin-btn admin-btn--primary">Nouveau produit</a>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div class="admin-alert admin-alert--success">Produit enregistré.</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="admin-alert admin-alert--success">Produit supprimé.</div>
    <?php endif; ?>

    <form class="admin-filters" method="GET">
        <div class="admin-field">
            <label for="marque_id">Marque</label>
            <select name="marque_id" id="marque_id">
                <option value="">Toutes les marques</option>
                <?php foreach ($marques as $m): ?>
                    <option value="<?= (int)$m['id'] ?>" <?= (!empty($_GET['marque_id']) && (int)$_GET['marque_id'] === (int)$m['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="statut">Statut</label>
            <select name="statut" id="statut">
                <option value="">Tous</option>
                <option value="1" <?= (isset($_GET['statut']) && $_GET['statut'] === '1') ? 'selected' : '' ?>>En ligne</option>
                <option value="0" <?= (isset($_GET['statut']) && $_GET['statut'] === '0') ? 'selected' : '' ?>>Hors ligne</option>
            </select>
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Filtrer</button>
        <a href="/admin/produits/index.php" class="admin-btn admin-btn--secondary">Réinitialiser</a>
    </form>

    <div class="admin-card">
        <?php if (empty($produits)): ?>
            <p class="admin-empty">Aucun produit trouvé.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nom</th>
                        <th>Marque</th>
                        <th>En ligne</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $p): ?>
                    <tr>
                        <td>
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= htmlspecialchars($p['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb">
                            <?php else: ?>
                                <span class="admin-thumb" style="display:inline-block;background:#eee;border-radius:4px;"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                            <small style="color:#888;"><?= htmlspecialchars($p['titre'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td>
                            <?php if (!empty($p['marque_nom'])): ?>
                                <span class="admin-badge" style="background:<?= htmlspecialchars($p['marque_couleur'] ?? '#EE7325', ENT_QUOTES, 'UTF-8') ?>;color:white;">
                                    <?= htmlspecialchars($p['marque_nom'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php else: ?>
                                <span class="admin-badge admin-badge--info">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form class="admin-toggle-form" action="/admin/produits/toggle.php" method="POST">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                                <div class="container">
                                    <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="produits-toggle-<?= $p['id'] ?>" <?= $p['en_ligne'] ? 'checked' : '' ?>>
                                    <label class="switch" for="produits-toggle-<?= $p['id'] ?>">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </form>
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a href="/admin/produits/edit.php?id=<?= (int)$p['id'] ?>" class="admin-btn admin-btn--small admin-btn--secondary">Modifier</a>
                                <a href="/admin/produits/delete.php?id=<?= (int)$p['id'] ?>" class="admin-btn admin-btn--small admin-btn--danger" data-confirm="Supprimer ce produit ?">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <div class="admin-pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= (int)($page - 1) ?>&<?= htmlspecialchars(http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)), ENT_QUOTES, 'UTF-8') ?>">Précédent</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="admin-pagination--active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&<?= htmlspecialchars(http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)), ENT_QUOTES, 'UTF-8') ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= (int)($page + 1) ?>&<?= htmlspecialchars(http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)), ENT_QUOTES, 'UTF-8') ?>">Suivant</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
