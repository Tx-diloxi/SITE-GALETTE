<?php
/**
 * Admin FAQ questions management page
 *
 * Allows adding, editing, deleting, and filtering FAQ questions by category.
 */
declare(strict_types=1); // Active le mode strict de typage en PHP

// --- Chargement des dépendances et authentification ---
require_once __DIR__ . '/../../../app/config/database.php'; // Connexion BDD
require_once __DIR__ . '/../../../app/config/admin.php';   // Fonctions d'administration
admin_check_auth(); // Vérifie que l'utilisateur est connecté en tant qu'admin

// --- Récupération des paramètres d'URL ---
$message = ''; // Message de notification
$edit_id = (int)($_GET['edit'] ?? 0); // ID de la question à modifier (depuis l'URL)
$filter_cat = (int)($_GET['categorie'] ?? 0); // Filtre par catégorie (depuis l'URL)

// --- Chargement de la liste des catégories ---
$categories = $pdo->query("SELECT id, titre FROM categorie_FAQ ORDER BY ordre ASC, id ASC")->fetchAll();

// --- Construction de la requête SQL avec filtre optionnel ---
$where = '';
$params = [];
if ($filter_cat > 0) {
    // Filtre les questions par catégorie
    $where = 'WHERE q.categorie_faq_id = ?';
    $params[] = $filter_cat;
}

// Requête pour récupérer toutes les questions (avec jointure sur la catégorie)
$questions = $pdo->prepare("
    SELECT q.id, q.question, q.en_ligne, q.ordre, q.categorie_faq_id, c.titre AS categorie_titre
    FROM question_FAQ q
    LEFT JOIN categorie_FAQ c ON c.id = q.categorie_faq_id
    $where
    ORDER BY q.ordre ASC, q.id ASC
");
$questions->execute($params);
$questions = $questions->fetchAll(); // Tableau de toutes les questions

// --- Traitement des actions du formulaire (ajout, modification, suppression) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    // Vérifie la validité du token CSRF
    if (!admin_csrf_verify($token)) {
        $message = admin_alert('Token CSRF invalide.', 'error');
    } elseif (isset($_POST['add'])) {
        // --- Ajout d'une nouvelle question ---
        $categorie_faq_id = (int)($_POST['categorie_faq_id'] ?? 0);
        $question = trim($_POST['question'] ?? '');
        $reponse = trim($_POST['reponse'] ?? '');
        $mots_cles = trim($_POST['mots_cles'] ?? '');
        $profil_cible = $_POST['profil_cible'] ?? '';
        $ordre = (int)($_POST['ordre'] ?? 0);
        $en_ligne = isset($_POST['en_ligne']) ? 1 : 0;
        // Vérifie que les champs obligatoires sont remplis
        if ($categorie_faq_id > 0 && $question !== '' && $reponse !== '') {
            $ins = $pdo->prepare("INSERT INTO question_FAQ (categorie_faq_id, question, reponse, mots_cles, profil_cible, ordre, en_ligne) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->execute([$categorie_faq_id, $question, $reponse, $mots_cles, $profil_cible ?: null, $ordre, $en_ligne]);
            $message = admin_alert('Question ajoutée.');
        } else {
            $message = admin_alert('Veuillez remplir tous les champs obligatoires.', 'error');
        }
    } elseif (isset($_POST['edit'])) {
        // --- Modification d'une question existante ---
        $id = (int)($_POST['id'] ?? 0);
        $categorie_faq_id = (int)($_POST['categorie_faq_id'] ?? 0);
        $question = trim($_POST['question'] ?? '');
        $reponse = trim($_POST['reponse'] ?? '');
        $mots_cles = trim($_POST['mots_cles'] ?? '');
        $profil_cible = $_POST['profil_cible'] ?? '';
        $ordre = (int)($_POST['ordre'] ?? 0);
        $en_ligne = isset($_POST['en_ligne']) ? 1 : 0;
        // Vérifie que l'ID est valide et les champs obligatoires remplis
        if ($id > 0 && $categorie_faq_id > 0 && $question !== '' && $reponse !== '') {
            $upd = $pdo->prepare("UPDATE question_FAQ SET categorie_faq_id = ?, question = ?, reponse = ?, mots_cles = ?, profil_cible = ?, ordre = ?, en_ligne = ? WHERE id = ?");
            $upd->execute([$categorie_faq_id, $question, $reponse, $mots_cles, $profil_cible ?: null, $ordre, $en_ligne, $id]);
            $message = admin_alert('Question modifiée.');
            $edit_id = 0; // Réinitialise le mode édition
        } else {
            $message = admin_alert('Veuillez remplir tous les champs obligatoires.', 'error');
        }
    } elseif (isset($_POST['delete'])) {
        // --- Suppression d'une question ---
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $del = $pdo->prepare("DELETE FROM question_FAQ WHERE id = ?");
            $del->execute([$id]);
            $message = admin_alert('Question supprimée.');
        }
    }
    // Fin du traitement POST
}

// --- Chargement de la question à éditer (si demandé dans l'URL) ---
$edit_question = null;
if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM question_FAQ WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_question = $stmt->fetch(); // Données de la question à éditer
}

// --- Liste des profils cibles disponibles ---
$profil_cibles = [
    '' => 'Tous',
    'grand_public' => 'Grand public',
    'b2b' => 'B2B',
    'rh' => 'RH',
    'presse' => 'Presse',
];

