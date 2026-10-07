<?php
// Initialise le flag de chargement unique du style du partial histoire
static $histoireStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$histoireStyleLoaded): $histoireStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial histoire -->
<link rel="stylesheet" href="/assets/css/partials/histoire/histoire.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial histoire est définie et non vide
if (!empty($titreHistoire)): ?>
<!-- Section racine du partial histoire -->
<section id="histoire">

    <!-- Conteneur des titres de la section histoire -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section histoire -->
        <h3><?= htmlspecialchars($titreHistoire['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section histoire -->
        <h2><?= htmlspecialchars($titreHistoire['titre'] ?? 'Notre histoire', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <?php
    // Vérifie si des étapes sont définies
    if (!empty($etapesHistoire)): ?>
    <!-- Liste chronologique des étapes de l'histoire -->
    <div class="histoire-liste">
        <?php
        // Boucle sur chaque étape de l'histoire
        foreach ($etapesHistoire as $index => $etape): ?>
        <!-- Étape individuelle de l'histoire -->
        <article class="histoire-etape" data-index="<?= $index ?>">

            <!-- Image illustrant l'étape -->
            <img src="<?= htmlspecialchars($etape['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($etape['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="etape-image"
                loading="lazy">
            <!-- Année de l'étape -->
            <span class="etape-annee"><?= htmlspecialchars($etape['annee'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <!-- Titre de l'étape -->
            <h3 class="etape-titre"><?= htmlspecialchars($etape['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
            <?php if (!empty($etape['region'])): ?>
            <!-- Région de l'étape -->
            <p class="etape-region"><?= htmlspecialchars($etape['region'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if (!empty($etape['description'])): ?>
            <!-- Description détaillée de l'étape -->
            <p class="etape-description"><?= nl2br(htmlspecialchars($etape['description'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>

        </article>
        <?php
        // Fin de boucle des étapes
        endforeach; ?>
    </div>

    <?php
    // Vérifie si le CTA doit être affiché
    if ($titreHistoire['cta_label'] && $titreHistoire['cta_lien']): ?>
    <!-- Conteneur du bouton CTA histoire -->
    <div class="histoire-engagement">
        <!-- Lien du bouton CTA -->
        <a href="<?= htmlspecialchars($titreHistoire['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($titreHistoire['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Images décoratives de blé -->
    <img src="/assets/images/ble.png" alt="Illustration de blé" class="ble-deco-1" loading="lazy">
    <img src="/assets/images/ble.png" alt="Illustration de blé" class="ble-deco-2" loading="lazy">
    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague4.svg" class="vague4" alt="Image d'une vague stylisée">

</section>
<?php endif; ?>