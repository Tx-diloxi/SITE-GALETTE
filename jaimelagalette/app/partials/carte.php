<?php
// Initialise le flag de chargement unique du style du partial carte
static $carteStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$carteStyleLoaded): $carteStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial carte -->
<link rel="stylesheet" href="/assets/css/partials/carte/carte.css">
<!-- Inclut la feuille de style CSS spécifique aux popups Leaflet du partial carte -->
<link rel="stylesheet" href="/assets/css/partials/carte/card-carte.css">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="/assets/lib/leaflet/leaflet.css" />
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial carte est définie et vraie
if ($carte):
    // Calcule l'ouverture et les horaires en temps réel depuis horaire_Site
    require_once HELPERS . 'horaires.php';
    $pointsCarte = array_map(function ($p) use ($pdo) {
        $p['est_ouvert'] = site_est_ouvert($pdo, (int)$p['id']);
        $dynamicHoraires = site_get_horaires($pdo, (int)$p['id']);
        if (!empty($dynamicHoraires)) {
            $p['horaires'] = site_format_horaires_text($dynamicHoraires);
        }
        return $p;
    }, $pointsCarte);
    ?>

<!-- Section principale de la carte -->
<section id="carte">
    <!-- Conteneur principal de la zone de carte -->
    <div class="carte-container">
        <!-- Titre de la section carte -->
        <div class="titre">
            <!-- Affiche le sous-titre de la carte -->
            <h3><?= htmlspecialchars($carte['sous_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
            <!-- Affiche le titre principal de la carte -->
            <h2><?= htmlspecialchars($carte['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
        </div>

        <!-- Conteneur relatif pour la carte et la mascotte -->
        <div class="map-wrapper">
            <!-- Zone de rendu Leaflet -->
            <div id="map"></div>
            <!-- Mascotte décorative visible sur la carte -->
            <img src="/assets/images/mascotte_carte.png" class="mascotte" alt="Mascotte pointant vers la carte"
                loading="lazy">
        </div>
    </div>
</section>

<!-- Leaflet JS -->
<script src="/assets/lib/leaflet/leaflet.js"></script>
<script>
const map = L.map('map').setView([48.083328, -1.68333], 6);

L.tileLayer(
    'https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; OpenStreetMap contributors'
    }).addTo(map);

const vendeurs = <?= json_encode($pointsCarte, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function escHtml(str) {
    return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

const icons = {
    adresse: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`,
    telephone: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.02z"/></svg>`,
    email: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>`,
    horaires: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`
};

if (Array.isArray(vendeurs)) {
    vendeurs.forEach(vendeur => {
        const lat = parseFloat(vendeur.latitude);
        const lon = parseFloat(vendeur.longitude);

        if (!lat || !lon) return;

        const adresseRow = vendeur.adresse ? `
            <div class="info-row">
                <div class="info-icon">${icons.adresse}</div>
                <div class="info-content">
                    <span class="info-label">Adresse</span>
                    <span class="info-value">${escHtml(vendeur.adresse)}</span>
                </div>
            </div>` : '';

        const telRow = vendeur.telephone ? `
            <div class="info-row">
                <div class="info-icon">${icons.telephone}</div>
                <div class="info-content">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value"><a href="tel:${escHtml(vendeur.telephone)}">${escHtml(vendeur.telephone)}</a></span>
                </div>
            </div>` : '';

        const emailRow = vendeur.email ? `
            <div class="info-row">
                <div class="info-icon">${icons.email}</div>
                <div class="info-content">
                    <span class="info-label">E-mail</span>
                    <span class="info-value"><a href="mailto:${escHtml(vendeur.email)}">${escHtml(vendeur.email)}</a></span>
                </div>
            </div>` : '';

        const horairesRow = vendeur.horaires ? `
            <div class="info-row highlight">
                <div class="info-icon">${icons.horaires}</div>
                <div class="info-content">
                    <span class="info-label">${escHtml(vendeur.jours ?? 'Horaires')}</span>
                    <span class="info-value">${escHtml(vendeur.horaires)}</span>
                </div>
            </div>` : '';

        const badge = vendeur.est_ouvert ?
            '<span class="badge-status ouvert">Ouvert</span>' :
            '<span class="badge-status ferme">Fermé</span>';

        const popupContent = `
            <div class="galette-popup-card">
                <div class="galette-popup-card-header">
                    <h2>${escHtml(vendeur.type ?? 'Site de production')}</h2>
                    <h3>${escHtml(vendeur.nom ?? '')}</h3>
                </div>
                <div class="galette-popup-card-body">
                    ${adresseRow}
                    ${telRow}
                    ${emailRow}
                    ${horairesRow}
                </div>
                <div class="galette-popup-card-footer">
                    <span class="localisation">${escHtml(vendeur.ville ?? '')} &bull; ${escHtml(vendeur.code_postal ?? '')}</span>
                    ${badge}
                </div>
            </div>
        `;

        const marker = L.marker([lat, lon]).addTo(map);
        marker.bindPopup(popupContent, {
            maxWidth: 320,
            className: 'leaflet-popup-galette'
        });
    });
}
</script>
<?php endif; ?>