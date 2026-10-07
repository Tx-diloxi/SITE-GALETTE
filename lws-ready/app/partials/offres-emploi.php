<?php
// --- Partial: Offres d'emploi ---
// Page : Recrutement
// Variables attendues :
// - $offreTitre : tableau contenant sous_titre, titre
// - $offresEmploi : tableau d'offres (titre, contrat, lieu, cta_label, date_publication)
// - $messageCandidatureSpontanee : texte optionnel pour les candidatures spontanées

// Initialise le flag de chargement unique du style
static $offresEmploiStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$offresEmploiStyleLoaded): $offresEmploiStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial offres emploi -->
<link rel="stylesheet" href="/assets/css/partials/offres-emploi/offres-emploi.css">
<?php endif; ?>

<?php
// Vérifie que les variables de condition du partial sont définies et non vides
if (!empty($offreTitre) && !empty($offresEmploi)):
?>
<!-- Section racine du partial Nos offres d'emploi -->
<section id="offres-emploi">

    <!-- Conteneur des titres de la section -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section -->
        <h3><?= htmlspecialchars($offreTitre['sous_titre'] ?? 'Nous rejoindre', ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section -->
        <h2><?= htmlspecialchars($offreTitre['titre'] ?? 'Nos offres d\'emploi', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <!-- Grille des offres d'emploi -->
    <div class="offres-grille">
        <?php foreach ($offresEmploi as $offre): ?>
        <!-- Carte offre individuelle -->
        <article class="offre-carte">
            <!-- Titre du poste -->
            <h3 class="offre-titre"><?= htmlspecialchars($offre['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>

            <!-- Métadonnées de l'offre -->
            <div class="offre-metadonnees">
                <!-- Type de contrat -->
                <div class="offre-metadonnee">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <span><?= htmlspecialchars($offre['contrat'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <!-- Localisation -->
                <div class="offre-metadonnee">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span><?= htmlspecialchars($offre['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <!-- Date de publication -->
                <div class="offre-metadonnee">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span>Publié le <?= date('d M Y', strtotime($offre['date_publication'])) ?></span>
                </div>
            </div>

            <!-- Bouton Découvrir → ouvre la modale -->
            <div class="offre-cta-container">
                <button type="button" class="offre-cta" data-offre-id="<?= $offre['id'] ?>">
                    <?= htmlspecialchars($offre['cta_label'] ?? 'Découvrir', ENT_QUOTES, 'UTF-8') ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Section candidature spontanée -->
    <?php if (!empty($candidatureSpontanee)): ?>
    <div class="candidature-spontanee">
        <div class="candidature-spontanee-icone">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
            </svg>
        </div>
        <h3 class="candidature-spontanee-titre">
            <?= htmlspecialchars($candidatureSpontanee['titre'] ?? 'Pas d\'offre en ce moment qui correspond à votre profil ?', ENT_QUOTES, 'UTF-8') ?>
        </h3>
        <p class="candidature-spontanee-description">
            <?= htmlspecialchars($candidatureSpontanee['description'] ?? 'On aime rencontrer des gens passionnés. Envoyez-nous votre candidature spontanée !', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php if (!empty($candidatureSpontanee['cta_label']) && !empty($candidatureSpontanee['cta_lien'])): ?>
        <a href="<?= htmlspecialchars($candidatureSpontanee['cta_lien'], ENT_QUOTES, 'UTF-8') ?>"
            class="candidature-spontanee-cta">
            <?= htmlspecialchars($candidatureSpontanee['cta_label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<!-- Modal de détail d'offre -->
<div class="offre-modal-overlay" id="offreModal" aria-hidden="true">
    <div class="offre-modal" role="dialog" aria-modal="true" aria-label="Détails de l'offre">
        <button class="offre-modal-close" aria-label="Fermer">&times;</button>
        <div class="offre-modal-content">
            <h3 class="offre-modal-titre"></h3>
            <div class="offre-modal-metadonnees">
                <span class="offre-modal-badge offre-modal-contrat"></span>
                <span class="offre-modal-badge offre-modal-lieu"></span>
                <span class="offre-modal-badge offre-modal-date"></span>
            </div>
            <div class="offre-modal-description"></div>
        </div>
    </div>
</div>

<script type="application/json" id="offresData"><?= json_encode($offresEmploi, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var offresData = {};
    try {
        offresData = JSON.parse(document.getElementById('offresData').textContent);
    } catch (e) { return; }

    var modal = document.getElementById('offreModal');
    var modalContent = modal.querySelector('.offre-modal-content');
    var modalClose = modal.querySelector('.offre-modal-close');
    var modalTitre = modalContent.querySelector('.offre-modal-titre');
    var modalContrat = modalContent.querySelector('.offre-modal-contrat');
    var modalLieu = modalContent.querySelector('.offre-modal-lieu');
    var modalDate = modalContent.querySelector('.offre-modal-date');
    var modalDescription = modalContent.querySelector('.offre-modal-description');

    function getOfferById(id) {
        for (var i = 0; i < offresData.length; i++) {
            if (String(offresData[i].id) === String(id)) return offresData[i];
        }
        return null;
    }

    document.querySelectorAll('[data-offre-id]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var offre = getOfferById(this.getAttribute('data-offre-id'));
            if (!offre) return;

            modalTitre.textContent = offre.titre || '';
            modalContrat.textContent = offre.contrat || '';
            modalLieu.textContent = offre.lieu || '';
            modalDate.textContent = offre.date_publication ? 'Publié le ' + new Date(offre.date_publication).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
            var safeDesc = (offre.description || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            modalDescription.innerHTML = safeDesc.replace(/\n/g, '<br>');

            document.body.style.overflow = 'hidden';
            modal.setAttribute('aria-hidden', 'false');
            modal.classList.add('is-open');
        });
    });

    function closeModal() {
        document.body.style.overflow = '';
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('is-open');
    }

    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
});
</script>
<?php endif; ?>