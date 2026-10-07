<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// En-têtes HTTP : on répond toujours en JSON avec UTF-8
header('Content-Type: application/json; charset=utf-8');
// Autorise les requêtes depuis n'importe quel domaine (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si la requête est une pré-vérification CORS (OPTIONS), on répond 204 et on stoppe
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Seules les requêtes POST sont acceptées
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Lecture et décodage du corps JSON de la requête
$input = json_decode(file_get_contents('php://input'), true);
// Si le JSON est invalide ou si le champ 'message' est vide, on renvoie une erreur 400
if (!$input || empty($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message requis']);
    exit;
}

// Connexion à la base de données
require_once __DIR__ . '/../app/config/database.php';

// Extraction des données utiles
$message  = trim($input['message']);       // La question posée par l'utilisateur
$profil   = $input['profil'] ?? null;       // Profil utilisateur (particulier, pro, etc.)
$pageUrl  = $input['page'] ?? null;         // Page d'où vient la requête

// ---- Normalisation de la question ----
// Convertit un texte en minuscules sans accents ni caractères spéciaux
function normalizeText(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'ô', 'ö', 'î', 'ï', 'ç', '\'', '"'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'o', 'o', 'i', 'i', 'c', ' ', ' '],
        $text
    );
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

// Découpe le texte en mots-clés (tokens) en filtrant les mots vides (stopwords)
function tokenize(string $text): array {
    // Liste des mots courants français sans intérêt pour la recherche
    $stopwords = ['de', 'la', 'le', 'les', 'des', 'du', 'et', 'un', 'une', 'dans', 'pour', 'sur',
                  'est', 'pas', 'que', 'qui', 'quoi', 'comment', 'ou', 'ou', 'en', 'au', 'aux',
                  'avec', 'ce', 'cest', 'ces', 'ses', 'son', 'sa', 'mes', 'tes', 'nos', 'vos',
                  'a', 'as', 'il', 'elle', 'on', 'nous', 'vous', 'ils', 'elles', 'par', 'plus',
                  'ne', 'ni', 'mais', 'car', 'donc', 'si', 'tout', 'tous', 'tres', 'bien', 'fait',
                  'faire', 'peut', 'avez', 'aurez', 'ont', 'sont', 'etes', 'suis', 'sommes', 'ete',
                  'quel', 'quelle', 'quels', 'quelles', 'quand', 'combien', 'pourquoi'];
    $words = explode(' ', $text);
    // On garde uniquement les mots de plus de 2 lettres qui ne sont pas des stopwords
    $words = array_values(array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, $stopwords)));
    // Stemming simple : suppression du 's' final des pluriels français
    $words = array_map(function($w) {
        if (strlen($w) > 3 && str_ends_with($w, 's')) {
            return substr($w, 0, -1);
        }
        return $w;
    }, $words);
    return array_values(array_unique($words));
}

