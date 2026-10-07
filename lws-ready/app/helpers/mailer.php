<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// Inclut l'autoload Composer pour PHPMailer
require_once APP_ROOT . 'vendor/autoload.php';
// Inclut la configuration SMTP du site
require_once APP_ROOT . 'app/config/mail.php';

// Importe les classes PHPMailer nécessaires
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Envoie un email via SMTP avec PHPMailer en gérant les pièces jointes
function sendMail(string $subject, string $htmlBody, string $altBody, string $replyTo = '', string $replyToName = '', string $to = '', string $attachmentPath = ''): bool
{
    // Crée une nouvelle instance de PHPMailer avec exceptions activées
    $mail = new PHPMailer(true);

    try {
        // Configure le serveur SMTP pour l'envoi
        $mail->isSMTP();
        // Définit l'hôte du serveur SMTP sortant
        $mail->Host       = MAIL_HOST;
        // Active l'authentification SMTP
        $mail->SMTPAuth   = true;
        // Nom d'utilisateur SMTP
        $mail->Username   = MAIL_USERNAME;
        // Mot de passe SMTP
        $mail->Password   = MAIL_PASSWORD;
        // Cryptage STARTTLS pour la sécurité de la connexion
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // Port SMTP standard avec TLS
        $mail->Port       = MAIL_PORT;
        // Timeout de connexion fixé à 10 secondes
        $mail->Timeout    = 10;

        // Configure le mode debug (désactivé en production)
        $mail->SMTPDebug = MAIL_DEBUG === 0 ? SMTP::DEBUG_OFF : MAIL_DEBUG;

        // Définit l'expéditeur du message
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        // Ajoute le destinataire principal (fallback vers MAIL_TO si non spécifié)
        $mail->addAddress($to !== '' ? $to : MAIL_TO);

        // Ajoute une adresse de réponse si fournie
        if ($replyTo !== '') {
            $mail->addReplyTo($replyTo, $replyToName);
        }

        // Ajoute une pièce jointe si le chemin est valide
        if ($attachmentPath !== '' && file_exists($attachmentPath)) {
            $mail->addAttachment($attachmentPath);
        }

        // Configure le jeu de caractères en UTF-8
        $mail->CharSet  = 'UTF-8';
        // Active le format HTML pour le corps du message
        $mail->isHTML(true);
        // Définit le sujet de l'email
        $mail->Subject = $subject;
        // Définit le corps HTML de l'email
        $mail->Body    = $htmlBody;
        // Définit le corps texte brut alternatif
        $mail->AltBody = $altBody;

        // Envoie l'email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Logge l'erreur d'envoi
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
