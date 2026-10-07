<?php
// Initialise le flag de chargement unique du style du partial hero
static $heroStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$heroStyleLoaded): $heroStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial hero -->
<link rel="stylesheet" href="/assets/css/partials/hero/hero.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial hero est définie et vraie
if (!empty($hero)): ?>
<!-- Section racine du partial hero -->
<section id="hero">
    <!-- Titre principal du hero -->
    <h1>
        <?php
        // Coupe le titre en mots pour les afficher séparément
        $mots = explode(' ', htmlspecialchars($hero['titre']));
        foreach ($mots as $mot):
        ?>
        <span><?= $mot ?></span>
        <?php endforeach; ?>
    </h1>

    <!-- Accroche du hero -->
    <p><?= htmlspecialchars($hero['accroche']) ?></p>

    <?php if (!empty($hero['image'])): ?>
    <!-- Image décorative du hero -->
    <img src="<?= htmlspecialchars($hero['image']) ?>" alt="<?= htmlspecialchars($hero['alt'] ?? '') ?>"
        class="hero_crepe" loading="eager">
    <?php endif; ?>
</section>
<?php endif; ?>

<script src="/assets/js/hero.js"></script>