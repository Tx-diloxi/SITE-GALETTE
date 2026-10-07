<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// Tableau de correspondance des jours de la semaine en français
$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];

// Vérifie si un point de vente (atelier) est ouvert à une date donnée
function site_est_ouvert(PDO $pdo, int $pointCarteId, ?DateTime $date = null): bool {
    // Vérifie s'il y a des horaires définis pour ce point de vente
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM horaire_Site WHERE point_carte_id = ?");
    $stmt->execute([$pointCarteId]);
    // Si aucun horaire n'est défini, le site est considéré comme toujours ouvert
    if ((int)$stmt->fetchColumn() === 0) {
        return true;
    }
    // Récupère la date et l'heure courantes dans le fuseau Europe/Paris
    $date = $date ?? new DateTime('now', new DateTimeZone('Europe/Paris'));
    $jour = (int)$date->format('w');
    $heure = $date->format('H:i:s');
    // Vérifie si l'heure actuelle se trouve dans une plage horaire définie pour ce jour
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM horaire_Site WHERE point_carte_id = ? AND jour = ? AND ouverture <= ? AND fermeture >= ?");
    $stmt->execute([$pointCarteId, $jour, $heure, $heure]);
    return (int)$stmt->fetchColumn() > 0;
}

// Récupère tous les horaires d'un point de vente depuis la base de données
function site_get_horaires(PDO $pdo, int $pointCarteId): array {
    global $joursFr;
    $stmt = $pdo->prepare("SELECT * FROM horaire_Site WHERE point_carte_id = ? ORDER BY jour, ouverture");
    $stmt->execute([$pointCarteId]);
    $rows = $stmt->fetchAll();
    $horaires = [];
    foreach ($rows as $row) {
        $jourLabel = $joursFr[(int)$row['jour']] ?? 'Jour ' . $row['jour'];
        $horaires[] = [
            'id' => $row['id'],
            'jour' => (int)$row['jour'],
            'jour_label' => $jourLabel,
            'ouverture' => substr($row['ouverture'], 0, 5),
            'fermeture' => substr($row['fermeture'], 0, 5),
        ];
    }
    return $horaires;
}

// Sauvegarde les horaires d'un point de vente en remplaçant complètement les anciens
function site_sauvegarder_horaires(PDO $pdo, int $pointCarteId, array $horaires): void {
    // Supprime tous les anciens horaires pour ce point de vente
    $stmt = $pdo->prepare("DELETE FROM horaire_Site WHERE point_carte_id = ?");
    $stmt->execute([$pointCarteId]);
    // Insère les nouveaux horaires dans la base
    $insert = $pdo->prepare("INSERT INTO horaire_Site (point_carte_id, jour, ouverture, fermeture) VALUES (?, ?, ?, ?)");
    foreach ($horaires as $h) {
        $jour = (int)($h['jour'] ?? 0);
        $ouverture = $h['ouverture'] ?? '00:00';
        $fermeture = $h['fermeture'] ?? '00:00';
        if ($ouverture && $fermeture) {
            $insert->execute([$pointCarteId, $jour, $ouverture . ':00', $fermeture . ':00']);
        }
    }
}

// Formate les horaires en une chaîne de texte lisible et compacte
function site_format_horaires_text(array $horaires): string {
    if (empty($horaires)) return '';
    $groups = [];
    foreach ($horaires as $h) {
        $key = $h['ouverture'] . '-' . $h['fermeture'];
        if (!isset($groups[$key])) $groups[$key] = [];
        $groups[$key][] = $h['jour_label'];
    }
    $parts = [];
    foreach ($groups as $plage => $jours) {
        if (count($jours) > 2) {
            $parts[] = $jours[0] . '-' . end($jours) . ' ' . $plage;
        } elseif (count($jours) === 2) {
            $parts[] = $jours[0] . ' & ' . $jours[1] . ' ' . $plage;
        } else {
            $parts[] = $jours[0] . ' ' . $plage;
        }
    }
    return implode(', ', $parts);
}