// --- Inclusion du haut de page ---
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <!-- Barre d'outils -->
    <div class="admin-toolbar">
        <h1>Questions FAQ</h1>
        <a href="index.php" class="admin-btn admin-btn--secondary">Retour</a>
    </div>

    <!-- Affichage du message de notification -->
    <?= $message ?>

    <!-- --- Filtre par catégorie --- -->
    <div class="admin-filters">
        <div class="admin-field">
            <label>Filtrer par catégorie</label>
            <select onchange="window.location.href='?categorie='+this.value">
                <option value="0">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars((string)(int)$cat['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $filter_cat === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['titre'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- --- Formulaire de modification d'une question (mode édition) --- -->
    <?php if ($edit_question): ?>
    <div class="admin-card">
        <h2>Modifier la question</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string)(int)$edit_question['id'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="admin-field">
                <label>Catégorie</label>
                <select name="categorie_faq_id" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars((string)(int)$cat['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (int)$edit_question['categorie_faq_id'] === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['titre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin-field">
                <label>Question</label>
                <input type="text" name="question" value="<?= htmlspecialchars($edit_question['question'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="500">
            </div>
            <div class="admin-field">
                <label>Réponse</label>
                <textarea name="reponse" required><?= htmlspecialchars($edit_question['reponse'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="admin-field">
                <label>Mots-clés (séparés par des espaces)</label>
                <input type="text" name="mots_cles" value="<?= htmlspecialchars($edit_question['mots_cles'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="500">
            </div>
            <div class="admin-field">
                <label>Profil cible</label>
                <select name="profil_cible">
                    <?php foreach ($profil_cibles as $val => $label): ?>
                    <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= ($edit_question['profil_cible'] ?: '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap">
                <div class="admin-field">
                    <label>Ordre</label>
                    <input type="number" name="ordre" value="<?= htmlspecialchars((string)(int)$edit_question['ordre'], ENT_QUOTES, 'UTF-8') ?>" min="0">
                </div>
                <div class="admin-field">
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="faq-edit-toggle"<?= $edit_question['en_ligne'] ? ' checked' : '' ?>>
                        <label class="switch" for="faq-edit-toggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="admin-btn-group">
                <button type="submit" name="edit" value="1" class="admin-btn admin-btn--primary">Enregistrer</button>
                <a href="questions.php<?= $filter_cat > 0 ? '?categorie=' . htmlspecialchars((string)$filter_cat, ENT_QUOTES, 'UTF-8') : '' ?>" class="admin-btn admin-btn--secondary">Annuler</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
    <!-- Fin du formulaire d'édition -->

    <!-- --- Formulaire d'ajout et liste des questions --- -->
    <div class="admin-card">
        <h2><?= $edit_question ? 'Toutes les questions' : 'Ajouter une question' ?></h2>
        <!-- Formulaire d'ajout (caché en mode édition) -->
        <?php if (!$edit_question): ?>
        <form method="POST" style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #eee">
            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="admin-field">
                    <label>Catégorie</label>
                    <select name="categorie_faq_id" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars((string)(int)$cat['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cat['titre'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label>Profil cible</label>
                    <select name="profil_cible">
                        <?php foreach ($profil_cibles as $val => $label): ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="admin-field">
                <label>Question</label>
                <input type="text" name="question" required maxlength="500">
            </div>
            <div class="admin-field">
                <label>Réponse</label>
                <textarea name="reponse" required></textarea>
            </div>
            <div class="admin-field">
                <label>Mots-clés (séparés par des espaces)</label>
                <input type="text" name="mots_cles" maxlength="500">
            </div>
            <div style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap">
                <div class="admin-field">
                    <label>Ordre</label>
                    <input type="number" name="ordre" value="0" min="0">
                </div>
                <div class="admin-field">
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="faq-add-toggle" checked>
                        <label class="switch" for="faq-add-toggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            <button type="submit" name="add" value="1" class="admin-btn admin-btn--primary">Ajouter</button>
        </form>
        <?php endif; ?>
        <!-- Fin formulaire d'ajout -->

        <!-- --- Tableau listant toutes les questions --- -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Catégorie</th>
                    <th style="width:80px">Ordre</th>
                    <th style="width:100px">En ligne</th>
                    <th style="width:160px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($questions)): ?>
                <!-- Message si aucune question -->
                <tr><td colspan="5" class="admin-empty">Aucune question.</td></tr>
                <?php endif; ?>
                <!-- Boucle sur chaque question pour afficher une ligne -->
                <?php foreach ($questions as $q): ?>
                <tr>
                    <td><?= htmlspecialchars(mb_substr($q['question'], 0, 80), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($q['question']) > 80 ? '…' : '' ?></td>
                    <td><?= htmlspecialchars($q['categorie_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)(int)$q['ordre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <!-- Toggle activer/désactiver (envoi vers toggle.php) -->
                        <form method="POST" action="toggle.php" class="admin-toggle-form">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)(int)$q['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                            <div class="container">
                                <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="faq-toggle-<?= $q['id'] ?>"<?= $q['en_ligne'] ? ' checked' : '' ?>>
                                <label class="switch" for="faq-toggle-<?= $q['id'] ?>">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </form>
                    </td>
                    <td>
                        <!-- Boutons d'action : modifier et supprimer -->
                        <div class="admin-btn-group">
                            <a href="questions.php?edit=<?= htmlspecialchars((string)(int)$q['id'], ENT_QUOTES, 'UTF-8') ?>&categorie=<?= htmlspecialchars((string)$filter_cat, ENT_QUOTES, 'UTF-8') ?>" class="admin-btn admin-btn--small admin-btn--primary">Modifier</a>
                            <form method="POST" style="display:inline" data-confirm-form="Supprimer cette question ?">
                                <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars((string)(int)$q['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" name="delete" value="1" class="admin-btn admin-btn--small admin-btn--danger">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <!-- Fin de la boucle sur les questions -->
            </tbody>
        </table>
        <!-- Fin du tableau -->
    </div>
</main>
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
