<?php
declare(strict_types=1);

// ---- SECTION PARTIAL ----

/**
 * Partial: faq
 *
 * Admin section editor for the FAQ section
 */

if (!isset($section) || !is_array($section)) {
    return;
}

// --- Traitement du formulaire de la section FAQ ---

// Vérifie si le formulaire a été soumis pour la section FAQ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'faq') {
    // Récupère et vérifie le token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_csrf_verify($token)) {
        // Token invalide : affiche une erreur
        echo '<div class="admin-alert admin-alert--error">Token CSRF invalide.</div>';
    } else {
        // Récupère et nettoie les champs du formulaire
        $sous_titre = trim($_POST['sous_titre'] ?? '');
        $titre      = trim($_POST['titre'] ?? '');
        $contenu    = trim($_POST['contenu'] ?? '');

        // Met à jour les données de la section FAQ en base
        $stmt = $pdo->prepare("UPDATE partial_FAQ SET sous_titre = :st, titre = :t, contenu = :c WHERE id = 1");
        $stmt->execute([
            ':st' => $sous_titre,
            ':t'  => $titre,
            ':c'  => $contenu,
        ]);

        // Redirige vers la page d'édition avec un indicateur de succès
        admin_redirect_page_saved();
    }
    // Fin de la validation du token CSRF
}
// Fin du traitement du formulaire
?>

<!-- Formulaire d'édition de la section FAQ -->
<form method="post">
    <!-- Champ caché : identifiant d'action -->
    <input type="hidden" name="action" value="faq">
    <!-- Champ caché : token CSRF anti-falsification -->
    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">

    <!-- Champ : Sous-titre de la section FAQ -->
    <div class="admin-field">
        <label>Sous-titre</label>
        <input type="text" name="sous_titre" value="<?= htmlspecialchars($section['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ : Titre de la section FAQ -->
    <div class="admin-field">
        <label>Titre</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($section['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <!-- Champ : Contenu / texte d'introduction de la FAQ -->
    <div class="admin-field">
        <label>Contenu</label>
        <textarea name="contenu"><?= htmlspecialchars($section['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <!-- Bouton de soumission -->
    <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
</form>
