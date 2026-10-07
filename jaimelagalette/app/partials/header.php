<?php
// Initialise le flag de chargement unique du style du partial header
static $headerStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$headerStyleLoaded): $headerStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial header -->
<link rel="stylesheet" href="/assets/css/partials/header/header.css">
<?php endif; ?>

<!-- Barre de navigation principale contenant le logo et le bouton du menu burger -->
<header>
    <!-- Conteneur interne de l'en-tête -->
    <div>

        <!-- Lien du logo renvoyant à l'accueil -->
        <a href="/">
            <?php
            // Vérifie si le logo personnalisé existe dans le footer
            if (!empty($footer['logo'])): ?>
            <!-- Logo personnalisé du site -->
            <img src="<?= htmlspecialchars($footer['logo']) ?>" alt="Logo J'aime la Galette" loading="eager">
            <?php
            else: ?>
            <!-- Logo par défaut si aucun logo personnalisé n'est défini -->
            <img src="/assets/images/logo_jaimelagalette.png" alt="Logo J'aime la Galette" loading="eager">
            <?php
            endif; ?>
            <!-- Texte du logo affiché à côté de l'image -->
            <span>
                <!-- Première ligne du nom de la marque -->
                <span>J'aime</span>
                <!-- Deuxième ligne du nom de la marque -->
                <span>la galette !</span>
            </span>
        </a>

        <!-- Bouton du menu burger pour ouvrir la navigation mobile -->
        <button class="burger-btn" id="burgerBtn" aria-label="Ouvrir le menu" aria-expanded="false"
            aria-controls="navModal">
            <!-- Première barre du burger -->
            <span></span>
            <!-- Deuxième barre du burger -->
            <span></span>
            <!-- Troisième barre du burger -->
            <span></span>
        </button>

    </div>
</header>

<!-- ===== MODAL NAVIGATION ===== -->
<!-- Fenêtre modale de navigation principale avec sous-menus -->
<div class="nav-modal" id="navModal" role="dialog" aria-modal="true" aria-label="Menu de navigation">

    <!-- Conteneur interne de la modale de navigation -->
    <div class="nav-modal__inner">

        <!-- Panneau gauche affichant la liste des liens principaux -->
        <div class="nav-modal__left">
            <!-- Balise de navigation principale -->
            <nav>
                <!-- Liste non ordonnée des liens de navigation -->
                <ul>
                    <!-- Lien vers la page d'accueil -->
                    <li>
                        <a href="/" class="nav-modal__link">Accueil</a>
                    </li>
                    <!-- Lien vers la page Le Groupe avec sous-menu -->
                    <li data-submenu="groupe">
                        <a href="/le-groupe" class="nav-modal__link has-submenu">Le Groupe</a>
                    </li>
                    <!-- Lien vers la page Nos Produits avec sous-menu -->
                    <li data-submenu="produits">
                        <a href="/nos-produits" class="nav-modal__link has-submenu">Nos Produits</a>
                    </li>
                    <!-- Lien vers la page Savoir-faire -->
                    <li>
                        <a href="/savoir-faire" class="nav-modal__link">Notre Savoir-faire</a>
                    </li>
                    <!-- Lien vers la page RSE -->
                    <li>
                        <a href="/rse" class="nav-modal__link">RSE</a>
                    </li>
                    <!-- Lien vers la page FAQ -->
                    <li>
                        <a href="/faq" class="nav-modal__link">FAQ</a>
                    </li>
                    <!-- Lien vers la page Recrutement -->
                    <li>
                        <a href="/recrutement" class="nav-modal__link">Recrutement</a>
                    </li>
                    <!-- Lien vers la page Contact -->
                    <li>
                        <a href="/contact" class="nav-modal__link">Contact</a>
                    </li>
                    <!-- Lien vers les informations légales avec sous-menu -->
                    <li data-submenu="legal">
                        <a href="#" class="nav-modal__link has-submenu">Infos légales</a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Panneau droit affichant les sous-menus contextuels -->
        <div class="nav-modal__right" id="submenuPanel">
            <!-- Sous-menu déroulant pour la section Le Groupe -->
            <ul class="nav-modal__submenu" data-for="groupe">
                <!-- Lien vers l'ancre Qui sommes-nous -->
                <li><a href="/le-groupe#intro">Qui sommes-nous</a></li>
                <!-- Lien vers l'ancre Nos valeurs -->
                <li><a href="/le-groupe#valeurs">Nos valeurs</a></li>
                <!-- Lien vers l'ancre Notre histoire -->
                <li><a href="/le-groupe#histoire">Notre histoire</a></li>
                <!-- Lien vers l'ancre Nos engagements -->
                <li><a href="/le-groupe#engagements">Nos engagements</a></li>
            </ul>

            <!-- Sous-menu déroulant pour la section Nos Produits -->
            <ul class="nav-modal__submenu" data-for="produits">
                <!-- Lien vers la page J'aime la Galette -->
                <li><a href="/nos-produits/jaime-la-galette">J'aime la Galette</a></li>
                <!-- Lien vers la page Be Good'n -->
                <li><a href="/nos-produits/be-goodn">Be Good'n</a></li>
            </ul>

            <!-- Sous-menu déroulant pour les informations légales -->
            <ul class="nav-modal__submenu" data-for="legal">
                <!-- Lien vers la page Mentions légales -->
                <li><a href="/mentions-legales">Mentions légales</a></li>
                <!-- Lien vers la page Politique de confidentialité -->
                <li><a href="/politique-confidentialite">Politique de confidentialité</a></li>
                <!-- Lien vers la page Gestion des cookies -->
                <li><a href="/cookies">Gestion des cookies</a></li>
            </ul>
        </div>

        <!-- Bouton de fermeture de la modale de navigation -->
        <button class="nav-modal__close" id="navClose" aria-label="Fermer le menu">
            <!-- Icône de croix pour la fermeture -->
            <span>&times;</span>
        </button>

    </div>

