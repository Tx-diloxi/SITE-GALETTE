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
        <div class="carrousel-piste">
            <?php
            // Boucle sur chaque carte partenaire
            foreach ($cardsPartenaire as $partenaire): ?>
            <?php if (!empty($partenaire['lien'])): ?>
            <!-- Lien externe vers le site du partenaire -->
            <a href="<?= htmlspecialchars($partenaire['lien']) ?>" target="_blank" rel="noopener noreferrer">
                <?php endif; ?>
                <!-- Logo du partenaire -->
                <img src="<?= htmlspecialchars($partenaire['logo']) ?>" alt="<?= htmlspecialchars($partenaire['alt']) ?>" loading="lazy"
                    class="logo-partenaires">
                <?php if (!empty($partenaire['lien'])): ?>
            </a>
            <?php endif; ?>
            <?php
            // Fin de boucle des cartes partenaire
            endforeach; ?>
        </div>
    </div>
</section>

<!-- Script d'animation du carrousel partenaires (défilement infini) -->
<script>
(function() {
    // Récupère la piste du carrousel
    const section = document.currentScript.previousElementSibling;
    const track = section.querySelector('.carrousel-piste');
    if (!track) return;

    // Duplique les éléments pour créer un effet de défilement infini
    Array.from(track.children).forEach(function(item) {
        const clone = item.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        if (clone.tagName === 'A') clone.setAttribute('tabindex', '-1');
        const img = clone.tagName === 'IMG' ? clone : clone.querySelector('img');
        if (img) img.setAttribute('alt', '');
        track.appendChild(clone);
    });

    let position = 0;
    let paused = false;
    const speed = 0.5;

    // Met en pause au survol
    track.addEventListener('mouseenter', function() {
        paused = true;
    });
    track.addEventListener('mouseleave', function() {
        paused = false;
    });

    // Animation du défilement
    function tick() {
        if (!paused) {
            position -= speed;
            const halfWidth = track.scrollWidth / 2;

            // Réinitialise la position pour boucler
            if (position <= -halfWidth) {
                position = 0;
            }
            track.style.transform = 'translateX(' + position + 'px)';
        }
        requestAnimationFrame(tick);
    }

    requestAnimationFrame(tick);
})();
</script>

<?php endif; ?>