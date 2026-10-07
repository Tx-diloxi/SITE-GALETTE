<?php
// Initialise le flag de chargement unique du style du partial engagements
static $engagementsStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$engagementsStyleLoaded): $engagementsStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial engagements -->
<link rel="stylesheet" href="/assets/css/partials/engagements/engagements.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial engagements est définie et vraie
if ($engagement): ?>

<!-- Image décorative de vague en haut de section -->
<img src="/assets/images/vague2.svg" class="vague-engagements" alt="Image d'une vague stylisée">

<!-- Section principale des engagements -->
<section id="engagements">
    <!-- Conteneur des titres de la section engagements -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section engagements -->
        <h3><?= htmlspecialchars($engagement['sous_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section engagements -->
        <h2><?= htmlspecialchars($engagement['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>
    <!-- Grille des cartes d'engagement -->
    <div class="grille-engagements grid-4">
        <?php
        // Boucle sur chaque carte d'engagement
        foreach ($cardsEngagement as $card): ?>
        <!-- Carte individuelle d'engagement -->
        <div class="carte-engagement card">
            <?php
            if ($card['image']): ?>
            <!-- Image décorative de la carte d'engagement -->
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($card['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <?php endif; ?>
            <!-- Titre de l'engagement -->
            <span><?= htmlspecialchars($card['titre'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php
            if ($card['description']): ?>
            <!-- Description de l'engagement -->
            <p><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
        <?php
        // Fin de boucle des cartes d'engagement
        endforeach; ?>
    </div>
    <?php
    // Vérifie si le CTA doit être affiché
    if ($engagement['cta_label'] && $engagement['cta_lien']): ?>
    <!-- Conteneur du bouton CTA engagements -->
    <div class="cta-engagement">
        <!-- Lien du bouton CTA -->
        <a href="<?= htmlspecialchars($engagement['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($engagement['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>