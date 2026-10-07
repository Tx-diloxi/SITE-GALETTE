<?php
// Initialise le flag de chargement unique du style du partial actions-rse
static $actionsRseStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$actionsRseStyleLoaded): $actionsRseStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS specifique au partial actions-rse -->
<link rel="stylesheet" href="/assets/css/partials/actions-rse/actions-rse.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial actions-rse est définie et non vide
if (!empty($actions_rse)):
?>
<!-- Section principale des actions RSE -->
<section id="actions_rse">
    <!-- Conteneur de la liste des actions RSE -->
    <div class="actions-rse-liste">
        <?php
        // Boucle sur chaque action RSE
        foreach ($actions_rse as $action_rse): ?>
        <!-- Bloc individuel d'action RSE -->
        <article class="actions-rse-bloc">
            <?php if (!empty($action_rse['image'])): ?>
            <!-- Conteneur du visuel de l'action RSE -->
            <div class="actions-rse-visuel">
                <!-- Image illustrative de l'action RSE -->
                <img src="<?= htmlspecialchars($action_rse['image'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($action_rse['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            </div>
            <?php endif; ?>
            <!-- Conteneur du contenu textuel de l'action RSE -->
            <div class="actions-rse-contenu">
                <?php if (!empty($action_rse['titre'])): ?>
                <div class="titre">
                    <!-- Surtitre de l'action RSE -->
                    <h3><?= htmlspecialchars($action_rse['titre'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($action_rse['description'])): ?>
                    <!-- Titre principal de l'action RSE -->
                    <h2><?= htmlspecialchars($action_rse['description'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php endif; ?>
                </div>

                <?php if (!empty($action_rse['contenu'])): ?>
                <!-- Texte descriptif de l'action RSE -->
                <p class="actions-rse-texte"><?= nl2br(htmlspecialchars($action_rse['contenu'], ENT_QUOTES, 'UTF-8')) ?>
                </p>
                <?php endif; ?>
                <?php if (!empty($action_rse['tags'])): ?>
                <!-- Tags de l'action RSE -->
                <p class="actions-rse-tags"><?= htmlspecialchars($action_rse['tags'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <!-- Vague décorative orange entre deux blocs -->
            <img src="/assets/images/vague1.svg" class="actions-rse-vague actions-rse-vague--orange"
                alt="Vague decorative">
            <!-- Vague décorative noire entre deux blocs -->
            <img src="/assets/images/vague2.svg" class="actions-rse-vague actions-rse-vague--noir"
                alt="Vague decorative">
            <!-- Vague décorative anthracite en fin de section -->
            <img src="/assets/images/vague3.svg" class="actions-rse-vague actions-rse-vague--anthracite"
                alt="Vague decorative">
        </article>
        <?php
        // Fin de boucle des actions RSE
        endforeach; ?>
    </div>
</section>
<?php
// Fin de la condition d'affichage des actions RSE
endif; ?>
