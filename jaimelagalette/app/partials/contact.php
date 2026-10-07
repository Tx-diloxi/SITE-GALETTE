<?php
// Initialise le flag de chargement unique du style du partial contact
static $contactStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$contactStyleLoaded): $contactStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial contact -->
<link rel="stylesheet" href="/assets/css/partials/contact/contact.css">
<?php endif; ?>

<!-- Image décorative de vague en haut de section -->
<img src="/assets/images/vague4.svg" class="vague-contact" alt="Image d'une vague stylisée">

<?php
// Vérifie que la variable de condition du partial contact est définie et non vide
if (!empty($contact)): ?>
<!-- Section racine du partial contact -->
<section id="contact">
    <!-- Conteneur des titres de la section contact -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section contact -->
        <h3><?= htmlspecialchars($contact['sous_titre']) ?></h3>
        <!-- Affiche le titre principal de la section contact -->
        <h2><?= htmlspecialchars($contact['titre']) ?></h2>
    </div>
    <!-- Grille des cartes contact -->
    <div class="grid-2">
        <?php
        // Boucle sur chaque carte de contact
        foreach ($cardsContact as $card): ?>
        <!-- Carte individuelle de contact -->
        <div class="card">
            <?php if ($card['image']): ?>
            <!-- Image illustrant le contact -->
            <img src="<?= htmlspecialchars($card['image']) ?>" alt="<?= htmlspecialchars($card['alt'] ?? '') ?>"
                loading="lazy">
            <?php endif; ?>
            <!-- Titre de la carte contact -->
            <h3><?= htmlspecialchars($card['titre']) ?></h3>
            <?php if ($card['description']): ?>
            <!-- Description de la carte contact -->
            <p><?= htmlspecialchars($card['description']) ?></p>
            <?php endif; ?>
            <!-- Lien et bouton CTA de la carte contact -->
            <a href="<?= htmlspecialchars($card['cta_lien']) ?>">
                <button><?= htmlspecialchars($card['cta_label']) ?></button>
            </a>
        </div>
        <?php
        // Fin de boucle des cartes contact
        endforeach; ?>
    </div>
</section>
<?php endif; ?>
