<?php
// Initialise le flag de chargement unique du style du partial intro
static $introStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$introStyleLoaded): $introStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial intro -->
<link rel="stylesheet" href="/assets/css/partials/intro/intro.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial intro est définie et vraie
if (!empty($intro)): ?>
<!-- Section racine du partial intro -->
<section id="intro">
    <div class="fil_ariane">
        <a href="/">Accueil</a> > <span><?= htmlspecialchars($intro['nom_page']) ?></span>
    </div>
    <!-- Titre principal du intro -->
    <div class="titre">
        <h3><?= htmlspecialchars($intro['sous_titre']) ?></h3>
        <h2><?= htmlspecialchars($intro['titre']) ?></h2>
    </div>

    <?php if (!empty($intro['citation'])): ?>
    <!-- Citation du intro -->
    <div>
        <blockquote class="citation">"<?= htmlspecialchars($intro['citation']) ?>"</blockquote>
    </div>
    <?php endif; ?>

    <?php if (!empty($intro['contenu'])): ?>
    <!-- Contenu du intro -->
    <div class="contenu">
        <?= nl2br(htmlspecialchars($intro['contenu'])) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($intro['image_mascotte'])): ?>
    <!-- Image décorative du intro -->
    <img src="<?= htmlspecialchars($intro['image_mascotte']) ?>"
        alt="<?= htmlspecialchars($intro['alt_mascotte'] ?? '') ?>" class="intro_mascotte" loading="eager">
    <?php endif; ?>

    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/intro/vague_intro.svg" class="vague_intro" alt="Image d'une vague stylisée">
</section>
<?php endif; ?>