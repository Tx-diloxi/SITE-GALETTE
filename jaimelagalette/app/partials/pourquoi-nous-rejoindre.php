<?php
// --- Partial: Pourquoi nous rejoindre ---
// Page : Recrutement
// Variables attendues :
// - $recrutement : tableau contenant sous_titre, titre
// - $cardsRecrutement : tableau de cartes (image, alt, titre, description)

// Initialise le flag de chargement unique du style
static $recrutementStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$recrutementStyleLoaded): $recrutementStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial recrutement -->
<link rel="stylesheet" href="/assets/css/partials/pourquoi-nous-rejoindre/pourquoi-nous-rejoindre.css">
<?php endif; ?>

<?php
// Vérifie que les variables de condition du partial sont définies et non vides
if (!empty($recrutement) && !empty($cardsRecrutement)):
?>
<!-- Section racine du partial Pourquoi nous rejoindre -->
<section id="recrutement-pourquoi">

    <!-- Conteneur des titres de la section -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section -->
        <h3><?= htmlspecialchars($recrutement['sous_titre'] ?? 'Pourquoi nous rejoindre ?', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section -->
        <h2><?= htmlspecialchars($recrutement['titre'] ?? 'Pourquoi nous rejoindre ?', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <!-- Grille des cartes "Pourquoi nous rejoindre" -->
    <div class="grid-3-recrutement">
        <?php foreach ($cardsRecrutement as $card): ?>
        <!-- Carte individuelle -->
        <div class="card recrutement-card">
            <?php if (!empty($card['image'])): ?>
            <!-- Image illustrant la valeur / l'avantage -->
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($card['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <?php endif; ?>
            <!-- Titre de la carte (Esprit d'équipe / Savoir-faire / Qualité de vie) -->
            <span
                class="recrutement-card-titre"><?= htmlspecialchars($card['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <?php if (!empty($card['description'])): ?>
            <!-- Description détaillant l'avantage -->
            <p class="recrutement-card-description"><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Image décorative de vague en bas de section (optionnel, comme dans les autres partials) -->
    <img src="/assets/images/vague3.svg" class="vague-recrutement" alt="Image d'une vague stylisée">

</section>
<?php endif; ?>