<?php
/**
 * Admin - Gestion des produits
 * Formulaire de création d'un produit
 */
declare(strict_types=1);

// ---- Initialisation ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$marques = $pdo->query("SELECT id, nom FROM marque ORDER BY nom")->fetchAll();

// ---- Traitement du formulaire ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } else {
        $image_url = admin_upload_image($_FILES['image'] ?? []);
        if ($image_url === null && !empty($_FILES['image']['name'])) {
            $error = 'Erreur lors de l\'upload de l\'image.';
        }
    }

    if (empty($error)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO produit (sous_titre, titre, nom, marque_id, image, en_ligne)
                VALUES (:sous_titre, :titre, :nom, :marque_id, :image, :en_ligne)
            ");
            $stmt->execute([
                ':sous_titre' => $_POST['sous_titre'] ?? '',
                ':titre' => $_POST['titre'] ?? '',
                ':nom' => $_POST['nom'] ?? '',
                ':marque_id' => !empty($_POST['marque_id']) ? (int)$_POST['marque_id'] : null,
                ':image' => $image_url ?? '',
                ':en_ligne' => (int)(!empty($_POST['en_ligne'])),
            ]);
            $produit_id = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare("
                INSERT INTO produit_Apropos (produit_id, sous_titre, titre, contenu, cta_label, cta_lien, image, alt)
                VALUES (:produit_id, '', '', '', '', '', '', '')
            ");
            $stmt->execute([':produit_id' => $produit_id]);

            $pdo->commit();
            admin_redirect('admin/produits/edit.php?id=' . $produit_id . '&saved=1');
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Erreur création produit : ' . $e->getMessage());
            $error = 'Erreur lors de la création.';
        }
    }
}

// ---- Affichage du formulaire ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Nouveau produit</h1>
        <a href="/admin/produits/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
    </div>

    <?php if (!empty($error)): ?>
    <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

        <div class="admin-section">
            <button type="button" class="admin-section-header" aria-expanded="true">
                Fiche produit
                <span class="admin-section-toggle">▼</span>
            </button>
            <div class="admin-section-body admin-section-body--open">
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" name="nom" id="nom"
                        value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="sous_titre">Sous-titre</label>
                    <input type="text" name="sous_titre" id="sous_titre"
                        value="<?= htmlspecialchars($_POST['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre"
                        value="<?= htmlspecialchars($_POST['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="marque_id">Marque</label>
                    <select name="marque_id" id="marque_id">
                        <option value="">— Sans marque —</option>
                        <?php foreach ($marques as $m): ?>
                        <option value="<?= (int)$m['id'] ?>"
                            <?= (!empty($_POST['marque_id']) && (int)$_POST['marque_id'] === (int)$m['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" accept="image/*" data-preview>
                </div>
                <div class="admin-field">
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="produits-add-toggle"
                            <?= !empty($_POST['en_ligne']) ? 'checked' : '' ?>>
                        <label class="switch" for="produits-add-toggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-section">
            <button type="button" class="admin-section-header" aria-expanded="false">
                À propos
                <span class="admin-section-toggle">▼</span>
            </button>
            <div class="admin-section-body">
                <p class="admin-empty">Sera configuré après la création du produit.</p>
            </div>
        </div>

        <button type="submit" class="admin-btn admin-btn--primary">Créer le produit</button>
    </form>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>