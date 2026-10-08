<?php
// Initialise le flag de chargement unique du style du partial chiffres-groupe
static $chiffresGroupeStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$chiffresGroupeStyleLoaded): $chiffresGroupeStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial chiffres-groupe -->
<link rel="stylesheet" href="/assets/css/partials/chiffres-groupe/chiffres-groupe.css">
<?php endif; ?>

<?php
// Vérifie que la variable de condition du partial chiffres est définie
if ($chiffres):
    // Requêtes dynamiques pour les vraies données depuis la table point_Carte
    try {
        [$totalSites, $totalRegions] = array_map('intval', $pdo->query("SELECT COUNT(*), COUNT(DISTINCT departement) FROM point_Carte")->fetch(PDO::FETCH_NUM));
    } catch (Exception $e) {
        $totalSites = 0;
        $totalRegions = 0;
    }
    // Remplace les valeurs statiques par les données réelles de la BDD
    $cardsChiffres = array_map(function ($card) use ($totalSites, $totalRegions) {
        if (isset($card['titre']) && $card['titre'] === 'x') {
            $card['titre'] = (string) $totalSites;
        }
        if (isset($card['titre']) && str_contains($card['titre'], 'régions')) {
            $card['titre'] = str_replace('x', (string) $totalRegions, $card['titre']);
        }
        return $card;
    }, $cardsChiffres);
?>
<!-- Section racine du partial chiffres-groupe -->
<section id="chiffres_groupe">
    <!-- Conteneur des titres de la section chiffres -->
    <div class="titre">
        <!-- Affiche le sous-titre de la section chiffres -->
        <h3><?= htmlspecialchars($chiffres['sous_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
        <!-- Affiche le titre principal de la section chiffres -->
        <h2><?= htmlspecialchars($chiffres['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>
    <!-- Grille des cartes chiffres -->
    <div class="grille-chiffres grid-5">
        <?php
        // Boucle sur chaque carte de chiffre
        foreach ($cardsChiffres as $card): ?>
        <!-- Carte individuelle de chiffre -->
        <div class="carte-chiffre card">
            <!-- Image illustrant le chiffre -->
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($card['alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <!-- Valeur du chiffre -->
            <span><?= htmlspecialchars($card['titre'], ENT_QUOTES, 'UTF-8') ?></span>
            <!-- Description associée au chiffre -->
            <p><?= htmlspecialchars($card['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php
        // Fin de boucle des cartes chiffres
        endforeach; ?>
    </div>
    <?php
    // Vérifie la présence du CTA avant affichage
    if ($chiffres['cta_label'] && $chiffres['cta_lien']): ?>
    <!-- Conteneur du bouton d'appel à l'action -->
    <div class="cta-container">
        <!-- Lien du bouton CTA chiffres groupe -->
        <a href="<?= htmlspecialchars($chiffres['cta_lien'], ENT_QUOTES, 'UTF-8') ?>">
            <button><?= htmlspecialchars($chiffres['cta_label'], ENT_QUOTES, 'UTF-8') ?></button>
        </a>
    </div>
    <?php
    endif; ?>
</section>
<?php
endif; ?>
