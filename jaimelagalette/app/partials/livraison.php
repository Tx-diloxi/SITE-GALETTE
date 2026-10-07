<?php
// Initialise le flag de chargement unique du style du partial livraison
static $livraisonStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$livraisonStyleLoaded): $livraisonStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial livraison -->
<link rel="stylesheet" href="/assets/css/partials/livraison/livraison.css">
<?php
// Vérifie si une image de fond personnalisée est définie pour la section livraison
if (!empty($livraison['image_fond'])): ?>
<!-- Style inline pour définir l'image de fond de la section livraison -->
<style>
#livraison {
    background-image: url('<?= htmlspecialchars($livraison['image_fond'], ENT_QUOTES, 'UTF-8') ?>');
}
</style>
<?php
endif; ?>
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial livraison est définie et non vide
if (!empty($livraison)): ?>
<!-- Section racine du partial livraison avec image de fond et contenu -->
<section id="livraison">
    <!-- Conteneur des titres de la section livraison -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section livraison -->
        <h3><?= htmlspecialchars($livraison['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section livraison -->
        <h2><?= htmlspecialchars($livraison['titre'] ?? 'Notre distribution', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <?php
    // Vérifie si un contenu texte est défini pour la livraison
    if (!empty($livraison['contenu'])): ?>
    <!-- Conteneur du texte de description de la livraison -->
    <div>
        <!-- Texte descriptif avec sauts de ligne conservés -->
        <p><?= nl2br(htmlspecialchars($livraison['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php
    endif; ?>

    <?php
    // Vérifie si le CTA est défini avec son libellé et son lien
    if (!empty($livraison['cta_label']) && !empty($livraison['cta_lien'])): ?>
    <!-- Conteneur du bouton d'appel à l'action -->
    <div>
        <!-- Lien du bouton CTA livraison -->
        <a href="<?= htmlspecialchars($livraison['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <!-- Bouton du CTA livraison -->
            <button><?= htmlspecialchars($livraison['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php
    endif; ?>

</section>
<?php
endif; ?>