</div>

<!-- Script de gestion de la navigation modale et des sous-menus -->
<script>
// Fonction principale auto-exécutée pour isoler les variables du scope global
(function() {
    // Récupère le bouton burger pour l'ouverture du menu
    const burgerBtn = document.getElementById('burgerBtn');
    // Récupère l'élément de la modale de navigation
    const navModal = document.getElementById('navModal');
    // Récupère le bouton de fermeture de la modale
    const navClose = document.getElementById('navClose');
    // Récupère tous les liens de navigation principaux
    const navLinks = document.querySelectorAll('.nav-modal__link');
    // Récupère le panneau des sous-menus
    const submenuPanel = document.getElementById('submenuPanel');
    // Récupère tous les sous-menus disponibles
    const allSubmenus = document.querySelectorAll('.nav-modal__submenu');
    // Récupère le conteneur interne de la modale
    const navInner = document.querySelector('.nav-modal__inner');

    // Stocke la référence du sous-menu actuellement actif
    let activeSubmenu = null;

    // Ouvre la modale de navigation
    function openModal() {
        // Ajoute la classe d'ouverture sur la modale
        navModal.classList.add('is-open');
        // Met à jour l'attribut d'accessibilité du bouton burger
        burgerBtn.setAttribute('aria-expanded', 'true');
        // Empêche le défilement de la page en arrière-plan
        document.body.classList.add('no-scroll');
        // Donne le focus au bouton de fermeture
        navClose.focus();
    }

    // Ferme la modale de navigation
    function closeModal() {
        // Retire la classe d'ouverture de la modale
        navModal.classList.remove('is-open');
        // Remet l'attribut d'accessibilité du bouton burger à faux
        burgerBtn.setAttribute('aria-expanded', 'false');
        // Réactive le défilement de la page
        document.body.classList.remove('no-scroll');
        // Réinitialise l'affichage des sous-menus
        resetSubmenus();
        // Remet le focus sur le bouton burger
        burgerBtn.focus();
    }

    // Réinitialise tous les sous-menus à leur état par défaut
    function resetSubmenus() {
        // Cache le panneau des sous-menus
        submenuPanel.classList.remove('is-visible');
        // Retire la classe active de tous les sous-menus
        allSubmenus.forEach(function(s) {
            s.classList.remove('is-active');
        });
        // Remet tous les éléments de navigation dans leur état normal
        navLinks.forEach(function(l) {
            l.closest('li').classList.remove('is-dimmed', 'is-active');
        });
        // Réinitialise la référence du sous-menu actif
        activeSubmenu = null;
    }

    // Affiche un sous-menu spécifique et atténue les autres liens
    function showSubmenu(key, activeLi) {
        // Définit le sous-menu actif
        activeSubmenu = key;
        // Rend le panneau des sous-menus visible
        submenuPanel.classList.add('is-visible');

        // Active uniquement le sous-menu correspondant à la clé
        allSubmenus.forEach(function(s) {
            s.classList.toggle('is-active', s.dataset.for === key);
        });

        // Applique les classes d'atténuation et d'activation aux liens
        navLinks.forEach(function(l) {
            const li = l.closest('li');
            // Réinitialise les états avant d'appliquer les nouveaux
            li.classList.remove('is-active', 'is-dimmed');
            // Le lien actif reste en évidence, les autres sont atténués
            li.classList.add(li === activeLi ? 'is-active' : 'is-dimmed');
        });
    }

    // Gestion des événements de survol et de clic sur les liens de navigation
    navLinks.forEach(function(link) {
        // Récupère l'élément parent li du lien
        const li = link.closest('li');
        // Récupère la clé du sous-menu associé
        const key = li.dataset.submenu;

        // Ouvre le sous-menu au survol de la souris
        li.addEventListener('mouseenter', function() {
            // Si aucun sous-menu associé, réinitialise l'affichage
            if (!key) {
                resetSubmenus();
                return;
            }
            // Affiche le sous-menu correspondant
            showSubmenu(key, li);
        });

        // Gère le clic sur un lien qui possède un sous-menu
        link.addEventListener('click', function(e) {
            // Si le sous-menu n'est pas encore visible, on l'ouvre d'abord
            if (key && activeSubmenu !== key) {
                e.preventDefault();
                showSubmenu(key, li);
            }
        });
    });

    // Gestion des clics sur les liens d'ancrage dans les sous-menus
    const submenuLinks = document.querySelectorAll('.nav-modal__submenu a[href*="#"]');

    // Parcourt chaque lien d'ancrage
    submenuLinks.forEach(function(link) {
        // Intercepte le clic sur le lien d'ancrage
        link.addEventListener('click', function(e) {
            // Empêche la navigation par défaut du lien
            e.preventDefault();

            // Récupère la valeur de l'attribut href du lien
            const href = this.getAttribute('href');
            // Extrait l'identifiant de la section cible après le #
            const targetId = href.split('#')[1];
            // Récupère l'élément DOM de la section cible
            const targetSection = document.getElementById(targetId);

            // Si la section cible existe dans le DOM
            if (targetSection) {
                // Ferme la modale de navigation
                closeModal();

                // Défile jusqu'à la section cible après un délai pour la fermeture de la modale
                setTimeout(function() {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }
        });
    });

    // Réinitialise les sous-menus quand la souris quitte la zone de la modale
    navInner.addEventListener('mouseleave', function() {
        resetSubmenus();
    });

    // Ouvre la modale au clic sur le bouton burger
    burgerBtn.addEventListener('click', openModal);
    // Ferme la modale au clic sur le bouton de fermeture
    navClose.addEventListener('click', closeModal);

    // Ferme la modale avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navModal.classList.contains('is-open')) {
            closeModal();
        }
    });

    // Ferme la modale en cliquant sur le fond d'écran (en dehors du contenu)
    navModal.addEventListener('click', function(e) {
        if (e.target === navModal) closeModal();
    });
})();
</script>