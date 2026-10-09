<?php
// Initialise le flag de chargement unique du style du partial footer
static $footerStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$footerStyleLoaded): $footerStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial footer -->
<link rel="stylesheet" href="/assets/css/partials/footer/footer.css">
<?php endif; ?>

<!-- Pied de page du site avec marque, liens et mentions légales -->
<footer class="site-footer">
    <!-- Conteneur principal du footer -->
    <div class="footer-container">

        <!-- Partie haute du footer incluant la marque et les liens -->
        <div class="footer-top">

            <!-- Bloc contenant le logo et la description de la marque -->
            <div class="footer-brand">
                <?php
                // Vérifie si un logo personnalisé est défini
                if (!empty($footer['logo'])): ?>
                <!-- Groupe du logo et de la description de la marque -->
                <div class="brand-logo-group">
                    <!-- Image du logo dans le footer -->
                    <img src="<?= htmlspecialchars($footer['logo']) ?>" alt="Logo" class="footer-logo" loading="lazy" decoding="async">
                </div>
                <!-- Description textuelle de la marque -->
                <h2><?= htmlspecialchars($footer['description']) ?></h2>
                <?php
                endif; ?>
            </div>

            <!-- Liens organisés par catégorie dans le footer -->
            <?php
            // Vérifie si des liens de footer existent
            if (!empty($footerLiens)):
                // Regroupe les liens par catégorie pour l'affichage en colonnes
                $liensParCategorie = [];
                foreach ($footerLiens as $lien) {
                    $categorie = $lien['categorie'];
                    $liensParCategorie[$categorie][] = $lien;
                }
            ?>

            <?php
            // Boucle sur chaque catégorie de liens pour générer les colonnes
            foreach ($liensParCategorie as $categorie => $liens): ?>
            <!-- Colonne de liens correspondant à une catégorie -->
            <div class="footer-section">
                <!-- Titre de la catégorie de liens -->
                <h3><?= htmlspecialchars($categorie) ?></h3>
                <!-- Conteneur de la liste des liens -->
                <div class="footer-links">
                    <?php
                    // Boucle sur chaque lien de la catégorie courante
                    foreach ($liens as $lien):
                        // Vérifie que le lien est bien défini
                        if (!empty($lien['lien'])): ?>
                    <!-- Lien individuel du footer -->
                    <a href="<?= htmlspecialchars($lien['lien']) ?>">
                        <?= htmlspecialchars($lien['titre'] ?? $lien['label'] ?? '') ?>
                    </a>
                    <?php
                        endif;
                    // Fin de boucle des liens
                    endforeach; ?>
                </div>
            </div>
            <?php
            // Fin de boucle des catégories
            endforeach; ?>
            <?php
            endif; ?>

        </div>

        <!-- Séparateur visuel entre la partie haute et basse du footer -->
        <hr class="footer-divider">

        <!-- Partie basse du footer contenant le copyright et les mentions légales -->
        <div class="footer-bottom">
            <?php
            // Vérifie si un texte de copyright est défini
            if (!empty($footer['copyright'])): ?>
            <!-- Texte de copyright affiché dans le footer -->
            <p class="footer-copyright"><?= nl2br(htmlspecialchars($footer['copyright'])) ?></p>
            <?php
            endif; ?>

            <?php
            // Vérifie si des liens légaux existent
            if (!empty($footerLegal)): ?>
            <!-- Conteneur des liens de mentions légales -->
            <div class="footer-legal">
                <?php
                // Boucle sur chaque élément légal
                foreach ($footerLegal as $legal):
                    // Vérifie si l'élément est un lien ou un texte simple
                    if (!empty($legal['lien'])): ?>
                <!-- Lien hypertexte vers une page légale -->
                <a href="<?= htmlspecialchars($legal['lien']) ?>">
                    <?= htmlspecialchars($legal['titre'] ?? $legal['label'] ?? '') ?>
                </a>
                <?php
                    elseif (!empty($legal['contenu'])): ?>
                <!-- Texte légal sans lien hypertexte -->
                <span><?= htmlspecialchars($legal['contenu']) ?></span>
                <?php
                    endif;
                // Fin de boucle des liens légaux
                endforeach; ?>
            </div>
            <?php
            endif; ?>
        </div>

    </div>
</footer>
