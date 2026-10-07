<?php
// Initialise le flag de chargement unique du style du partial essentiel
static $essentielStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$essentielStyleLoaded): $essentielStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial essentiel -->
<link rel="stylesheet" href="/assets/css/partials/essentiel/essentiel.css">
<?php endif; ?>

<?php
// Vérifie que les variables de condition du partial essentiel sont définies et non vides
if ($essentielTitre && $cardsIngredient):
?>
<!-- Section racine du partial essentiel -->
<section id="essentiel">

    <!-- Conteneur des titres de la section essentiel -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section essentiel -->
        <h3><?= htmlspecialchars($essentielTitre['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section essentiel -->
        <h2><?= htmlspecialchars($essentielTitre['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <!-- Grille des ingrédients essentiels -->
    <div class="grid-4-essentiel">
        <?php foreach ($cardsIngredient as $card): ?>
        <!-- Carte individuelle d'ingrédient -->
        <div class="card">
            <?php if (!empty($card['image'])): ?>
            <!-- Image illustrant l'ingrédient -->
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($card['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <?php endif; ?>
            <!-- Titre de l'ingrédient -->
            <span><?= htmlspecialchars($card['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <?php if (!empty($card['description'])): ?>
            <!-- Description de l'ingrédient -->
            <p><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <img src="<?= htmlspecialchars($essentielTitre['image_fond'], ENT_QUOTES, 'UTF-8') ?>" class="mascotte"
            alt="<?= htmlspecialchars($essentielTitre['alt_fond'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>


</section>
<?php endif; ?>