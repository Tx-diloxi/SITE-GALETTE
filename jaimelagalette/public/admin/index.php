<?php
/**
 * Tableau de bord de l'administration
 */
declare(strict_types=1);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/config/admin.php';

// -----------------------------------------------
// AUTHENTIFICATION
// -----------------------------------------------
admin_check_auth();

// Rediriger les commerciaux vers leur espace
if (auth_is_commercial()) {
    header('Location: /admin/commercial/visite-mystere/index.php');
    exit;
}

// -----------------------------------------------
// RÉCUPÉRATION DES STATISTIQUES DU TABLEAU DE BORD
// -----------------------------------------------
try {
    // Compte les candidatures non lues
    $nbCandidatures = $pdo->query("SELECT COUNT(*) FROM applications WHERE lue = FALSE")->fetchColumn();
} catch (PDOException $e) {
    // Si la colonne 'lue' n'existe pas, compte toutes les candidatures
    $nbCandidatures = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
}
// Compte les messages de contact non traités (statut 'nouveau')
$nbContacts = $pdo->query("SELECT COUNT(*) FROM formulaire_contact WHERE statut = 'nouveau'")->fetchColumn();
// Compte les produits publiés en ligne
$nbProduitsEnLigne = $pdo->query("SELECT COUNT(*) FROM produit WHERE en_ligne = TRUE")->fetchColumn();
// Compte les produits masqués (hors ligne)
$nbProduitsHorsLigne = $pdo->query("SELECT COUNT(*) FROM produit WHERE en_ligne = FALSE")->fetchColumn();
// Compte les offres d'emploi actives
$nbOffresActives = $pdo->query("SELECT COUNT(*) FROM offre_Emploi WHERE en_ligne = TRUE")->fetchColumn();
// Compte les inspections du jour
$nb_inspections_aujourdhui = $pdo->query("SELECT COUNT(*) FROM inspection WHERE DATE(cree_le) = CURDATE()")->fetchColumn();
$nb_inspections_semaine = $pdo->query("SELECT COUNT(*) FROM inspection WHERE YEARWEEK(cree_le, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();

// Récupère les 3 dernières candidatures (les plus récentes)
$dernieresCandidatures = $pdo->query("SELECT id, prenom, nom, poste_souhaite, cree_le FROM applications ORDER BY cree_le DESC LIMIT 3")->fetchAll();
// Récupère les 3 derniers messages de contact
$derniersMessages = $pdo->query("SELECT id, nom, email, sujet, cree_le FROM formulaire_contact ORDER BY cree_le DESC LIMIT 3")->fetchAll();

// -----------------------------------------------
// AFFICHAGE DU TABLEAU DE BORD
// -----------------------------------------------
require_once __DIR__ . '/layout/header.php';
?>
<!-- --- Contenu principal du tableau de bord --- -->
<main class="admin-main">
    <div class="admin-dashboard">
        <h1>Tableau de bord</h1>

        <!-- Widgets statistiques -->
        <div class="admin-widgets">
            <!-- Widget : nombre de candidatures non lues -->
            <div class="admin-widget <?= $nbCandidatures > 0 ? 'admin-widget--warning' : '' ?>">
                <span class="admin-widget-number"><?= $nbCandidatures ?></span>
                <span class="admin-widget-label">Candidatures non lues</span>
                <?php if ($nbCandidatures > 0): ?>
                    <!-- Badge d'alerte si des candidatures sont en attente -->
                    <span class="admin-badge admin-badge--danger"><?= $nbCandidatures ?></span>
                <?php endif; ?>
            </div>
            <!-- Widget : nombre de messages non lus -->
            <div class="admin-widget <?= $nbContacts > 0 ? 'admin-widget--warning' : '' ?>">
                <span class="admin-widget-number"><?= $nbContacts ?></span>
                <span class="admin-widget-label">Messages non lus</span>
                <?php if ($nbContacts > 0): ?>
                    <span class="admin-badge admin-badge--danger"><?= $nbContacts ?></span>
                <?php endif; ?>
            </div>
            <!-- Widget : nombre de produits en ligne -->
            <div class="admin-widget">
                <span class="admin-widget-number"><?= $nbProduitsEnLigne ?></span>
                <span class="admin-widget-label">Produits en ligne</span>
            </div>
            <!-- Widget : nombre de produits hors ligne -->
            <div class="admin-widget">
                <span class="admin-widget-number"><?= $nbProduitsHorsLigne ?></span>
                <span class="admin-widget-label">Produits hors ligne</span>
            </div>
            <!-- Widget : nombre d'offres d'emploi actives -->
            <div class="admin-widget">
                <span class="admin-widget-number"><?= $nbOffresActives ?></span>
                <span class="admin-widget-label">Offres d'emploi actives</span>
            </div>
            <!-- Widget : nombre d'inspections aujourd'hui -->
            <div class="admin-widget">
                <span class="admin-widget-number"><?= $nb_inspections_aujourdhui ?></span>
                <span class="admin-widget-label">Inspections aujourd'hui</span>
            </div>
            <!-- Widget : nombre d'inspections cette semaine -->
            <div class="admin-widget">
                <span class="admin-widget-number"><?= $nb_inspections_semaine ?></span>
                <span class="admin-widget-label">Inspections cette semaine</span>
            </div>
        </div>

        <!-- Grille des listes récentes -->
        <div class="admin-dashboard-grid">
            <!-- Carte : dernières candidatures reçues -->
            <div class="admin-card">
                <h2>Dernières candidatures</h2>
                <!-- Vérifie s'il y a des candidatures récentes -->
                <?php if (empty($dernieresCandidatures)): ?>
                    <p class="admin-empty">Aucune candidature récente.</p>
                <?php else: ?>
                    <ul class="admin-list">
                        <!-- Boucle sur les 3 dernières candidatures -->
                        <?php foreach ($dernieresCandidatures as $c): ?>
                        <li>
                            <a href="/admin/candidatures/view.php?id=<?= (int)$c['id'] ?>">
                                <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                                – <?= htmlspecialchars($c['poste_souhaite'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="admin-date"><?= htmlspecialchars($c['cree_le'], ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <!-- Fin de boucle candidatures -->
                    </ul>
                <?php endif; ?>
            </div>
            <!-- Carte : derniers messages de contact -->
            <div class="admin-card">
                <h2>Derniers messages contact</h2>
                <!-- Vérifie s'il y a des messages récents -->
                <?php if (empty($derniersMessages)): ?>
                    <p class="admin-empty">Aucun message récent.</p>
                <?php else: ?>
                    <ul class="admin-list">
                        <!-- Boucle sur les 3 derniers messages -->
                        <?php foreach ($derniersMessages as $m): ?>
                        <li>
                            <a href="/admin/contacts/view.php?id=<?= (int)$m['id'] ?>">
                                <strong><?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                                – <?= htmlspecialchars($m['sujet'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="admin-date"><?= htmlspecialchars($m['cree_le'], ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <!-- Fin de boucle messages -->
                    </ul>
                <?php endif; ?>
            </div>
            <!-- Carte : dernières inspections mystères -->
            <div class="admin-card">
                <h2>Dernières visites mystères</h2>
                <?php
                $dernieres_inspections = $pdo->query("
                    SELECT i.id, i.cree_le, l.prenom AS lp, l.nom AS ln,
                           c.prenom AS cp, c.nom AS cn
                    FROM inspection i
                    JOIN livreur l ON l.id = i.livreur_id
                    JOIN commercial c ON c.id = i.commercial_id
                    ORDER BY i.cree_le DESC LIMIT 3
                ")->fetchAll();
                ?>
                <?php if (empty($dernieres_inspections)): ?>
                    <p class="admin-empty">Aucune inspection récente.</p>
                <?php else: ?>
                    <ul class="admin-list">
                        <?php foreach ($dernieres_inspections as $ins): ?>
                        <li>
                            <a href="/admin/visites-mysteres/view.php?id=<?= (int)$ins['id'] ?>">
                                <strong><?= htmlspecialchars($ins['lp'] . ' ' . $ins['ln'], ENT_QUOTES, 'UTF-8') ?></strong>
                                – par <?= htmlspecialchars($ins['cp'] . ' ' . $ins['cn'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="admin-date"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ins['cree_le'])), ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/layout/footer.php'; ?>
