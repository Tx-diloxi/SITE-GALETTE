<?php
// Initialise le flag de chargement unique du style du partial animation
static $animationStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$animationStyleLoaded): $animationStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial animation -->
<link rel="stylesheet" href="/assets/css/partials/animation/animation.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial animation est définie et non vide
if (!empty($animation)): ?>
<!-- Section racine du partial animation -->
<section id="animation">

    <!-- Conteneur des titres de la section animation -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section animation -->
        <h3><?= htmlspecialchars($animation['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section animation -->
        <h2><?= htmlspecialchars($animation['titre'] ?? 'Animations', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <?php if (!empty($animation['contenu'])): ?>
    <!-- Contenu texte de la section animation -->
    <div class="section-texte">
        <?= nl2br(htmlspecialchars($animation['contenu'], ENT_QUOTES, 'UTF-8')) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($cardsAnimation)): ?>
    <!-- Grille des cartes d'animation -->
    <div class="animation-cartes">
        <?php
        // Boucle sur chaque carte d'animation
        foreach ($cardsAnimation as $card): ?>
        <!-- Carte individuelle d'animation -->
        <article class="animation-carte">
            <?php if (!empty($card['image'])): ?>
            <!-- Image illustrant l'animation -->
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($card['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async">
            <?php endif; ?>
            <!-- Titre de l'animation -->
            <h2><?= htmlspecialchars($card['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        </article>
        <?php
        // Fin de boucle des cartes d'animation
        endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>
<!-- Image décorative de vague en bas de section -->
<img src="/assets/images/vague3.svg" class="vague-animation" alt="Image d'une vague stylisée">