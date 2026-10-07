<?php
// Initialise le flag de chargement unique du style du partial valeurs
static $valeursStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$valeursStyleLoaded): $valeursStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial valeurs -->
<link rel="stylesheet" href="/assets/css/partials/valeurs/valeurs.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial valeurs est définie et non vide
if (!empty($valeurs)): ?>
<!-- Section racine du partial valeurs -->
<section id="valeurs">
    <!-- Conteneur des titres de la section valeurs -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section valeurs -->
        <h3><?= htmlspecialchars($valeurs['sous_titre']) ?></h3>
        <!-- Affiche le titre principal de la section valeurs -->
        <h2><?= htmlspecialchars($valeurs['titre']) ?></h2>
    </div>
    <!-- Grille des cartes valeurs -->
    <div class="grid-4">
        <?php
        // Boucle sur chaque carte de valeur
        foreach ($cardsValeurs as $card): ?>
        <!-- Carte individuelle de valeur -->
        <div class="card">
            <?php if ($card['image']): ?>
            <!-- Image illustrant la valeur -->
            <img src="<?= htmlspecialchars($card['image']) ?>" alt="<?= htmlspecialchars($card['alt'] ?? '') ?>"
                loading="lazy">
            <?php endif; ?>
            <!-- Titre de la valeur -->
            <span><?= htmlspecialchars($card['titre']) ?></span>
            <?php if ($card['description']): ?>
            <!-- Description de la valeur -->
            <p><?= htmlspecialchars($card['description']) ?></p>
            <?php endif; ?>
        </div>
        <?php
        // Fin de boucle des cartes de valeurs
        endforeach; ?>
    </div>
    <!-- Mascotte décorative en forme de coeur -->
    <img src="/assets/images/mascotte_coeur.png" class="mascotte" alt="Image d'une mascotte en forme de coeur"
        loading="lazy">
</section>
<?php endif; ?>