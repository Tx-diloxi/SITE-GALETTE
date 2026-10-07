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
        <div class="carrousel-piste">
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
</section>

<!-- Script d'animation du carrousel labels (défilement infini) -->
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