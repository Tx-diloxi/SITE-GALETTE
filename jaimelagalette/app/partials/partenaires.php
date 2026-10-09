<?php
// Initialise le flag de chargement unique du style du partial partenaires
static $partenairesStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$partenairesStyleLoaded): $partenairesStyleLoaded = true; ?>
<!-- Inclut la feuille de style CSS spécifique au partial partenaires -->
<link rel="stylesheet" href="/assets/css/partials/partenaires/partenaires.css">
<?php endif; ?>

<?php
// Vérifie que les variables de contenu du partial sont définies et non vides
if (!empty($partenaire) && !empty($cardsPartenaire)): ?>
<!-- Section racine du partial partenaires -->
<section id="partenaires">
    <!-- Conteneur des titres de la section partenaires -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section partenaires -->
        <h3><?= htmlspecialchars($partenaire['sous_titre']) ?></h3>
        <!-- Affiche le titre principal de la section partenaires -->
        <h2><?= htmlspecialchars($partenaire['titre']) ?></h2>
    </div>

    <!-- Conteneur du carrousel partenaires -->
    <div class="carrousel-conteneur">
        <!-- Piste du carrousel partenaires -->
        <div class="carrousel-piste" data-boucle data-orange>
            <!-- Groupe de logos : répété par le script pour boucler sans raccord -->
            <div class="carrousel-groupe">
            <?php
            // Boucle sur chaque carte partenaire
            foreach ($cardsPartenaire as $partenaire): ?>
            <?php if (!empty($partenaire['lien'])): ?>
            <!-- Lien externe vers le site du partenaire -->
            <a href="<?= htmlspecialchars($partenaire['lien']) ?>" target="_blank" rel="noopener noreferrer">
                <?php endif; ?>
                <!-- Logo du partenaire -->
                <img src="<?= htmlspecialchars($partenaire['logo']) ?>" alt="<?= htmlspecialchars($partenaire['alt']) ?>" loading="eager"
                    class="logo-partenaires">
                <?php if (!empty($partenaire['lien'])): ?>
            </a>
            <?php endif; ?>
            <?php
            // Fin de boucle des cartes partenaire
            endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Script partagé des carrousels de logos (défilement infini sans raccord) -->
<script src="/assets/js/carrousel-logos.js" defer></script>

<?php endif; ?>