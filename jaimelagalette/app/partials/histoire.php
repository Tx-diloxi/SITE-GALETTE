<?php
// Initialise le flag de chargement unique du style du partial histoire
static $histoireStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$histoireStyleLoaded): $histoireStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial histoire -->
<link rel="stylesheet" href="/assets/css/partials/histoire/histoire.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial histoire est définie et non vide
if (!empty($titreHistoire)): ?>
<!-- Section racine du partial histoire -->
<section id="histoire">

    <!-- Conteneur des titres de la section histoire -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section histoire -->
        <h3><?= htmlspecialchars($titreHistoire['sous_titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section histoire -->
        <h2><?= htmlspecialchars($titreHistoire['titre'] ?? 'Notre histoire', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <?php
    // Vérifie si des étapes sont définies
    if (!empty($etapesHistoire)): ?>
    <!-- Liste chronologique des étapes de l'histoire -->
    <div class="histoire-liste">
        <?php
        // Boucle sur chaque étape de l'histoire
        foreach ($etapesHistoire as $index => $etape): ?>
        <!-- Étape individuelle de l'histoire -->
        <article class="histoire-etape" data-index="<?= $index ?>">

            <!-- Image illustrant l'étape -->
            <img src="<?= htmlspecialchars($etape['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($etape['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="etape-image"
                loading="lazy">
            <!-- Année de l'étape -->
            <span class="etape-annee"><?= htmlspecialchars($etape['annee'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <!-- Titre de l'étape -->
            <h3 class="etape-titre"><?= htmlspecialchars($etape['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
            <?php if (!empty($etape['region'])): ?>
            <!-- Région de l'étape -->
            <p class="etape-region"><?= htmlspecialchars($etape['region'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if (!empty($etape['description'])): ?>
            <!-- Description détaillée de l'étape -->
            <p class="etape-description"><?= nl2br(htmlspecialchars($etape['description'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>

        </article>
        <?php
        // Fin de boucle des étapes
        endforeach; ?>
    </div>

    <?php
    // Vérifie si le CTA doit être affiché
    if ($titreHistoire['cta_label'] && $titreHistoire['cta_lien']): ?>
    <!-- Conteneur du bouton CTA histoire -->
    <div class="histoire-engagement">
        <!-- Lien du bouton CTA -->
        <a href="<?= htmlspecialchars($titreHistoire['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($titreHistoire['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($etapesHistoire)): ?>
    <script>
    // Trace un serpentin SVG (lignes + demi-tours arrondis) qui traverse les étapes ; s'adapte à leur nombre
    (function () {
        var liste = document.querySelector('#histoire .histoire-liste');
        if (!liste) return;

        var NS = 'http://www.w3.org/2000/svg';
        var svg = document.createElementNS(NS, 'svg');
        svg.setAttribute('class', 'histoire-serpent');
        svg.setAttribute('aria-hidden', 'true');
        var path = document.createElementNS(NS, 'path');
        svg.appendChild(path);
        // En dernier enfant : ne décale pas l'alternance gauche/droite des cartes (:nth-child)
        liste.appendChild(svg);

        function draw() {
            var box = liste.getBoundingClientRect();
            var rows = [];
            var minX = Infinity, maxX = -Infinity;

            // Un repère par étape : le médaillon. On retient aussi l'étendue des cartes.
            liste.querySelectorAll('.histoire-etape').forEach(function (etape) {
                var img = etape.querySelector('.etape-image');
                if (!img) return;
                var m = img.getBoundingClientRect();
                if (!m.width) return;
                var c = etape.getBoundingClientRect();
                rows.push({ x: m.left + m.width / 2 - box.left, y: m.top + m.height / 2 - box.top });
                minX = Math.min(minX, c.left - box.left);
                maxX = Math.max(maxX, c.right - box.left);
            });

            // Moins de deux repères (ou mobile, médaillons masqués) : on garde le trait droit
            if (rows.length < 2) {
                liste.classList.remove('has-snake');
                path.removeAttribute('d');
                return;
            }

            // Bords où le serpent fait demi-tour, et rayon des virages (adapté à l'espace disponible)
            var xl = Math.max(minX - 40, 10);
            var xr = Math.min(maxX + 40, box.width - 10);
            var gap = Infinity;
            for (var i = 1; i < rows.length; i++) gap = Math.min(gap, rows[i].y - rows[i - 1].y);
            var R = Math.max(8, Math.min(60, gap / 4, (xr - xl) / 4));

            // Chaque étape : une ligne vers un bord, un demi-tour arrondi, puis retour en sens inverse
            var d = 'M' + rows[0].x + ' ' + rows[0].y;
            rows.forEach(function (row, i) {
                if (i === rows.length - 1) {
                    d += ' H' + row.x;
                    return;
                }
                var right = i % 2 === 0;
                var edge = right ? xr : xl;
                var dir = right ? 1 : -1;
                var sweep = right ? 1 : 0;
                var next = rows[i + 1].y;
                d += ' H' + (edge - dir * R)
                   + ' A' + R + ' ' + R + ' 0 0 ' + sweep + ' ' + edge + ' ' + (row.y + R)
                   + ' V' + (next - R)
                   + ' A' + R + ' ' + R + ' 0 0 ' + sweep + ' ' + (edge - dir * R) + ' ' + next;
            });
            path.setAttribute('d', d);
            liste.classList.add('has-snake');
        }

        draw();
        window.addEventListener('load', draw);
        window.addEventListener('resize', draw);
        if ('ResizeObserver' in window) new ResizeObserver(draw).observe(liste);
    })();
    </script>
    <?php endif; ?>

    <!-- Images décoratives de blé -->
    <img src="/assets/images/ble.png" alt="Illustration de blé" class="ble-deco-1" loading="lazy">
    <img src="/assets/images/ble.png" alt="Illustration de blé" class="ble-deco-2" loading="lazy">
    <!-- Image décorative de vague en bas de section -->
    <img src="/assets/images/vague4.svg" class="vague4" alt="Image d'une vague stylisée">

</section>
<?php endif; ?>