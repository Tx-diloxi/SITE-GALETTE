<?php
// Initialise le flag de chargement unique du style du partial circuit-court
static $circuitCourtStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$circuitCourtStyleLoaded): $circuitCourtStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial circuit-court -->
<link rel="stylesheet" href="/assets/css/partials/circuit-court/circuit-court.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial circuit-court est définie et vraie
if ($circuitCourt): ?>
<!-- Section principale du partial circuit-court -->
<section id="circuit-court">

    <!-- Conteneur des titres de la section circuit-court -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section circuit-court -->
        <h3><?= htmlspecialchars($circuitCourt['sous_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section circuit-court -->
        <h2><?= htmlspecialchars($circuitCourt['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <?php if ($circuitCourt['contenu']): ?>
    <!-- Contenu texte de présentation -->
    <div>
        <p><?= nl2br(htmlspecialchars($circuitCourt['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($circuitCourt['cta_label'] && $circuitCourt['cta_lien']): ?>
    <!-- Conteneur du bouton d'appel à l'action -->
    <div>
        <!-- Lien du bouton CTA circuit-court -->
        <a href="<?= htmlspecialchars($circuitCourt['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($circuitCourt['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($circuitCourt['image']): ?>
    <!-- Image principale de la section circuit-court -->
    <img src="<?= htmlspecialchars($circuitCourt['image'], ENT_QUOTES, 'UTF-8') ?>"
        alt="<?= htmlspecialchars($circuitCourt['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async">
    <?php endif; ?>
    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague2.svg" class="vague" alt="Image d'une vague stylisée">
</section>
<?php endif; ?>