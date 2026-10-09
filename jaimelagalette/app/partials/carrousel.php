<?php
// Initialise le flag de chargement unique du style du partial carrousel
static $carrouselStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$carrouselStyleLoaded): $carrouselStyleLoaded = true; ?>
<!-- Inclut la feuille de style CSS spécifique au partial carrousel -->
<link rel="stylesheet" href="/assets/css/partials/carrousel/carrousel.css">
<?php endif; ?>

<?php 
// Vérifie que les variables de condition du partial carrousel sont définies et non vides
if (!empty($listeProduits) && is_array($listeProduits) && count($listeProduits) > 0): 
    // Calcule le nombre total de slides pour le carrousel
    $totalSlides = count($listeProduits);
    // Inclut le helper SEO pour la génération d'URL
    require_once HELPERS . 'seo.php';
    // CONFLIT DÉTECTÉ : règle 3 non appliquée ici pour préserver le fonctionnement
    // Cette boucle est un placeholder pour un enrichissement futur des produits
    $listeProduitsEnriched = [];
    foreach ($listeProduits as $p) {
        $listeProduitsEnriched[] = $p;
    }
    $listeProduits = $listeProduitsEnriched;
?>

<?php if (!empty($listeProduits[0]['image'])): ?>
<!-- Précharge l'image du premier produit : elle est insérée par le script, le navigateur ne la découvrirait que tard -->
<link rel="preload" as="image" href="<?= htmlspecialchars($listeProduits[0]['image'], ENT_QUOTES, 'UTF-8') ?>" fetchpriority="high">
<?php endif; ?>