// Cherche la meilleure réponse FAQ correspondant à la question posée
function findBestFaqAnswer(PDO $pdo, string $messageRaw, ?string $profil): ?array {
    $normalized = normalizeText($messageRaw);
    $tokens = tokenize($normalized);

    // Aucun mot-clé exploitable -> pas de réponse possible
    if (empty($tokens)) return null;

    $candidates = [];
    $isFallback = false;

    // Étape 1 : recherche FULLTEXT en mode BOOLEAN avec tous les tokens obligatoires (+*)
    $fulltextTokens = array_map(fn($t) => $t . '*', array_slice($tokens, 0, 5));
    $fulltextQuery = '+' . implode(' +', $fulltextTokens);
    if (strlen($fulltextQuery) > 3) {
        $sql = "SELECT q.*, c.titre as categorie_titre
                FROM question_FAQ q
                JOIN categorie_FAQ c ON q.categorie_faq_id = c.id
                WHERE q.en_ligne = TRUE
                  AND MATCH(q.question, q.mots_cles) AGAINST(:query IN BOOLEAN MODE)";

        $params = ['query' => $fulltextQuery];

        // Filtre par profil si fourni
        if ($profil) {
            $sql .= " AND (q.profil_cible IS NULL OR q.profil_cible = :profil)";
            $params['profil'] = $profil;
        }

        $sql .= " LIMIT 5";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $candidates = $stmt->fetchAll();
    }

    // Étape 2 : fallback avec LIKE (un seul token suffit pour matcher)
    if (empty($candidates)) {
        $isFallback = true;
        $wordConditions = [];
        $params = [];
        foreach ($tokens as $i => $token) {
            $wordConditions[] = "(q.question LIKE :like_q{$i} OR q.mots_cles LIKE :like_k{$i})";
            $params["like_q{$i}"] = "%{$token}%";
            $params["like_k{$i}"] = "%{$token}%";
        }

        $sql = "SELECT q.*, c.titre as categorie_titre
                FROM question_FAQ q
                JOIN categorie_FAQ c ON q.categorie_faq_id = c.id
                WHERE q.en_ligne = TRUE
                  AND (" . implode(' OR ', $wordConditions) . ")";

        if ($profil) {
            $sql .= " AND (q.profil_cible IS NULL OR q.profil_cible = :profil)";
            $params['profil'] = $profil;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $candidates = $stmt->fetchAll();
    }

    // Aucun candidat trouvé
    if (empty($candidates)) return null;

    // Étape 3 : calcul d'un score pour chaque candidat selon le ratio de tokens matchés
    $bestScore = 0;
    $bestMatch = null;

    foreach ($candidates as $row) {
        // On combine question + mots-clés pour l'évaluation
        $questionText = normalizeText($row['question']);
        $keywordsText = normalizeText($row['mots_cles'] ?? '');
        $combined = $questionText . ' ' . $questionText . ' ' . $keywordsText;

        // Compte combien de tokens apparaissent dans le texte combiné
        $matchCount = 0;
        foreach ($tokens as $token) {
            if (str_contains($combined, $token)) {
                $matchCount++;
            }
        }

        $ratio = $matchCount / max(count($tokens), 1);

        // Bonus de score si les tokens sont dans la question (pas seulement dans les mots-clés)
        $questionMatchCount = 0;
        foreach ($tokens as $token) {
            if (str_contains($questionText, $token)) {
                $questionMatchCount++;
            }
        }

        if ($matchCount > 0) {
            $ratio += ($questionMatchCount / max(count($tokens), 1)) * 0.3;
        }

        // On garde le meilleur score
        if ($ratio > $bestScore) {
            $bestScore = $ratio;
            $bestMatch = $row;
        }
    }

    // Seuil de pertinence : plus strict pour le fallback LIKE, plus souple pour FULLTEXT
    $threshold = $isFallback ? 0.7 : 0.3;
    if ($bestMatch === null || $bestScore < $threshold) {
        return null;
    }

    // Retourne les données formatées de la meilleure réponse trouvée
    return [
        'id' => (int)$bestMatch['id'],
        'question' => $bestMatch['question'],
        'reponse' => $bestMatch['reponse'],
        'categorie' => $bestMatch['categorie_titre'],
        'score' => round($bestScore, 2),
        'faq_url' => '/faq#' . $bestMatch['id'],
        'categorie_url' => '/faq#categorie-' . slugify($bestMatch['categorie_titre']),
    ];
}

// Convertit un texte en slug URL (ex: "Nos produits" -> "nos-produits")
function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'ô', 'ö', 'î', 'ï', 'ç'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'o', 'o', 'i', 'i', 'c'],
        $text
    );
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s_]+/', '-', $text);
    return trim($text, '-');
}

// Enregistre la question posée dans les logs pour analyse ultérieure
function logQuestion(PDO $pdo, string $questionBrute, ?int $matchedId, ?string $profil, ?string $pageUrl): void {
    $stmt = $pdo->prepare(
        "INSERT INTO chatbot_log (question_brute, matched_question_id, profil, page_url)
         VALUES (:question, :matched_id, :profil, :page)"
    );
    $stmt->execute([
        'question' => $questionBrute,
        'matched_id' => $matchedId,
        'profil' => $profil,
        'page' => $pageUrl,
    ]);
}

// ---- Traitement principal ----
// Recherche de la meilleure réponse
$result = findBestFaqAnswer($pdo, $message, $profil);
$matchedId = $result ? $result['id'] : null;

// On logue la question (qu'elle ait trouvé une réponse ou non)
logQuestion($pdo, $message, $matchedId, $profil, $pageUrl);

// Réponse JSON : succès ou échec avec suggestions
if ($result) {
    echo json_encode([
        'found' => true,
        'answer' => $result['reponse'],
        'question' => $result['question'],
        'categorie' => $result['categorie'],
        'faq_url' => $result['faq_url'],
        'suggestions' => [],
    ]);
} else {
    echo json_encode([
        'found' => false,
        'answer' => "Je n'ai pas encore la réponse à cette question. Vous pouvez :\n" .
                     "• Consulter notre FAQ complète : /faq\n" .
                     "• Nous contacter directement : /contact\n" .
                     "• Reformuler votre question autrement.",
        'question' => null,
        'categorie' => null,
        'faq_url' => '/faq',
        'suggestions' => [
            'Quels additifs utilisez-vous ?',
            'Où trouver vos produits ?',
            'Comment postuler ?',
        ],
    ]);
}
