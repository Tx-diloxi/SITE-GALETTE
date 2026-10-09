<?php
/**
 * Purge des données personnelles arrivées en fin de conservation (RGPD).
 *
 * Durées annoncées dans la politique de confidentialité (docker/sql/03_legal_update.sql) :
 *   - demandes de contact          : 3 ans
 *   - candidatures et CV           : 2 ans
 *   - questions de l'assistant     : 12 mois
 *
 * À lancer en ligne de commande uniquement, par exemple une fois par mois via une tâche cron :
 *   php /chemin/vers/app/cli/purge_donnees.php
 * Sur LWS : panneau > Tâches Cron, avec la même commande (chemin absolu vers php et vers ce fichier).
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/paths.php';

// Durées de conservation (en mois)
const CONSERVATION_CONTACTS = 36;
const CONSERVATION_CANDIDATURES = 24;
const CONSERVATION_CHATBOT = 12;

// Supprime les contacts expirés
$stmt = $pdo->prepare("DELETE FROM formulaire_contact WHERE cree_le < DATE_SUB(NOW(), INTERVAL :mois MONTH)");
$stmt->execute([':mois' => CONSERVATION_CONTACTS]);
echo 'Contacts supprimés : ' . $stmt->rowCount() . PHP_EOL;

// Supprime les candidatures expirées, avec leur fichier CV
$dossierCv = WEB_ROOT . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR;
$stmt = $pdo->prepare("SELECT id, cv_path FROM applications WHERE cree_le < DATE_SUB(NOW(), INTERVAL :mois MONTH)");
$stmt->execute([':mois' => CONSERVATION_CANDIDATURES]);
$expirees = $stmt->fetchAll();

$suppression = $pdo->prepare("DELETE FROM applications WHERE id = ?");
foreach ($expirees as $candidature) {
    if (!empty($candidature['cv_path'])) {
        @unlink($dossierCv . basename((string)$candidature['cv_path']));
    }
    $suppression->execute([$candidature['id']]);
}
echo 'Candidatures supprimées : ' . count($expirees) . PHP_EOL;

// Supprime les questions de l'assistant expirées
$stmt = $pdo->prepare("DELETE FROM chatbot_log WHERE created_at < DATE_SUB(NOW(), INTERVAL :mois MONTH)");
$stmt->execute([':mois' => CONSERVATION_CHATBOT]);
echo 'Questions du chatbot supprimées : ' . $stmt->rowCount() . PHP_EOL;
