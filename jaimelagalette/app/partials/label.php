<?php
// Initialise le flag de chargement unique du style du partial labels
static $labelsStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$labelsStyleLoaded): $labelsStyleLoaded = true; ?>
<!-- Inclut la feuille de style CSS spécifique au partial labels -->
<link rel="stylesheet" href="/assets/css/partials/partenaires/partenaires.css">
<?php endif; ?>

<?php
// Vérifie que les variables de contenu du partial sont définies et non vides
if (!empty($label) && !empty($cardsLabel)): ?>
<!-- Section racine du partial labels -->
<section id="partenaires">
    <!-- Conteneur des titres de la section labels -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section labels -->
        <h3><?= htmlspecialchars($label['sous_titre']) ?></h3>
        <!-- Affiche le titre principal de la section labels -->
        <h2><?= htmlspecialchars($label['titre']) ?></h2>
    </div>

    <!-- Conteneur du carrousel labels -->
    <div class="carrousel-conteneur">
        <!-- Piste du carrousel labels -->
        <div class="carrousel-piste" data-boucle data-orange>
            <!-- Groupe de logos : répété par le script pour boucler sans raccord -->
            <div class="carrousel-groupe">
            <?php
            // Boucle sur chaque carte label
            foreach ($cardsLabel as $cardLabel): ?>
            <?php if (!empty($cardLabel['lien'])): ?>
            <!-- Lien externe vers le site du label -->
            <a href="<?= htmlspecialchars($cardLabel['lien']) ?>" target="_blank" rel="noopener noreferrer">
                <?php endif; ?>
                <!-- Logo du label -->
                <img src="<?= htmlspecialchars($cardLabel['logo']) ?>" alt="<?= htmlspecialchars($cardLabel['alt']) ?>"
                    loading="lazy" class="logo-partenaires">
                <?php if (!empty($cardLabel['lien'])): ?>
            </a>
            <?php endif; ?>
            <?php
            // Fin de boucle des cartes label
            endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Script partagé des carrousels de logos (défilement infini sans raccord) -->
<script src="/assets/js/carrousel-logos.js" defer></script>

<?php endif; ?>