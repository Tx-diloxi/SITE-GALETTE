<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../../app/config/database.php';
require_once __DIR__ . '/../../../../app/config/admin.php';
require_once __DIR__ . '/../../../../app/config/commercial.php';

$commercial = commercial_check_auth();

$success = false;
$error = '';

$livreurs = $pdo->query("SELECT id, nom, prenom, secteur FROM livreur WHERE en_ligne = TRUE ORDER BY nom, prenom")->fetchAll();
$questions = $pdo->query("SELECT id, categorie, question, ordre FROM inspection_question WHERE actif = TRUE ORDER BY ordre")->fetchAll();

$categories = [];
foreach ($questions as $q) {
    $categories[$q['categorie']][] = $q;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!commercial_csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide. Veuillez réessayer.';
    } else {
        $livreurId = (int) ($_POST['livreur_id'] ?? 0);
        $noteGenerale = !empty($_POST['note_generale']) ? (float) $_POST['note_generale'] : null;
        $commentaire = trim($_POST['commentaire'] ?? '');
        $reponses = $_POST['reponse'] ?? [];

        if ($livreurId <= 0) {
            $error = 'Veuillez sélectionner un livreur.';
        } elseif (empty($reponses)) {
            $error = 'Veuillez répondre aux questions.';
        } else {
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO inspection (livreur_id, commercial_id, note_generale, commentaire) VALUES (:livreur_id, :commercial_id, :note_generale, :commentaire)");
                $stmt->execute([
                    ':livreur_id' => $livreurId,
                    ':commercial_id' => $commercial['id'],
                    ':note_generale' => $noteGenerale,
                    ':commentaire' => $commentaire ?: null,
                ]);
                $inspectionId = (int) $pdo->lastInsertId();

                $stmtRep = $pdo->prepare("INSERT INTO inspection_reponse (inspection_id, question_id, note, commentaire) VALUES (:inspection_id, :question_id, :note, :commentaire)");
                foreach ($reponses as $questionId => $data) {
                    $note = isset($data['note']) ? min(5, max(1, (int) $data['note'])) : 1;
                    $cmt = trim($data['commentaire'] ?? '');
                    $stmtRep->execute([
                        ':inspection_id' => $inspectionId,
                        ':question_id' => (int) $questionId,
                        ':note' => $note,
                        ':commentaire' => $cmt ?: null,
                    ]);
                }

                $photosFiles = $_FILES['photos'] ?? null;
                $hasPhotos = $photosFiles && !empty($photosFiles['name'][0]) && $photosFiles['error'][0] === UPLOAD_ERR_OK;
                if ($hasPhotos) {
                    if (!commercial_ensure_upload_dir()) {
                        throw new RuntimeException("Dossier d'upload inaccessible.");
                    }
                    commercial_upload_photos([
                        'name' => $photosFiles['name'],
                        'type' => $photosFiles['type'],
                        'tmp_name' => $photosFiles['tmp_name'],
                        'error' => $photosFiles['error'],
                        'size' => $photosFiles['size'],
                    ], $inspectionId, $pdo);
                }

                $pdo->commit();
                header('Location: success.php');
                exit;
            } catch (PDOException | RuntimeException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('Erreur visite mystère : ' . $e->getMessage());
                $error = 'Une erreur est survenue lors de l\'enregistrement.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visite mystère – J'aime la Galette</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,900;1,400&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/page/admin/admin.css">
    <link rel="stylesheet" href="/assets/css/page/commercial/commercial.css">
</head>
<body class="commercial-body">
    <header class="commercial-header">
        <div class="commercial-header-inner">
            <div class="commercial-header-brand">
                <img src="/assets/images/logo_jaimelagalette.png" alt="J'aime la Galette" class="commercial-header-logo">
                <span class="commercial-header-title">Visite mystère</span>
            </div>
            <div class="commercial-header-right">
                <span class="commercial-header-user"><?= htmlspecialchars($commercial['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="/admin/logout.php" class="commercial-btn commercial-btn--outline">Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="commercial-main">
        <div class="commercial-container">
            <div class="commercial-form-header">
                <h1>Grille d'inspection – Visite mystère</h1>
                <p>Renseignez les informations ci-dessous pour effectuer une visite mystère.</p>
            </div>

            <?php if ($error): ?>
                <div class="commercial-alert commercial-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="commercial-form">
                <input type="hidden" name="csrf_token" value="<?= commercial_csrf_token() ?>">

                <section class="commercial-section">
                    <div class="commercial-field">
                        <label for="livreur_id" class="commercial-label">Livreur concerné</label>
                        <select name="livreur_id" id="livreur_id" class="commercial-select" required>
                            <option value="">Sélectionner un livreur…</option>
                            <?php foreach ($livreurs as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['prenom'] . ' ' . $l['nom'], ENT_QUOTES, 'UTF-8') ?><?= $l['secteur'] ? ' — ' . htmlspecialchars($l['secteur'], ENT_QUOTES, 'UTF-8') : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </section>

                <?php foreach ($categories as $categorie => $catQuestions): ?>
                <section class="commercial-section">
                    <h2 class="commercial-section-title"><?= htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php foreach ($catQuestions as $q): ?>
                    <div class="commercial-question">
                        <div class="commercial-question-header">
                            <span class="commercial-question-text"><?= htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="commercial-question-body">
                            <div class="commercial-stars" data-question-id="<?= $q['id'] ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label class="commercial-star-label">
                                    <input type="radio" name="reponse[<?= $q['id'] ?>][note]" value="<?= $i ?>" class="commercial-star-input">
                                    <span class="commercial-star" data-value="<?= $i ?>">★</span>
                                </label>
                                <?php endfor; ?>
                            </div>
                            <div class="commercial-question-comment">
                                <input type="text" name="reponse[<?= $q['id'] ?>][commentaire]" class="commercial-input" placeholder="Commentaire optionnel…" maxlength="500">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </section>
                <?php endforeach; ?>

                <section class="commercial-section">
                    <h2 class="commercial-section-title">Photos</h2>
                    <div class="commercial-field">
                        <label for="photos" class="commercial-label">Ajouter des photos (max 10)</label>
                        <div class="commercial-file-zone" id="fileDropZone">
                            <span class="commercial-file-icon">📷</span>
                            <span class="commercial-file-text">Cliquez ou glissez-déposez vos photos ici</span>
                            <span class="commercial-file-hint">JPEG, PNG ou WebP — 5 Mo max par photo — 10 photos max</span>
                            <input type="file" name="photos[]" id="photos" class="commercial-file-input" accept="image/jpeg,image/png,image/webp" multiple>
                        </div>
                        <div class="commercial-file-previews" id="filePreviews"></div>
                    </div>
                </section>

                <section class="commercial-section">
                    <h2 class="commercial-section-title">Appréciation générale</h2>
                    <div class="commercial-form-row">
                        <div class="commercial-field commercial-field--small">
                            <label for="note_generale" class="commercial-label">Note globale /20</label>
                            <input type="number" name="note_generale" id="note_generale" class="commercial-input commercial-input--number" min="0" max="20" step="0.5" placeholder="/20">
                        </div>
                    </div>
                    <div class="commercial-field">
                        <label for="commentaire" class="commercial-label">Commentaire général</label>
                        <textarea name="commentaire" id="commentaire" class="commercial-textarea" rows="4" placeholder="Observations, remarques, points d'attention…" maxlength="2000"></textarea>
                    </div>
                </section>

                <section class="commercial-section commercial-section--footer">
                    <div class="commercial-field commercial-field--readonly">
                        <label class="commercial-label">Commercial</label>
                        <p class="commercial-readonly-value"><?= htmlspecialchars($commercial['nom'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="commercial-form-actions">
                        <button type="submit" class="commercial-btn commercial-btn--primary">
                            <span class="commercial-btn-icon">💾</span> Enregistrer la visite
                        </button>
                    </div>
                </section>
            </form>
        </div>
    </main>

    <script>
    document.querySelectorAll('.commercial-stars').forEach(function(container) {
        var stars = container.querySelectorAll('.commercial-star');
        var inputs = container.querySelectorAll('.commercial-star-input');

        stars.forEach(function(star) {
            star.addEventListener('mouseenter', function() {
                var val = parseInt(this.getAttribute('data-value'));
                stars.forEach(function(s, idx) {
                    s.classList.toggle('commercial-star--hover', idx < val);
                });
            });

            star.addEventListener('mouseleave', function() {
                stars.forEach(function(s) {
                    s.classList.remove('commercial-star--hover');
                });
            });

            star.addEventListener('click', function() {
                var val = parseInt(this.getAttribute('data-value'));
                inputs.forEach(function(inp) {
                    inp.checked = parseInt(inp.value) === val;
                });
                stars.forEach(function(s, idx) {
                    s.classList.toggle('commercial-star--active', idx < val);
                });
            });
        });
    });

    var dropZone = document.getElementById('fileDropZone');
    var fileInput = document.getElementById('photos');
    var previews = document.getElementById('filePreviews');

    dropZone.addEventListener('click', function() { fileInput.click(); });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('commercial-file-zone--dragover');
    });

    dropZone.addEventListener('dragleave', function() {
        dropZone.classList.remove('commercial-file-zone--dragover');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('commercial-file-zone--dragover');
        if (e.dataTransfer.files) {
            fileInput.files = e.dataTransfer.files;
            updateFilePreviews();
        }
    });

    fileInput.addEventListener('change', updateFilePreviews);

    function updateFilePreviews() {
        previews.innerHTML = '';
        var files = fileInput.files;
        if (files.length > 10) {
            alert('Maximum 10 photos autorisées.');
            fileInput.value = '';
            return;
        }
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (!file.type.match('image.*')) continue;
            var reader = new FileReader();
            reader.onload = function(e) {
                var div = document.createElement('div');
                div.className = 'commercial-file-preview';
                div.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu"><span class="commercial-file-preview-remove" data-idx="' + i + '">×</span>';
                previews.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    }

    previews.addEventListener('click', function(e) {
        if (e.target.classList.contains('commercial-file-preview-remove')) {
            e.target.parentElement.remove();
        }
    });
    </script>
</body>
</html>
