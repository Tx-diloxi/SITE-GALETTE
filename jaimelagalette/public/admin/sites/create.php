<?php
/**
 * Admin - Création d'un site
 *
 * Formulaire de création d'un nouveau site avec localisation
 * et horaires d'ouverture.
 */

declare(strict_types=1);

// ---- Inclusion des dépendances ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
require_once __DIR__ . '/../../../app/helpers/horaires.php';

// ---- Vérification d'authentification ----
admin_check_auth();

// ---- Initialisation ----
// Tableau associatif des jours de la semaine (lundi -> samedi)
$jours = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$error = null;

// ---- Traitement du formulaire ----
// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie la validité du token CSRF
    if (!admin_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF invalide.';
    // Vérifie que le champ nom n'est pas vide
    } elseif (empty(trim($_POST['nom'] ?? ''))) {
        $error = 'Le nom est requis.';
    // Vérifie que le champ adresse n'est pas vide
    } elseif (empty(trim($_POST['adresse'] ?? ''))) {
        $error = 'L\'adresse est requise.';
    // Vérifie que le champ code postal n'est pas vide
    } elseif (empty(trim($_POST['code_postal'] ?? ''))) {
        $error = 'Le code postal est requis.';
    // Vérifie que le champ ville n'est pas vide
    } elseif (empty(trim($_POST['ville'] ?? ''))) {
        $error = 'La ville est requise.';
    }

    // Si aucune erreur de validation, on insère le site
    if (empty($error)) {
        // Démarre une transaction pour garantir l'intégrité des données
        $pdo->beginTransaction();
        try {
            // Insère le nouveau site dans la table point_Carte
            $stmt = $pdo->prepare("
                INSERT INTO point_Carte (partial_carte_id, type_site, nom, adresse, code_postal, ville,
                    departement,                 telephone, email, email_rh, est_ouvert, latitude, longitude)
                VALUES (:partial_carte_id, :type_site, :nom, :adresse, :code_postal, :ville,
                    :departement, :telephone, :email, :email_rh, :est_ouvert, :latitude, :longitude)
            ");
            // Exécute la requête avec les valeurs du formulaire
            $stmt->execute([
                ':partial_carte_id' => 1,
                ':type_site' => $_POST['type_site'] ?? 'Atelier',
                ':nom' => $_POST['nom'],
                ':adresse' => $_POST['adresse'],
                ':code_postal' => $_POST['code_postal'],
                ':ville' => $_POST['ville'],
                ':departement' => $_POST['departement'] ?? '',
                ':telephone' => $_POST['telephone'] ?? '',
                ':email' => $_POST['email'] ?? '',
                ':email_rh' => $_POST['email_rh'] ?? '',
                ':est_ouvert' => 0,
                ':latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
                ':longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            ]);
            // Récupère l'ID du site qui vient d'être créé
            $site_id = (int)$pdo->lastInsertId();

            // Traite les horaires envoyés par le formulaire
            $horaires = [];
            // Boucle sur chaque jour pour récupérer ouverture/fermeture
            foreach (($_POST['horaire'] ?? []) as $jour => $times) {
                // N'ajoute le créneau que si les deux champs sont remplis
                if (!empty($times['ouverture']) && !empty($times['fermeture'])) {
                    $horaires[] = ['jour' => $jour, 'ouverture' => $times['ouverture'], 'fermeture' => $times['fermeture']];
                }
            }
            // Sauvegarde les horaires via la fonction utilitaire
            site_sauvegarder_horaires($pdo, $site_id, $horaires);

            // Met à jour le statut d'ouverture
            $hasHours = !empty($horaires);
            $upd = $pdo->prepare("UPDATE point_Carte SET est_ouvert = ? WHERE id = ?");
            $upd->execute([(int)$hasHours, $site_id]);

            // Valide la transaction
            $pdo->commit();
            $created = true;
        } catch (Exception $e) {
            // En cas d'erreur, annule toutes les modifications
            $pdo->rollBack();
            error_log('Erreur création site : ' . $e->getMessage());
            $error = 'Erreur lors de la création.';
        }
    }
}

// ---- Affichage ----
// Inclusion de l'en-tête de la page d'administration
require_once __DIR__ . '/../layout/header.php';
?>
<!-- Contenu principal de la page -->
<main class="admin-main">
    <!-- Barre d'outils avec le titre et le bouton de retour -->
    <div class="admin-toolbar">
        <h1>Nouveau site</h1>
        <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
    </div>

    <!-- Affiche un message d'erreur si la validation a échoué -->
    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <!-- Fin de l'affichage d'erreur -->

    <!-- Affiche un message de succès après création -->
    <?php if (!empty($created)): ?>
        <div class="admin-alert admin-alert--success">Site créé avec succès.</div>
        <!-- Carte d'information post-création avec liens d'action -->
        <div class="admin-card">
            <h2>Informations</h2>
            <p>Ce site peut avoir des étapes dans le savoir-faire. Éditer la page savoir-faire pour lier ce site.</p>
            <div class="admin-btn-group" style="margin-top:1rem;">
                <a href="/admin/sites/edit.php?id=<?= htmlspecialchars((string)$site_id, ENT_QUOTES, 'UTF-8') ?>" class="admin-btn admin-btn--primary">Modifier le site</a>
                <a href="/admin/sites/create.php" class="admin-btn admin-btn--secondary">Ajouter un autre site</a>
                <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            </div>
        </div>
    <!-- Sinon, affiche le formulaire de création -->
    <?php else: ?>
        <form method="POST">
            <!-- Champ caché pour le token CSRF (protection contre les attaques) -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

            <!-- Carte : informations générales du site -->
            <div class="admin-card">
                <h2>Informations générales</h2>
                <!-- Champ : type de site (atelier, siège, dépôt, autre) -->
                <div class="admin-field">
                    <label for="type_site">Type de site *</label>
                    <select name="type_site" id="type_site">
                        <option value="Atelier">Atelier</option>
                        <option value="Siège">Siège</option>
                        <option value="Dépôt">Dépôt</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <!-- Champ : nom du site -->
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" name="nom" id="nom" required>
                </div>
                <!-- Champ : adresse -->
                <div class="admin-field">
                    <label for="adresse">Adresse *</label>
                    <input type="text" name="adresse" id="adresse" required>
                </div>
                <!-- Champ : code postal (max 5 caractères) -->
                <div class="admin-field">
                    <label for="code_postal">Code postal *</label>
                    <input type="text" name="code_postal" id="code_postal" required maxlength="5">
                </div>
                <!-- Champ : ville -->
                <div class="admin-field">
                    <label for="ville">Ville *</label>
                    <input type="text" name="ville" id="ville" required>
                </div>
                <!-- Champ : département (optionnel) -->
                <div class="admin-field">
                    <label for="departement">Département</label>
                    <input type="text" name="departement" id="departement">
                </div>
                <!-- Champ : téléphone (optionnel) -->
                <div class="admin-field">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone">
                </div>
                <!-- Champ : email (optionnel) -->
                <div class="admin-field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email">
                </div>
                <div class="admin-field">
                    <label for="email_rh">Email RH</label>
                    <input type="email" name="email_rh" id="email_rh">
                </div>
                <!-- Bloc : localisation avec carte interactive et géocodage -->
                <div class="admin-field">
                    <label>Localisation</label>
                    <div id="geocode-status" style="font-size:0.85rem;color:var(--gris-text);margin-bottom:0.5rem;"></div>
                    <button type="button" id="geocode-btn" class="admin-btn admin-btn--small admin-btn--secondary" style="margin-bottom:0.75rem;">📍 Géocoder l'adresse</button>
                    <div id="map-preview" style="height:300px;border-radius:6px;border:1px solid #ddd;margin-bottom:0.75rem;display:none;"></div>
                    <div style="display:flex;gap:0.75rem;">
                        <div style="flex:1;">
                            <label for="latitude">Latitude</label>
                            <input type="number" name="latitude" id="latitude" step="any" readonly style="background:#f5f5f5;cursor:default;">
                        </div>
                        <div style="flex:1;">
                            <label for="longitude">Longitude</label>
                            <input type="number" name="longitude" id="longitude" step="any" readonly style="background:#f5f5f5;cursor:default;">
                        </div>
                    </div>
                    <small style="color:var(--gris-text);">Cliquez "Géocoder" pour localiser automatiquement l'adresse, ou déplacez le marqueur sur la carte.</small>
                </div>
            </div>
            <!-- Fin de la carte informations générales -->

            <!-- Carte : horaires d'ouverture -->
            <div class="admin-card">
                <h2>Horaires d'ouverture</h2>
                <p style="color:var(--gris-text);font-size:0.875rem;margin-bottom:1rem;">Définissez les plages horaires pour chaque jour. Le site sera automatiquement marqué comme ouvert si au moins un créneau est défini.</p>
                <!-- Tableau des créneaux horaires par jour -->
                <table class="admin-table">
                    <thead>
                        <tr><th>Jour</th><th>Ouverture</th><th>Fermeture</th></tr>
                    </thead>
                    <tbody>
                        <!-- Boucle sur les jours de la semaine pour afficher les champs horaires -->
                        <?php foreach ($jours as $num => $label): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><input type="time" name="horaire[<?= htmlspecialchars((string)$num, ENT_QUOTES, 'UTF-8') ?>][ouverture]" style="width:130px;padding:0.4rem;border:1px solid #ccc;border-radius:4px;"></td>
                            <td><input type="time" name="horaire[<?= htmlspecialchars((string)$num, ENT_QUOTES, 'UTF-8') ?>][fermeture]" style="width:130px;padding:0.4rem;border:1px solid #ccc;border-radius:4px;"></td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Fin de la boucle sur les jours -->
                    </tbody>
                </table>
                <small style="color:var(--gris-text);">Laissez vide si fermé ce jour-là.</small>
            </div>
            <!-- Fin de la carte horaires -->

            <!-- Bouton de soumission du formulaire -->
            <button type="submit" class="admin-btn admin-btn--primary">Créer le site</button>
        </form>
    <?php endif; ?>
    <!-- Fin du formulaire / message de succès -->
</main>
<!-- Inclusion de la bibliothèque Leaflet pour la carte interactive -->
<link rel="stylesheet" href="/assets/lib/leaflet/leaflet.css" />
<script src="/assets/lib/leaflet/leaflet.js"></script>
<!-- Script JavaScript pour la carte et le géocodage -->
<script>
(function() {
    // Récupère les éléments du DOM pour la carte et les champs de coordonnées
    const mapEl = document.getElementById('map-preview');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const statusEl = document.getElementById('geocode-status');
    const btn = document.getElementById('geocode-btn');
    let map, marker;
    // Coordonnées du centre de la France pour le zoom initial
    const FRANCE_CENTER = [46.603354, 1.888334];

    // Construit l'adresse complète à partir des champs du formulaire
    function getAddress() {
        const nom = document.getElementById('nom')?.value || '';
        const adr = document.getElementById('adresse')?.value || '';
        const cp = document.getElementById('code_postal')?.value || '';
        const ville = document.getElementById('ville')?.value || '';
        return [adr, cp, ville, nom].filter(Boolean).join(', ');
    }
    // Fin de getAddress

    // Place ou déplace un marqueur sur la carte aux coordonnées données
    function placeMarker(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        // Met à jour les champs quand l'utilisateur glisse le marqueur
        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            latInput.value = pos.lat.toFixed(7);
            lngInput.value = pos.lng.toFixed(7);
            statusEl.textContent = '✓ Marqueur déplacé.';
        });
        statusEl.textContent = '✓ Marqueur positionné. Glissez-le pour affiner.';
    }
    // Fin de placeMarker

    // Initialise la carte Leaflet centrée sur la France
    mapEl.style.display = 'block';
    map = L.map(mapEl).setView(FRANCE_CENTER, 6);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    // Permet de placer un marqueur en cliquant sur la carte
    map.on('click', function(e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });
    statusEl.textContent = 'Cliquez sur la carte pour placer le marqueur, ou utilisez "Géocoder".';
    setTimeout(() => map.invalidateSize(), 200);

    // Gère le clic sur le bouton de géocodage
    btn.addEventListener('click', function() {
        const address = getAddress();
        if (!address) { statusEl.textContent = 'Veuillez remplir l\'adresse d\'abord.'; return; }
        statusEl.textContent = 'Géocodage en cours…';
        btn.disabled = true;
        // Appelle l'API Nominatim pour convertir l'adresse en coordonnées
        const url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address) + '&limit=1';
        fetch(url, { headers: { 'User-Agent': 'JaLG-Admin/1.0' } })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    latInput.value = lat.toFixed(7);
                    lngInput.value = lng.toFixed(7);
                    statusEl.textContent = '✓ Localisé : ' + (data[0].display_name || address);
                    map.setView([lat, lng], 14);
                    placeMarker(lat, lng);
                } else {
                    statusEl.textContent = 'Aucune coordonnée trouvée pour cette adresse.';
                }
            })
            .catch(() => {
                btn.disabled = false;
                statusEl.textContent = 'Erreur lors du géocodage. Vérifiez votre connexion.';
            });
    });
})();
</script>
<!-- ---- -->
<!-- Inclusion du pied de page -->
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
