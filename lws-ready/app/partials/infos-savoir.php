<?php
// Initialise le flag de chargement unique du style du partial savoir-faire
static $savoirFaireStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$savoirFaireStyleLoaded): $savoirFaireStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial savoir-faire -->
<link rel="stylesheet" href="/assets/css/partials/savoir-faire/savoir-faire.css">
<?php endif; ?>

<!-- Section racine du partial savoir-faire -->
<section id="savoir-faire">
    <?php if (!empty($titreSavoirFaire)): ?>
    <!-- Conteneur des titres de la section savoir-faire -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section savoir-faire -->
        <h3><?= htmlspecialchars($titreSavoirFaire['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section savoir-faire -->
        <h2><?= htmlspecialchars($titreSavoirFaire['titre'] ?? 'Notre savoir-faire', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>
    <?php endif; ?>

    <?php if (!empty($etapesSavoirFaire)): ?>
    <!-- Timeline des étapes du savoir-faire -->
    <div class="savoir-faire-timeline">
        <?php
        // Boucle sur chaque étape du savoir-faire
        foreach ($etapesSavoirFaire as $index => $etape) : ?>
        <!-- Étape individuelle du savoir-faire -->
        <article class="savoir-faire-etape" data-index="<?= $index ?>">

            <!-- Cercle avec icône -->
            <div class="etape-cercle">
                <?php if (!empty($etape['image_fond'])): ?>
                <!-- Icône de l'étape -->
                <img src="<?= htmlspecialchars($etape['image_fond'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($etape['alt_fond'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="etape-icone" loading="lazy">
                <?php endif; ?>
            </div>

            <!-- Carte de contenu de l'étape -->
            <div class="etape-carte">
                <div class="etape-contenu-wrapper">
                    <div class="etape-texte">
                        <!-- Sous-titre de l'étape -->
                        <h3 class="etape-sous-titre">
                            <?= htmlspecialchars($etape['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                        <!-- Titre principal de l'étape -->
                        <h2 class="etape-titre"><?= htmlspecialchars($etape['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                        <?php if (!empty($etape['contenu'])): ?>
                        <!-- Description de l'étape -->
                        <p class="etape-description">
                            <?= nl2br(htmlspecialchars($etape['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($etape['image_droite'])): ?>
                    <!-- Logos à droite (HACCP, AB, packaging...) -->
                    <div class="etape-logos">
                        <?php if (is_array($etape['image_droite'])): ?>
                        <?php
                        // Boucle sur chaque logo
                        foreach ($etape['image_droite'] as $img): ?>
                        <img src="<?= htmlspecialchars($img['src'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($img['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        <?php
                        // Fin de boucle des logos
                        endforeach; ?>
                        <?php else: ?>
                        <img src="<?= htmlspecialchars($etape['image_droite'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($etape['alt_droite'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Courbe décorative entre les étapes (sauf pour la dernière) -->
            <?php if ($index < count($etapesSavoirFaire) - 1): ?>
            <svg class="courbe-segment" viewBox="0 0 100 300" preserveAspectRatio="none" aria-hidden="true">
                <path d="M 50 0 Q 70 100, 50 200 Q 30 250, 50 300" fill="none" stroke="#2A2A2A" stroke-width="1"
                    stroke-dasharray="3,3" />
            </svg>
            <?php endif; ?>

        </article>
        <?php
        // Fin de boucle des étapes du savoir-faire
        endforeach; ?>
    </div>

    <?php
    // Vérifie si le CTA doit être affiché
    if (!empty($titreSavoirFaire['cta_label']) && !empty($titreSavoirFaire['cta_lien'])): ?>
    <!-- Conteneur du bouton CTA savoir-faire -->
    <div class="savoir-faire-engagement">
        <!-- Lien du bouton CTA -->
        <a href="<?= htmlspecialchars($titreSavoirFaire['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($titreSavoirFaire['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php endif; ?>

    <?php endif; ?>

    <!-- Images décoratives de blé -->
    <img src="/assets/images/ble.png" alt="Illustration de blé" class="ble-deco-1" loading="lazy">
    <img src="/assets/images/ble.png" alt="Illustration de blé" class="ble-deco-2" loading="lazy">
</section>