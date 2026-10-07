<?php
// Initialise le flag de chargement unique du style du partial legal
static $legalStyleLoaded = false;
// Vérifie si le style n'a pas encore ete charge pour eviter les doublons
if (!$legalStyleLoaded): $legalStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS specifique au partial legal -->
<link rel="stylesheet" href="/assets/css/page/legal/legal.css">
<?php endif; ?>

<?php
// Verifie si des sections legales existent et sont non vides
if (!empty($legalSections)): ?>
<!-- Section racine du partial legal -->
<section id="legal" class="section">
    <!-- Conteneur du contenu legal -->
    <div class="contenu">
        <?php
        // Boucle sur chaque section legale a afficher
        foreach ($legalSections as $section): ?>
        <!-- Titre de la section legale -->
        <h3><?= htmlspecialchars($section['titre']) ?></h3>
        <!-- Contenu HTML de la section legale (deja securise en base) -->
        <?= $section['contenu'] ?>
        <?php
        // Fin de boucle des sections legales
        endforeach; ?>
    </div>
</section>
<?php endif; ?>