<section id="carrousel">

    <div class="titre">
        <h3 id="titre-sous-titre"><?= htmlspecialchars($titre ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <h1 id="titre-principal"><?= htmlspecialchars($sousTitre ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    </div>

    <!-- Compteur -->
    <div class="carrousel-produits-compteur">
        <span class="carrousel-produits__compteur-actif" id="compteur-actif">1</span>
        <span>/</span>
        <span><?= $totalSlides ?></span>
    </div>

    <!-- Scène du carrousel -->
    <div class="carrousel-produits-scene">
        <div class="carrousel-produits__track" id="carrousel-track">
            <!-- Les slides seront injectées dynamiquement en JS -->
        </div>
    </div>

    <!-- Infos produit actif -->
    <div class="carrousel-produits__infos">
        <h3 class="carrousel-produits__marque" id="produit-marque">
            <?= htmlspecialchars($listeProduits[0]['marque'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <h2 class="carrousel-produits__nom-produit" id="produit-nom"><?= htmlspecialchars($listeProduits[0]['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <a href="#a_propos" class="carrousel-produits__cta" id="produit-cta">DÉTAILS</a>
    </div>

    <!-- Navigation -->
    <div class="carrousel-produits__nav">
        <button class="carrousel-produits__btn carrousel-produits__btn--prev" id="btn-prev"
            aria-label="Produit précédent">&#8592;</button>
        <button class="carrousel-produits__btn carrousel-produits__btn--next" id="btn-next"
            aria-label="Produit suivant">&#8594;</button>
    </div>

</section>

<!-- Image décorative de vague en bas de section -->
<img src="/assets/images/vague2.svg" class="vague-carrousel" alt="Image d'une vague stylisée">

<script>
(function() {
    // Données produits depuis PHP
    const produitsArray =
        <?= json_encode($listeProduits, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const produitsData =
        <?= json_encode($produitsData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const totalSlides = produitsArray.length;

    let currentIndex = 0;

    // Éléments DOM
    const track = document.getElementById('carrousel-track');
    const compteurActif = document.getElementById('compteur-actif');
    const produitMarque = document.getElementById('produit-marque');
    const produitNom = document.getElementById('produit-nom');
    const produitCta = document.getElementById('produit-cta');
    const titreSousTitre = document.getElementById('titre-sous-titre');
    const titrePrincipal = document.getElementById('titre-principal');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');

    // Fonction pour échapper le HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Fonction pour mettre à jour la section À propos
    function updateAproposSection(aproposData) {
        const sectionApropos = document.querySelector('#a_propos');
        if (!sectionApropos) return;

        // Si pas de données, ne pas vider la section
        if (!aproposData) return;

        // Vider la section mais garder l'ID
        sectionApropos.innerHTML = '';

        // Titres
        if (aproposData.sous_titre || aproposData.titre) {
            const titreDiv = document.createElement('div');
            titreDiv.className = 'titre';
            if (aproposData.sous_titre) {
                const h3 = document.createElement('h3');
                h3.textContent = aproposData.sous_titre;
                titreDiv.appendChild(h3);
            }
            if (aproposData.titre) {
                const h2 = document.createElement('h2');
                h2.textContent = aproposData.titre;
                titreDiv.appendChild(h2);
            }
            sectionApropos.appendChild(titreDiv);
        }

        // Contenu texte
        if (aproposData.contenu) {
            const div = document.createElement('div');
            const p = document.createElement('p');
            p.innerHTML = escapeHtml(aproposData.contenu).replace(/\n/g, '<br>');
            div.appendChild(p);
            sectionApropos.appendChild(div);
        }

        // Bouton CTA
        if (aproposData.cta_label && aproposData.cta_lien) {
            const div = document.createElement('div');
            const a = document.createElement('a');
            a.href = aproposData.cta_lien;
            const button = document.createElement('button');
            button.textContent = aproposData.cta_label;
            a.appendChild(button);
            div.appendChild(a);
            sectionApropos.appendChild(div);
        }

        // Image
        if (aproposData.image) {
            const img = document.createElement('img');
            img.src = aproposData.image;
            img.alt = aproposData.alt || '';
            sectionApropos.appendChild(img);
        }

        // Vague décorative
        const vagueImg = document.createElement('img');
        vagueImg.src = '/assets/images/vague1.svg';
        vagueImg.className = 'vague';
        vagueImg.alt = "Image d'une vague stylisée";
        sectionApropos.appendChild(vagueImg);
    }

    // Met à jour l'affichage des 3 slides et la section À propos
    // (initial : le serveur a déjà rendu la section À propos du premier produit, on ne la refait pas)
    function updateProductContent(initial) {
        const activeProduct = produitsArray[currentIndex];

        // Met à jour les infos texte du carrousel
        produitMarque.textContent = activeProduct.marque || '';
        produitNom.textContent = activeProduct.nom || '';
        titreSousTitre.textContent = activeProduct.sous_titre || '';
        titrePrincipal.textContent = activeProduct.titre || '';
        compteurActif.textContent = currentIndex + 1;
        produitCta.href = activeProduct.page_url || '#a_propos';

        // Met à jour la section À propos avec les données du produit actif
        const produitId = activeProduct.id;
        const dejaRendue = initial && document.querySelector('#a_propos') && document.querySelector('#a_propos').children.length > 0;
        if (!dejaRendue && produitsData && produitsData[produitId] && produitsData[produitId].apropos) {
            updateAproposSection(produitsData[produitId].apropos);
        }
    }

    // Met à jour l'affichage des 3 slides
    function renderSlides(initial) {
        if (totalSlides === 0) return;

        const prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        const nextIndex = (currentIndex + 1) % totalSlides;

        const prevProduct = produitsArray[prevIndex];
        const activeProduct = produitsArray[currentIndex];
        const nextProduct = produitsArray[nextIndex];

        track.innerHTML = `
            <article class="carrousel-produits__slide carrousel-produits__slide--prev" data-index="${prevIndex}">
                <div class="carrousel-produits__img-wrap">
                    <a href="${escapeHtml(prevProduct.page_url)}">
                    <img src="${escapeHtml(prevProduct.image)}" alt="${escapeHtml(prevProduct.nom)}" loading="lazy">
                    </a>
                </div>
            </article>
            <article class="carrousel-produits__slide actif" data-index="${currentIndex}">
                <div class="carrousel-produits__img-wrap">
                    <a href="${escapeHtml(activeProduct.page_url)}">
                    <img src="${escapeHtml(activeProduct.image)}" alt="${escapeHtml(activeProduct.nom)}" loading="eager" fetchpriority="high">
                    </a>
                </div>
            </article>
            <article class="carrousel-produits__slide carrousel-produits__slide--next" data-index="${nextIndex}">
                <div class="carrousel-produits__img-wrap">
                    <a href="${escapeHtml(nextProduct.page_url)}">
                    <img src="${escapeHtml(nextProduct.image)}" alt="${escapeHtml(nextProduct.nom)}" loading="lazy">
                    </a>
                </div>
            </article>
        `;

        // Met à jour le contenu texte et la section À propos
        updateProductContent(initial === true);

        // Ré-attache les événements sur les nouveaux slides
        attachSlideEvents();
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        renderSlides();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        renderSlides();
    }

    function attachSlideEvents() {
        const prevSlideEl = document.querySelector('.carrousel-produits__slide--prev');
        const nextSlideEl = document.querySelector('.carrousel-produits__slide--next');

        if (prevSlideEl) {
            prevSlideEl.removeEventListener('click', prevSlide);
            prevSlideEl.addEventListener('click', prevSlide);
        }
        if (nextSlideEl) {
            nextSlideEl.removeEventListener('click', nextSlide);
            nextSlideEl.addEventListener('click', nextSlide);
        }
    }

    // Événements des boutons
    if (btnPrev) btnPrev.addEventListener('click', prevSlide);
    if (btnNext) btnNext.addEventListener('click', nextSlide);

    // Navigation clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            nextSlide();
        }
    });

    // Initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            renderSlides(true);
        });
    } else {
        renderSlides(true);
    }
})();
</script>
<?php endif; ?>