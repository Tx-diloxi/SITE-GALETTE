<?php
/**
 * Admin - Modification d'un site
 *
 * Formulaire d'édition d'un site existant avec mise à jour
 * des coordonnées, horaires et statut.
 */

declare(strict_types=1);

// ---- Inclusion des dépendances ----
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';

// ---- Vérification d'authentification ----
admin_check_auth();

// ---- Récupération des données ----
// Récupère l'ID du site depuis l'URL
$id = (int)($_GET['id'] ?? 0);
// Redirige vers la liste si l'ID est invalide
if ($id <= 0) {
    admin_redirect('admin/sites/index.php');
}

// Récupère les informations du site depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM point_Carte WHERE id = :id");
$stmt->execute([':id' => $id]);
$site = $stmt->fetch();

// Inclusion des fonctions de gestion des horaires
require_once __DIR__ . '/../../../app/helpers/horaires.php';
// Vérifie si le site est ouvert en temps réel
$est_ouvert_realtime = site_est_ouvert($pdo, $id);
// Récupère les horaires existants du site
$existingHoraires = site_get_horaires($pdo, $id);
// Formate les horaires en texte lisible
$horaires_text = site_format_horaires_text($existingHoraires);

// Redirige si le site n'existe pas en base
if (!$site) {
    admin_redirect('admin/sites/index.php');
}

$error = null;
// Tableau associatif des jours de la semaine (lundi -> samedi)
$jours = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];

// ---- Organisation des horaires ----
// Organise les horaires par jour pour faciliter l'affichage dans le formulaire
$horairesByJour = [];
foreach ($existingHoraires as $h) {
    $j = $h['jour'];
    if (!isset($horairesByJour[$j])) $horairesByJour[$j] = [];
    $horairesByJour[$j][] = $h;
}
// Fin de la boucle de réorganisation des horaires

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

    // Si aucune erreur de validation, on met à jour le site
    if (empty($error)) {
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
        site_sauvegarder_horaires($pdo, $id, $horaires);
        $horaireText = site_format_horaires_text(site_get_horaires($pdo, $id));

        // Met à jour les informations du site dans la base de données
        $stmt = $pdo->prepare("
            UPDATE point_Carte SET
                type_site = :type_site, nom = :nom, adresse = :adresse,
                code_postal = :code_postal, ville = :ville, departement = :departement,
                telephone = :telephone, email = :email, email_rh = :email_rh,
                est_ouvert = :est_ouvert,
                latitude = :latitude, longitude = :longitude
            WHERE id = :id
        ");
        $stmt->execute([
            ':type_site' => $_POST['type_site'] ?? 'Atelier',
            ':nom' => $_POST['nom'],
            ':adresse' => $_POST['adresse'],
            ':code_postal' => $_POST['code_postal'],
            ':ville' => $_POST['ville'],
            ':departement' => $_POST['departement'] ?? '',
            ':telephone' => $_POST['telephone'] ?? '',
            ':email' => $_POST['email'] ?? '',
            ':email_rh' => $_POST['email_rh'] ?? '',
            ':est_ouvert' => (int)!empty($horaireText),
            ':latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
            ':longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            ':id' => $id,
        ]);
        // Redirige vers la page d'édition avec un message de succès
        admin_redirect('admin/sites/edit.php?id=' . $id . '&saved=1');
    }
}
// Fin du traitement du formulaire

