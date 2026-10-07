<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/admin.php';
admin_check_auth();

$questions = $pdo->query("SELECT id, categorie, question FROM inspection_question WHERE actif = TRUE ORDER BY ordre")->fetchAll();

$inspections = $pdo->query("
    SELECT i.id, i.note_generale, i.commentaire, i.cree_le,
           l.nom AS livreur_nom, l.prenom AS livreur_prenom, l.secteur AS livreur_secteur,
           c.nom AS commercial_nom, c.prenom AS commercial_prenom
    FROM inspection i
    JOIN livreur l ON l.id = i.livreur_id
    JOIN commercial c ON c.id = i.commercial_id
    ORDER BY i.cree_le DESC
")->fetchAll();

$reponses = $pdo->query("
    SELECT r.inspection_id, r.note, r.commentaire, r.question_id, q.categorie, q.question
    FROM inspection_reponse r
    JOIN inspection_question q ON q.id = r.question_id
    ORDER BY r.inspection_id, q.ordre
")->fetchAll();

$nbPhotos = $pdo->query("
    SELECT inspection_id, COUNT(*) AS nb FROM inspection_photo GROUP BY inspection_id
")->fetchAll();

$nbPhotosMap = [];
foreach ($nbPhotos as $np) {
    $nbPhotosMap[$np['inspection_id']] = $np['nb'];
}

$reponsesMap = [];
foreach ($reponses as $r) {
    $reponsesMap[$r['inspection_id']][] = $r;
}

$headers = ['ID', 'Date', 'Livreur', 'Prénom livreur', 'Secteur livreur', 'Commercial', 'Prénom commercial', 'Note /20', 'Commentaire général', 'Nb photos'];

$qColKeys = [];
foreach ($questions as $q) {
    $label = str_replace('"', '""', $q['categorie'] . ' - ' . $q['question']);
    $headers[] = $label . ' (note)';
    $headers[] = $label . ' (commentaire)';
    $qColKeys[$q['id']] = true;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="visites-mysteres-' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

fprintf($output, "\xEF\xBB\xBF");

fputcsv($output, $headers, ';');

foreach ($inspections as $i) {
    $row = [
        $i['id'],
        date('d/m/Y H:i', strtotime($i['cree_le'])),
        $i['livreur_nom'],
        $i['livreur_prenom'],
        $i['livreur_secteur'] ?? '',
        $i['commercial_nom'],
        $i['commercial_prenom'],
        $i['note_generale'] !== null ? $i['note_generale'] . '/20' : '',
        $i['commentaire'] ?? '',
        $nbPhotosMap[$i['id']] ?? 0,
    ];

    $iReponses = $reponsesMap[$i['id']] ?? [];
    $reponsesByQ = [];
    foreach ($iReponses as $r) {
        $reponsesByQ[$r['question_id']] = $r;
    }

    foreach ($questions as $q) {
        $rep = $reponsesByQ[$q['id']] ?? null;
        $row[] = $rep ? $rep['note'] : '';
        $row[] = $rep && $rep['commentaire'] ? $rep['commentaire'] : '';
    }

    fputcsv($output, $row, ';');
}

fclose($output);
exit;