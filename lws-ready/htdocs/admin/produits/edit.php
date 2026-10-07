<?php
/**
 * Admin - Gestion des produits
 * Formulaire d'édition d'un produit
 */
declare(strict_types=1);

// ---- Initialisation ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    admin_redirect('admin/produits/index.php');
}

$stmt = $pdo->prepare("SELECT p.*, m.nom AS marque_nom, m.couleur_hex AS marque_couleur FROM produit p LEFT JOIN marque m ON p.marque_id = m.id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$produit = $stmt->fetch();

if (!$produit) {
    admin_redirect('admin/produits/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM produit_Apropos WHERE produit_id = :id");
$stmt->execute([':id' => $id]);
$apropos = $stmt->fetch() ?: [];

$marques = $pdo->query("SELECT id, nom FROM marque ORDER BY nom")->fetchAll();

$error = null;

// ---- Traitement du formulaire ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    } else {
        $pdo->beginTransaction();
        try {
            $image_url = admin_upload_image($_FILES['image'] ?? []);
            $apropos_image_url = admin_upload_image($_FILES['apropos_image'] ?? []);
            $stmt = $pdo->prepare("
                UPDATE produit SET
                    sous_titre = :sous_titre, titre = :titre, nom = :nom,
                    marque_id = :marque_id,
                    image = COALESCE(NULLIF(:image, ''), image),
                    en_ligne = :en_ligne
                WHERE id = :id
            ");
            $stmt->execute([
                ':sous_titre' => $_POST['sous_titre'] ?? '',
                ':titre' => $_POST['titre'] ?? '',
                ':nom' => $_POST['nom'] ?? '',
                ':marque_id' => !empty($_POST['marque_id']) ? (int)$_POST['marque_id'] : null,
                ':image' => $image_url ?? '',
                ':en_ligne' => (int)(!empty($_POST['en_ligne'])),
                ':id' => $id,
            ]);

            if ($apropos) {
                $stmt = $pdo->prepare("
                    UPDATE produit_Apropos SET
                        sous_titre = :sous_titre, titre = :titre, contenu = :contenu,
                        cta_label = :cta_label, cta_lien = :cta_lien,
                        image = COALESCE(NULLIF(:image, ''), image), alt = :alt
                    WHERE produit_id = :produit_id
                ");
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO produit_Apropos (produit_id, sous_titre, titre, contenu, cta_label, cta_lien, image, alt)
                    VALUES (:produit_id, :sous_titre, :titre, :contenu, :cta_label, :cta_lien, :image, :alt)
                ");
            }
            $stmt->execute([
                ':produit_id' => $id,
                ':sous_titre' => $_POST['apropos_sous_titre'] ?? '',
                ':titre' => $_POST['apropos_titre'] ?? '',
                ':contenu' => $_POST['apropos_contenu'] ?? '',
                ':cta_label' => $_POST['apropos_cta_label'] ?? '',
                ':cta_lien' => $_POST['apropos_cta_lien'] ?? '',
                ':image' => $apropos_image_url ?? '',
                ':alt' => $_POST['apropos_alt'] ?? '',
            ]);

            $pdo->commit();
            admin_redirect('admin/produits/edit.php?id=' . $id . '&saved=1');
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Erreur sauvegarde produit : ' . $e->getMessage());
            $error = 'Erreur lors de la sauvegarde.';
        }
    }
}

// ---- Affichage du formulaire ----
require_once __DIR__ . '/../layout/header.php';
?>
<main class="admin-main">
    <div class="admin-toolbar">
        <h1>Modifier : <?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="admin-btn-group">
            <a href="/admin/produits/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            <a href="/admin/produits/delete.php?id=<?= (int)$id ?>" class="admin-btn admin-btn--danger">Supprimer</a>
        </div>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div class="admin-alert admin-alert--success">Produit enregistré.</div>
    <?php endif; ?>
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
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="admin-field">
                    <label for="sous_titre">Sous-titre</label>
                    <input type="text" name="sous_titre" id="sous_titre" value="<?= htmlspecialchars($produit['sous_titre'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($produit['titre'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="marque_id">Marque</label>
                    <select name="marque_id" id="marque_id">
                        <option value="">— Sans marque —</option>
                        <?php foreach ($marques as $m): ?>
                            <option value="<?= (int)$m['id'] ?>" <?= ($produit['marque_id'] == $m['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label>Image actuelle</label>
                    <?php if (!empty($produit['image'])): ?>
                        <div class="admin-image-field">
                            <div class="admin-image-current">
                                <img src="<?= htmlspecialchars($produit['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                            </div>
                            <div class="admin-field">
                                <label for="image">Remplacer l'image</label>
                                <input type="file" name="image" id="image" accept="image/*" data-preview>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="file" name="image" id="image" accept="image/*" data-preview>
                    <?php endif; ?>
                </div>
                <div class="admin-field">
                    <div class="container">
                        <input type="checkbox" class="checkbox" name="en_ligne" value="1" id="produits-edit-toggle" <?= $produit['en_ligne'] ? 'checked' : '' ?>>
                        <label class="switch" for="produits-edit-toggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-section">
            <button type="button" class="admin-section-header" aria-expanded="<?= !empty($apropos) ? 'true' : 'false' ?>">
                À propos
                <span class="admin-section-toggle">▼</span>
            </button>
            <div class="admin-section-body <?= !empty($apropos) ? 'admin-section-body--open' : '' ?>">
                <div class="admin-field">
                    <label for="apropos_sous_titre">Sous-titre</label>
                    <input type="text" name="apropos_sous_titre" id="apropos_sous_titre" value="<?= htmlspecialchars($apropos['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="apropos_titre">Titre</label>
                    <input type="text" name="apropos_titre" id="apropos_titre" value="<?= htmlspecialchars($apropos['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="apropos_contenu">Contenu</label>
                    <textarea name="apropos_contenu" id="apropos_contenu"><?= htmlspecialchars($apropos['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="admin-field">
                    <label for="apropos_cta_label">CTA label</label>
                    <input type="text" name="apropos_cta_label" id="apropos_cta_label" value="<?= htmlspecialchars($apropos['cta_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label for="apropos_cta_lien">CTA lien</label>
                    <input type="text" name="apropos_cta_lien" id="apropos_cta_lien" value="<?= htmlspecialchars($apropos['cta_lien'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="admin-field">
                    <label>Image actuelle</label>
                    <?php if (!empty($apropos['image'])): ?>
                        <div class="admin-image-field">
                            <div class="admin-image-current">
                                <img src="<?= htmlspecialchars($apropos['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                            </div>
                            <div class="admin-field">
                                <label for="apropos_image">Remplacer l'image</label>
                                <input type="file" name="apropos_image" id="apropos_image" accept="image/*" data-preview>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="file" name="apropos_image" id="apropos_image" accept="image/*" data-preview>
                    <?php endif; ?>
                </div>
                <div class="admin-field">
                    <label for="apropos_alt">Alt</label>
                    <input type="text" name="apropos_alt" id="apropos_alt" value="<?= htmlspecialchars($apropos['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
        </div>

        <div class="admin-btn-group" style="margin-top:1.5rem;">
            <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
            <a href="/admin/produits/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
        </div>
    </form>
</main>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
