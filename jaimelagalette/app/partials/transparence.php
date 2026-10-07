<?php
// Initialise le flag de chargement unique du style du partial transparence
static $transparenceStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$transparenceStyleLoaded): $transparenceStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial transparence -->
<link rel="stylesheet" href="/assets/css/partials/transparence/transparence.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial transparence est définie et non vide
if (!empty($transparence)):
?>

<!-- Image décorative de vague en haut de section -->
<img src="/assets/images/vague4.svg" class="vague-transparence-haut" alt="Image d'une vague stylisée">

<!-- Section racine du partial transparence -->
<section id="transparence">
    <!-- Conteneur des titres de la section transparence -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section transparence -->
        <h3><?= htmlspecialchars($transparence['sous_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section transparence -->
        <h2><?= htmlspecialchars($transparence['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <!-- Grille de cartes transparence -->
    <div class="grid-4-transparence">
        <?php
        // Boucle sur chaque carte de transparence
        foreach ($cardsTransparence as $card): ?>
        <!-- Carte individuelle de transparence -->
        <div class="card">
            <!-- Image illustrant la carte -->
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($card['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <!-- Titre de la carte -->
            <span><?= htmlspecialchars($card['titre'], ENT_QUOTES, 'UTF-8') ?></span>
            <!-- Description de la carte -->
            <p><?= htmlspecialchars($card['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php
        // Fin de boucle des cartes de transparence
        endforeach; ?>
        <!-- Mascotte décorative -->
        <img src="/assets/images/mascotte_transparence.png" class="mascotte"
            alt="Image d'une mascotte avec les differentes sans de transparence" loading="lazy">
    </div>

</section>

<?php
endif;
?>

<!-- Image décorative de vague en bas de section -->
<img src="/assets/images/vague3.svg" class="vague-transparence-bas" alt="Image d'une vague stylisée">
