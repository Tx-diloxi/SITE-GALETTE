<?php
// Initialise le flag de chargement unique du style du partial produits
static $produitsStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$produitsStyleLoaded): $produitsStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial produits -->
<link rel="stylesheet" href="/assets/css/partials/cta-produits/cta-produits.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial produits est définie et non vide
if (!empty($produits)): ?>
<!-- Section racine du partial produits -->
<section id="produits">
    <!-- Images décoratives de vague -->
    <img src="/assets/images/vague1.svg" class="vague2" alt="Image d'une vague stylisée">
    <img src="/assets/images/vague2.svg" class="vague" alt="Image d'une vague stylisée">

    <!-- Conteneur des titres de la section produits -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section produits -->
        <h3><?= htmlspecialchars($produits['sous_titre']) ?></h3>
        <!-- Affiche le titre principal de la section produits -->
        <h2><?= htmlspecialchars($produits['titre']) ?></h2>
    </div>
    <!-- Grille des cartes produits -->
    <div class="grid-2">
        <?php
        // Boucle sur chaque carte produit
        foreach ($cardsProduits as $i => $card): ?>
        <!-- Carte individuelle de produit avec image de fond -->
        <div class="card produit-card produit-card--<?= $i + 1 ?>"
            <?= $card['image'] ? 'style="background-image: url(\'' . htmlspecialchars($card['image']) . '\')"' : '' ?>>
            <!-- Tags du produit -->
            <p><?= htmlspecialchars($card['tags'] ?? '') ?></p>
            <!-- Marque / nom du produit -->
            <p><?= htmlspecialchars($card['marque_nom'] ?? $card['marque'] ?? '') ?></p>
            <?php if ($card['description']): ?>
            <!-- Description du produit -->
            <p><?= htmlspecialchars($card['description']) ?></p>
            <?php endif; ?>
            <?php if ($card['cta_label'] && $card['cta_lien']): ?>
            <!-- Lien et bouton CTA du produit -->
            <a href="<?= htmlspecialchars($card['cta_lien']) ?>">
                <button><?= htmlspecialchars($card['cta_label']) ?></button>
            </a>
            <?php endif; ?>
        </div>
        <?php
        // Fin de boucle des cartes produits
        endforeach; ?>
    </div>
</section>
<?php endif; ?>