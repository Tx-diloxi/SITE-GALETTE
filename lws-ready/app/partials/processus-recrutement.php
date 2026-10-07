<?php
// --- Partial: Processus de recrutement ---
// Page : Recrutement
// Variables attendues :
// - $processus : tableau contenant sous_titre, titre
// - $etapesProcessus : tableau d'étapes (titre, description, ordre)

// Initialise le flag de chargement unique du style
static $processusStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$processusStyleLoaded): $processusStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial processus -->
<link rel="stylesheet" href="/assets/css/partials/processus/processus.css">
<?php endif; ?>

<?php
// Vérifie que les variables de condition du partial sont définies et non vides
if (!empty($processus) && !empty($etapesProcessus)):
?>
<!-- Section racine du partial Notre processus -->
<section id="processus-recrutement">

    <!-- Conteneur des titres de la section -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section -->
        <h3><?= htmlspecialchars($processus['sous_titre'] ?? 'Simple et transparent', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section -->
        <h2><?= htmlspecialchars($processus['titre'] ?? 'Notre processus', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <!-- Timeline horizontale des étapes -->
    <div class="processus-timeline">
        <?php foreach ($etapesProcessus as $index => $etape): 
            $numero = $index + 1;
        ?>
        <!-- Étape individuelle -->
        <div class="processus-etape">
            <!-- Numéro d'étape avec cercle -->
            <div class="processus-etape-numero">
                <span><?= $numero ?></span>
            </div>
            <!-- Trait de connexion (sauf pour la dernière étape) -->
            <?php if ($numero < count($etapesProcessus)): ?>
            <div class="processus-etape-ligne"></div>
            <?php endif; ?>
            <!-- Contenu de l'étape -->
            <div class="processus-etape-contenu">
                <!-- Titre de l'étape (Candidature, Premier échange, etc.) -->
                <h3 class="processus-etape-titre"><?= htmlspecialchars($etape['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <!-- Description de l'étape -->
                <p class="processus-etape-description">
                    <?= htmlspecialchars($etape['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague2.svg" class="vague-processus" alt="Image d'une vague stylisée">

</section>
<?php endif; ?>