<?php
// Initialise le flag de chargement unique du style du partial apropos
static $aproposStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$aproposStyleLoaded): $aproposStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial apropos -->
<link rel="stylesheet" href="/assets/css/partials/apropos/apropos.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial apropos est définie et vraie
if ($apropos): ?>
<!-- Section principale du partial à propos -->
<section id="a_propos">

    <!-- Conteneur des titres de la section à propos -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section à propos -->
        <h3><?= htmlspecialchars($apropos['sous_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section à propos -->
        <h2><?= htmlspecialchars($apropos['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <?php if ($apropos['contenu']): ?>
    <!-- Contenu texte de présentation -->
    <div>
        <p><?= nl2br(htmlspecialchars($apropos['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($apropos['cta_label'] && $apropos['cta_lien']): ?>
    <!-- Conteneur du bouton d'appel à l'action -->
    <div>
        <!-- Lien du bouton CTA à propos -->
        <a href="<?= htmlspecialchars($apropos['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($apropos['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($apropos['image']): ?>
    <!-- Image principale de la section à propos -->
    <img src="<?= htmlspecialchars($apropos['image'], ENT_QUOTES, 'UTF-8') ?>"
        alt="<?= htmlspecialchars($apropos['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async">
    <?php endif; ?>
    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague1.svg" class="vague" alt="Image d'une vague stylisée">
</section>
<?php endif; ?>