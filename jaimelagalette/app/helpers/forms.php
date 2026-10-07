<?php
declare(strict_types=1);

require_once HELPERS . 'mailer.php';

// Vérifie que l'utilisateur ne soumet pas trop de formulaires (prévention spam)
function check_submission_rate(): bool {
    $now = time();
    $window = 60; // fenêtre de 60 secondes
    $max = 3;     // maximum 3 soumissions par fenêtre
    $timestamps = $_SESSION['form_submit_timestamps'] ?? [];
    // Nettoie les timestamps hors fenêtre
    $timestamps = array_values(array_filter($timestamps, fn($t) => $t > $now - $window));
    if (count($timestamps) >= $max) {
        return false; // trop de soumissions
    }
    $timestamps[] = $now;
    $_SESSION['form_submit_timestamps'] = $timestamps;
    return true;
}

function handleContactForm(PDO $pdo): array
{
    $csrfToken = $_POST['csrf_token'] ?? '';
    if ($csrfToken === '' || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        return ['success' => false, 'errors' => ['global' => 'Session invalide. Veuillez recharger la page.']];
    }

    // Vérification du taux de soumission (anti-spam)
    if (!check_submission_rate()) {
        return ['success' => false, 'errors' => ['global' => 'Trop de soumissions. Veuillez patienter.']];
    }

    $errors = [];

    $profil   = $_POST['form_profil'] ?? '';
    $nom      = trim($_POST['nom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $message  = trim($_POST['message'] ?? '');
    $rgpd     = isset($_POST['rgpd']) ? 1 : 0;

    if (!in_array($profil, ['b2c', 'b2b'], true)) {
        $errors['profil'] = 'Profil invalide.';
    }

    if ($nom === '' || mb_strlen($nom) > 100) {
        $errors['nom'] = 'Le nom est obligatoire.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    }

    if ($message === '' || mb_strlen($message) > 2000) {
        $errors['message'] = 'Le message est obligatoire.';
    }

    if (!$rgpd) {
        $errors['rgpd'] = 'Vous devez accepter la politique de confidentialité.';
    }

    $societe   = null;
    $telephone = null;
    $profilB2b = null;
    $sujet     = '';

    // Validation spécifique selon le profil
    if ($profil === 'b2c') {
        // Validation du sujet pour le grand public
        $sujet = trim($_POST['sujet'] ?? '');
        if ($sujet === '' || mb_strlen($sujet) > 200) {
            $errors['sujet'] = 'L\'objet est obligatoire.';
        }
    } elseif ($profil === 'b2b') {
        $societe   = trim($_POST['societe'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $profilB2b = trim($_POST['profil_b2b'] ?? '');
        $sujet     = trim($_POST['sujet_b2b'] ?? '');

        if ($societe === '' || mb_strlen($societe) > 100) {
            $errors['societe'] = 'Le nom de la société est obligatoire.';
        }
        if ($profilB2b === '') {
            $errors['profil_b2b'] = 'Veuillez sélectionner votre secteur.';
        }
        if ($sujet === '' || mb_strlen($sujet) > 200) {
            $errors['sujet_b2b'] = 'L\'objet est obligatoire.';
        }
    }

    if ($errors !== []) {
        return ['success' => false, 'errors' => $errors];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO formulaire_contact
                (profil, nom, societe, email, telephone, profil_b2b, sujet, message, rgpd_accepte)
            VALUES
                (:profil, :nom, :societe, :email, :telephone, :profil_b2b, :sujet, :message, :rgpd)
        ");
        $stmt->execute([
            ':profil'     => $profil,
            ':nom'        => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            ':societe'    => $societe !== null ? htmlspecialchars($societe, ENT_QUOTES, 'UTF-8') : null,
            ':email'      => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            ':telephone'  => $telephone !== null ? htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8') : null,
            ':profil_b2b' => $profilB2b !== null ? htmlspecialchars($profilB2b, ENT_QUOTES, 'UTF-8') : null,
            ':sujet'      => htmlspecialchars($sujet, ENT_QUOTES, 'UTF-8'),
            ':message'    => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            ':rgpd'       => $rgpd,
        ]);
    } catch (PDOException $e) {
        error_log('Contact form insert error: ' . $e->getMessage());
        return ['success' => false, 'errors' => ['global' => 'Erreur lors de l\'envoi. Veuillez réessayer.']];
    }

    // Prépare les informations pour l'email
    $profilLabel = ($profil === 'b2b') ? 'Professionnel' : 'Grand public';
    $infosSociete = '';
    if ($profil === 'b2b' && $societe !== null) {
        $infosSociete = "<tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Société</td><td>" . htmlspecialchars($societe, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        if ($telephone !== '') {
            $infosSociete .= "<tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Téléphone</td><td>" . htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        }
        $infosSociete .= "<tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Secteur</td><td>" . htmlspecialchars($profilB2b, ENT_QUOTES, 'UTF-8') . "</td></tr>";
    }

    // Corps HTML de l'email
    $htmlBody = "
    <html><body>
    <table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;width:100%;max-width:600px;font-family:sans-serif;'>
        <tr><td colspan='2' style='background:#EE7325;color:#ffffff;font-weight:bold;text-align:center;font-size:1.1em;'>Nouveau message – " . htmlspecialchars($sujet, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;width:120px;'>Profil</td><td>{$profilLabel}</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Nom</td><td>" . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Email</td><td>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</td></tr>
        {$infosSociete}
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Sujet</td><td>" . htmlspecialchars($sujet, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Message</td><td>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</td></tr>
    </table>
    </body></html>";

    // Corps texte brut de l'email
    $altBody = "Nouveau message – {$sujet}\n"
        . "Profil : {$profilLabel}\n"
        . "Nom : {$nom}\n"
        . "Email : {$email}\n"
        . ($societe !== null ? "Société : {$societe}\n" : '')
        . ($telephone !== '' ? "Téléphone : {$telephone}\n" : '')
        . "Sujet : {$sujet}\n"
        . "Message : {$message}";

    // Détermine le destinataire : SAV vers qualite@, sinon adresse par défaut
    $toEmail = ($profil === 'b2c' && $sujet === 'sav')
        ? 'qualite@jaimelagalette.com'
        : MAIL_TO;

    // Envoie l'email
    $mailSent = sendMail(
        "Nouveau message – {$sujet}",
        $htmlBody,
        $altBody,
        $email,
        $nom,
        $toEmail
    );

    if (!$mailSent) {
        error_log('Contact form: email sending failed for ' . $email);
        return ['success' => false, 'errors' => ['mail' => 'Erreur lors de l\'envoi de l\'email.']];
    }

    return ['success' => true];
}

// Traite le formulaire de candidature (recrutement)
function handleApplicationForm(PDO $pdo): array
{
    $csrfToken = $_POST['csrf_token'] ?? '';
    if ($csrfToken === '' || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        return ['success' => false, 'errors' => ['global' => 'Session invalide. Veuillez recharger la page.']];
    }

    // Vérification du taux de soumission (anti-spam)
    if (!check_submission_rate()) {
        return ['success' => false, 'errors' => ['global' => 'Trop de soumissions. Veuillez patienter.']];
    }

    $errors = [];

    $prenom  = trim($_POST['prenom'] ?? '');
    $nom     = trim($_POST['nom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $rgpd    = isset($_POST['rgpd']) ? 1 : 0;
    $typeCandidature = trim($_POST['type_candidature'] ?? '');
    $offreEmploiId = null;
    $poste = '';
    if (str_starts_with($typeCandidature, 'offre_')) {
        $offreEmploiId = (int) substr($typeCandidature, 6);
        if ($offreEmploiId <= 0) {
            $errors['type_candidature'] = 'Offre invalide.';
        } else {
            // Récupère le titre de l'offre depuis la base
            $stmtOffre = $pdo->prepare("SELECT titre FROM offre_Emploi WHERE id = ?");
            $stmtOffre->execute([$offreEmploiId]);
            $offre = $stmtOffre->fetch();
            if (!$offre) {
                $errors['type_candidature'] = 'Cette offre n\'existe plus.';
            } else {
                $poste = $offre['titre'];
            }
        }
    } elseif ($typeCandidature === 'spontanee') {
        $poste = trim($_POST['poste'] ?? '');
        $siteId = (int)($_POST['site_id'] ?? 0);
        if ($siteId <= 0) {
            $errors['site_id'] = 'Veuillez sélectionner un site.';
        }
    } else {
        $errors['type_candidature'] = 'Veuillez sélectionner une offre ou choisir candidature spontanée.';
    }

    if ($prenom === '' || mb_strlen($prenom) > 100) {
        $errors['prenom'] = 'Le prénom est obligatoire.';
    }

    if ($nom === '' || mb_strlen($nom) > 100) {
        $errors['nom'] = 'Le nom est obligatoire.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    }

    if ($typeCandidature === 'spontanee' && ($poste === '' || mb_strlen($poste) > 200)) {
        $errors['poste'] = 'Le poste souhaité est obligatoire.';
    }

    if ($message === '' || mb_strlen($message) > 2000) {
        $errors['message'] = 'Le message est obligatoire.';
    }

    if (!$rgpd) {
        $errors['rgpd'] = 'Vous devez accepter le traitement de vos données.';
    }

    $cvPath = null;

    if (!empty($_FILES['cv']['name'])) {
        $file = $_FILES['cv'];
        $allowedMime = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['cv'] = 'Erreur lors de l\'upload du fichier.';
        } elseif ($file['size'] > (defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 5 * 1024 * 1024)) {
            $errors['cv'] = 'Le fichier est trop volumineux (max 5 Mo).';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedMime, true)) {
                $errors['cv'] = 'Format de fichier non accepté (PDF, DOC, DOCX uniquement).';
            }
        }

        if (empty($errors['cv'])) {
            $uploadDir = APP_ROOT . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = match ($mime) {
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                default => null,
            };
            if (!$ext) {
                $errors['cv'] = 'Format de fichier non accepté.';
            }
        }

        if (empty($errors['cv'])) {
            $cvPath = uniqid('cv_', true) . '.' . $ext;

            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $cvPath)) {
                $errors['cv'] = 'Erreur lors de l\'enregistrement du fichier.';
                $cvPath = null;
            }
        }
    }

    if ($errors !== []) {
        return ['success' => false, 'errors' => $errors];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO applications
                (prenom, nom, email, poste_souhaite, type_candidature, offre_emploi_id, message, cv_path, rgpd_accepte)
            VALUES
                (:prenom, :nom, :email, :poste, :type_candidature, :offre_emploi_id, :message, :cv_path, :rgpd)
        ");
        $stmt->execute([
            ':prenom'          => htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'),
            ':nom'             => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            ':email'           => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            ':poste'           => htmlspecialchars($poste, ENT_QUOTES, 'UTF-8'),
            ':type_candidature' => $typeCandidature,
            ':offre_emploi_id' => $offreEmploiId,
            ':message'         => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            ':cv_path'         => $cvPath,
            ':rgpd'            => $rgpd,
        ]);
    } catch (PDOException $e) {
        error_log('Application form insert error: ' . $e->getMessage());
        // Nettoie le fichier uploadé en cas d'erreur
        if ($cvPath !== null) {
            $uploadDir = APP_ROOT . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR;
            @unlink($uploadDir . $cvPath);
        }
        return ['success' => false, 'errors' => ['global' => 'Erreur lors de l\'envoi. Veuillez réessayer.']];
    }

    // Détermine l'adresse email destinataire (RH du site concerné ou fallback)
    $toEmail = MAIL_TO;
    if ($offreEmploiId !== null) {
        $stmtSite = $pdo->prepare("
            SELECT pc.email_rh FROM offre_Emploi oe
            JOIN point_Carte pc ON oe.point_carte_id = pc.id
            WHERE oe.id = ?
        ");
        $stmtSite->execute([$offreEmploiId]);
        $siteEmail = $stmtSite->fetchColumn();
        if ($siteEmail) {
            $toEmail = $siteEmail;
        }
    } elseif (!empty($siteId)) {
        $stmtSite = $pdo->prepare("SELECT email_rh FROM point_Carte WHERE id = ?");
        $stmtSite->execute([$siteId]);
        $siteEmail = $stmtSite->fetchColumn();
        if ($siteEmail) {
            $toEmail = $siteEmail;
        }
    }

    // Prépare les informations CV pour l'email
    $cvLinkHtml = '';
    $cvLinkText = '';
    if ($cvPath !== null) {
        $cvUrl = '/assets/uploads/cv/' . rawurlencode($cvPath);
        $cvLinkHtml = "<tr><td><strong>CV</strong></td><td><a href='{$cvUrl}'>Télécharger le CV</a></td></tr>";
        $cvLinkText = "\nCV : " . BASE_URL . "assets/uploads/cv/{$cvPath}";
    }

    $emailType = ($offreEmploiId !== null) ? "Candidature – {$poste}" : "Candidature spontanée";

    // Corps HTML de l'email
    $htmlBody = "
    <html><body>
    <table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;width:100%;max-width:600px;font-family:sans-serif;'>
        <tr><td colspan='2' style='background:#EE7325;color:#ffffff;font-weight:bold;text-align:center;font-size:1.1em;'>{$emailType} – {$prenom} {$nom}</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;width:120px;'>Nom</td><td>" . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Prénom</td><td>" . htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Email</td><td>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Poste souhaité</td><td>" . htmlspecialchars($poste, ENT_QUOTES, 'UTF-8') . "</td></tr>
        <tr><td style='background:#111111;color:#ffffff;font-weight:bold;'>Message</td><td>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</td></tr>
        {$cvLinkHtml}
    </table>
    </body></html>";

    // Corps texte brut de l'email
    $altBody = "{$emailType} – {$prenom} {$nom}\n"
        . "Nom : {$nom}\n"
        . "Prénom : {$prenom}\n"
        . "Email : {$email}\n"
        . "Poste souhaité : {$poste}\n"
        . "Message : {$message}"
        . $cvLinkText;

    // Chemin complet du CV pour pièce jointe
    $cvFullPath = '';
    if ($cvPath !== null) {
        $uploadDir = APP_ROOT . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR;
        $cvFullPath = $uploadDir . $cvPath;
    }

    // Envoie l'email (non bloquant – la candidature est déjà en BDD)
    try {
        sendMail(
            "{$emailType} – {$prenom} {$nom}",
            $htmlBody,
            $altBody,
            $email,
            $prenom . ' ' . $nom,
            $toEmail,
            $cvFullPath
        );
    } catch (\Throwable $e) {
        error_log('Application form: email sending failed (non-blocking): ' . $e->getMessage());
    }

    return ['success' => true];
}