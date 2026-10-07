<?php
/**
 * Admin - Liste des sites
 *
 * Affiche la liste de tous les sites (points de vente) avec filtres
 * par type et statut en temps réel.
 */

declare(strict_types=1);

// ---- Inclusion des dépendances ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
require_once __DIR__ . '/../../../app/helpers/horaires.php';

// ---- Vérification d'authentification ----
admin_check_auth();

// ---- Récupération des données ----
// Prépare les conditions de filtrage pour la requête SQL
$where = [];
$params = [];

// Ajoute un filtre par type de site si spécifié dans l'URL
if (!empty($_GET['type_site'])) {
    $where[] = 'type_site = :type_site';
    $params[':type_site'] = $_GET['type_site'];
}

// Construit la clause WHERE si des filtres sont actifs
$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Récupère tous les sites depuis la base de données (avec filtre éventuel)
$stmt = $pdo->prepare("SELECT * FROM point_Carte $whereClause ORDER BY ville, nom");
$stmt->execute($params);
$sites = $stmt->fetchAll();

// ---- Calcul du statut en temps réel ----
// Boucle sur chaque site pour déterminer s'il est ouvert maintenant
foreach ($sites as &$s) {
    $s['est_ouvert_realtime'] = site_est_ouvert($pdo, (int)$s['id']);
}
unset($s);
// Fin de la boucle de calcul

// ---- Types pour le filtre ----
// Récupère la liste des types de sites distincts pour le filtre
$types = $pdo->query("SELECT DISTINCT type_site FROM point_Carte ORDER BY type_site")->fetchAll();

// ---- Affichage ----
// Inclusion de l'en-tête de la page d'administration
require_once __DIR__ . '/../layout/header.php';
?>
<!-- Contenu principal de la page -->
<main class="admin-main">
    <!-- Barre d'outils avec le titre et le bouton d'ajout -->
    <div class="admin-toolbar">
        <h1>Sites</h1>
        <a href="/admin/sites/create.php" class="admin-btn admin-btn--primary">Nouveau site</a>
    </div>

    <!-- Affiche les messages de confirmation (enregistrement ou suppression) -->
    <?php if (isset($_GET['saved'])): ?>
        <div class="admin-alert admin-alert--success">Site enregistré.</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="admin-alert admin-alert--success">Site supprimé.</div>
    <?php endif; ?>

    <!-- Formulaire de filtrage par type et statut -->
    <form class="admin-filters" method="GET">
        <div class="admin-field">
            <label for="type_site">Type</label>
            <select name="type_site" id="type_site">
                <option value="">Tous les types</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= htmlspecialchars($t['type_site'], ENT_QUOTES, 'UTF-8') ?>" <?= (!empty($_GET['type_site']) && $_GET['type_site'] === $t['type_site']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['type_site'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="est_ouvert">Statut</label>
            <select name="est_ouvert" id="est_ouvert">
                <option value="">Tous</option>
                <option value="1" <?= (isset($_GET['est_ouvert']) && $_GET['est_ouvert'] === '1') ? 'selected' : '' ?>>Ouvert</option>
                <option value="0" <?= (isset($_GET['est_ouvert']) && $_GET['est_ouvert'] === '0') ? 'selected' : '' ?>>Fermé</option>
            </select>
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Filtrer</button>
        <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Réinitialiser</a>
    </form>

    <!-- Carte contenant la liste des sites -->
    <div class="admin-card">
        <!-- Vérifie s'il y a des sites à afficher -->
        <?php if (empty($sites)): ?>
            <p class="admin-empty">Aucun site trouvé.</p>
        <?php else: ?>
            <!-- Tableau listant tous les sites -->
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Ville</th>
                        <th>Type</th>
                        <th>Ouvert</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Boucle sur chaque site pour afficher une ligne -->
                    <?php foreach ($sites as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['nom'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td><?= htmlspecialchars($s['ville'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($s['code_postal'], ENT_QUOTES, 'UTF-8') ?>)</td>
                        <td><span class="admin-badge admin-badge--info"><?= htmlspecialchars($s['type_site'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <!-- Affiche le statut ouvert/fermé en temps réel -->
                        <td>
                            <span class="admin-badge <?= $s['est_ouvert_realtime'] ? 'admin-badge--success' : 'admin-badge--danger' ?>">
                                <?= $s['est_ouvert_realtime'] ? 'Ouvert' : 'Fermé' ?>
                            </span>
                            <?php
                            $horaireText = site_format_horaires_text(site_get_horaires($pdo, (int)$s['id']));
                            if ($horaireText): ?>
                            <br><small style="color:var(--gris-text);font-size:0.75rem;"><?= htmlspecialchars($horaireText, ENT_QUOTES, 'UTF-8') ?></small>
                            <?php endif; ?>
                        </td>
                        <!-- Boutons d'action : modifier ou supprimer -->
                        <td>
                            <div class="admin-actions">
                                <a href="/admin/sites/edit.php?id=<?= htmlspecialchars((string)$s['id'], ENT_QUOTES, 'UTF-8') ?>" class="admin-btn admin-btn--small admin-btn--secondary">Modifier</a>
                                <a href="/admin/sites/delete.php?id=<?= htmlspecialchars((string)$s['id'], ENT_QUOTES, 'UTF-8') ?>" class="admin-btn admin-btn--small admin-btn--danger" data-confirm="Supprimer ce site ?">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- Fin de la boucle sur les sites -->
                </tbody>
            </table>
        <?php endif; ?>
        <!-- Fin de la vérification de sites -->
    </div>
</main>
<!-- ---- -->
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