// ---- Affichage ----
// Inclusion de l'en-tête de la page d'administration
require_once __DIR__ . '/../layout/header.php';
?>
<!-- Contenu principal de la page -->
<main class="admin-main">
    <!-- Barre d'outils avec le titre et les boutons d'action -->
    <div class="admin-toolbar">
        <h1>Modifier : <?= htmlspecialchars($site['nom'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="admin-btn-group">
            <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
            <a href="/admin/sites/delete.php?id=<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>" class="admin-btn admin-btn--danger">Supprimer</a>
        </div>
    </div>

    <!-- Affiche un message de confirmation après enregistrement -->
    <?php if (isset($_GET['saved'])): ?>
        <div class="admin-alert admin-alert--success">Site enregistré.</div>
    <?php endif; ?>
    <!-- Affiche un message d'erreur si la validation a échoué -->
    <?php if (!empty($error)): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Formulaire de modification du site -->
    <form method="POST">
        <!-- Champ caché pour le token CSRF (protection contre les attaques) -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(admin_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="partial_carte_id" value="1">

        <!-- Carte : informations générales du site -->
        <div class="admin-card">
            <!-- Champ : type de site -->
            <div class="admin-field">
                <label for="type_site">Type de site *</label>
                <select name="type_site" id="type_site">
                    <option value="Atelier" <?= $site['type_site'] === 'Atelier' ? 'selected' : '' ?>>Atelier</option>
                    <option value="Siège" <?= $site['type_site'] === 'Siège' ? 'selected' : '' ?>>Siège</option>
                    <option value="Dépôt" <?= $site['type_site'] === 'Dépôt' ? 'selected' : '' ?>>Dépôt</option>
                    <option value="Autre" <?= $site['type_site'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
            <!-- Champ : nom du site -->
            <div class="admin-field">
                <label for="nom">Nom *</label>
                <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($site['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <!-- Champ : adresse -->
            <div class="admin-field">
                <label for="adresse">Adresse *</label>
                <input type="text" name="adresse" id="adresse" value="<?= htmlspecialchars($site['adresse'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <!-- Champ : code postal -->
            <div class="admin-field">
                <label for="code_postal">Code postal *</label>
                <input type="text" name="code_postal" id="code_postal" value="<?= htmlspecialchars($site['code_postal'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="5">
            </div>
            <!-- Champ : ville -->
            <div class="admin-field">
                <label for="ville">Ville *</label>
                <input type="text" name="ville" id="ville" value="<?= htmlspecialchars($site['ville'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <!-- Champ : département (optionnel) -->
            <div class="admin-field">
                <label for="departement">Département</label>
                <input type="text" name="departement" id="departement" value="<?= htmlspecialchars($site['departement'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <!-- Champ : téléphone (optionnel) -->
            <div class="admin-field">
                <label for="telephone">Téléphone</label>
                <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($site['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <!-- Champ : email (optionnel) -->
            <div class="admin-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($site['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="admin-field">
                <label for="email_rh">Email RH</label>
                <input type="email" name="email_rh" id="email_rh" value="<?= htmlspecialchars($site['email_rh'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <!-- Bloc : horaires d'ouverture -->
            <div class="admin-field">
                <label>Horaires d'ouverture</label>
                <!-- Affiche le résumé des horaires actuels s'il existe -->
                <?php if ($horaires_text): ?>
                <div style="margin-bottom:0.75rem;padding:0.5rem 0.75rem;background:#f0f7ff;border-radius:4px;font-size:0.85rem;">
                    Résumé actuel : <strong><?= htmlspecialchars($horaires_text, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <?php endif; ?>
                <!-- Tableau des créneaux horaires par jour -->
                <table class="admin-table">
                    <thead>
                        <tr><th>Jour</th><th>Ouverture</th><th>Fermeture</th></tr>
                    </thead>
                    <tbody>
                        <!-- Boucle sur les jours de la semaine pour afficher les champs horaires -->
                        <?php foreach ($jours as $num => $label):
                            $slot = $horairesByJour[$num][0] ?? null;
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><input type="time" name="horaire[<?= htmlspecialchars((string)$num, ENT_QUOTES, 'UTF-8') ?>][ouverture]" value="<?= htmlspecialchars($slot['ouverture'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:130px;padding:0.4rem;border:1px solid #ccc;border-radius:4px;"></td>
                            <td><input type="time" name="horaire[<?= htmlspecialchars((string)$num, ENT_QUOTES, 'UTF-8') ?>][fermeture]" value="<?= htmlspecialchars($slot['fermeture'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:130px;padding:0.4rem;border:1px solid #ccc;border-radius:4px;"></td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Fin de la boucle sur les jours -->
                    </tbody>
                </table>
                <small style="color:var(--gris-text);">Laissez vide si fermé ce jour-là.</small>
            </div>
            <!-- Bloc : statut ouvert/fermé en temps réel -->
            <div class="admin-field">
                <label>Statut en temps réel</label>
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <span class="admin-badge <?= $est_ouvert_realtime ? 'admin-badge--success' : 'admin-badge--danger' ?>">
                        <?= $est_ouvert_realtime ? 'Ouvert' : 'Fermé' ?>
                    </span>
                    <small style="color:var(--gris-text);">Calculé depuis les horaires ci-dessus.</small>
                </div>
            </div>
            <!-- Bloc : localisation avec carte interactive -->
            <div class="admin-field">
                <label>Localisation</label>
                <div id="geocode-status" style="font-size:0.85rem;color:var(--gris-text);margin-bottom:0.5rem;"></div>
                <button type="button" id="geocode-btn" class="admin-btn admin-btn--small admin-btn--secondary" style="margin-bottom:0.75rem;">📍 Géocoder l'adresse</button>
                <div id="map-preview" style="height:300px;border-radius:6px;border:1px solid #ddd;margin-bottom:0.75rem;display:none;"></div>
                <div style="display:flex;gap:0.75rem;">
                    <div style="flex:1;">
                        <label for="latitude">Latitude</label>
                        <input type="number" name="latitude" id="latitude" step="any" value="<?= htmlspecialchars($site['latitude'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#f5f5f5;cursor:default;">
                    </div>
                    <div style="flex:1;">
                        <label for="longitude">Longitude</label>
                        <input type="number" name="longitude" id="longitude" step="any" value="<?= htmlspecialchars($site['longitude'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#f5f5f5;cursor:default;">
                    </div>
                </div>
                <small style="color:var(--gris-text);">Cliquez "Géocoder" pour localiser automatiquement l'adresse, ou déplacez le marqueur sur la carte.</small>
            </div>
        </div>
        <!-- Fin de la carte informations générales -->

        <!-- Carte d'information supplémentaire -->
        <div class="admin-card">
            <h2>Informations</h2>
            <p>Ce site peut avoir des étapes dans le savoir-faire. Éditer la page savoir-faire pour lier ce site.</p>
        </div>

        <!-- Boutons d'action du formulaire -->
        <div class="admin-btn-group">
            <button type="submit" class="admin-btn admin-btn--primary">Enregistrer</button>
            <a href="/admin/sites/index.php" class="admin-btn admin-btn--secondary">Retour à la liste</a>
        </div>
    </form>
</main>
<!-- Inclusion de la bibliothèque Leaflet pour la carte interactive -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

    // Initialise la carte Leaflet centrée sur les coordonnées données
    function initMap(lat, lng) {
        mapEl.style.display = 'block';
        if (!map) {
            map = L.map(mapEl).setView([lat, lng], 14);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            // Permet de placer un marqueur en cliquant sur la carte
            map.on('click', function(e) {
                placeMarker(e.latlng.lat, e.latlng.lng);
            });
        } else {
            map.setView([lat, lng], 14);
        }
        placeMarker(lat, lng);
        setTimeout(() => map.invalidateSize(), 200);
    }
    // Fin de initMap

    // Si le site a déjà des coordonnées, initialise la carte avec celles-ci
    if (latInput.value && lngInput.value) {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            initMap(lat, lng);
            statusEl.textContent = '✓ Position actuelle. Glissez le marqueur ou cliquez sur la carte pour ajuster.';
        } else {
            initMap(FRANCE_CENTER[0], FRANCE_CENTER[1]);
            map.setZoom(6);
            statusEl.textContent = 'Cliquez sur la carte pour placer le marqueur, ou utilisez "Géocoder".';
        }
    } else {
        // Sinon, initialise la carte centrée sur la France
        mapEl.style.display = 'block';
        map = L.map(mapEl).setView(FRANCE_CENTER, 6);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        map.on('click', function(e) {
            placeMarker(e.latlng.lat, e.latlng.lng);
        });
        statusEl.textContent = 'Cliquez sur la carte pour placer le marqueur, ou utilisez "Géocoder".';
        setTimeout(() => map.invalidateSize(), 200);
    }

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
                    if (!map) {
                        initMap(lat, lng);
                    } else {
                        map.setView([lat, lng], 14);
                        placeMarker(lat, lng);
                    }
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
