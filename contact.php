<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer laden
require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

// Config laden (Passwort ausgelagert)
$config = require __DIR__ . '/config.php';

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kontakt.html');
    exit;
}

// Honeypot (Spam-Schutz)
if (!empty($_POST['website'] ?? '')) {
    header('Location: contact-success.html');
    exit;
}

// Daten holen
$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

// Validierung
if ($name === '' || $email === '' || $message === '') {
    header('Location: kontakt.html?status=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: kontakt.html?status=email');
    exit;
}

// Mail vorbereiten
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_user'];
    $mail->Password = $config['smtp_pass'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $config['smtp_port'];
    $mail->CharSet = 'UTF-8';

    // Absender
    $mail->setFrom($config['smtp_user'], 'Rühl & Rein Website');

    // Empfänger
    $mail->addAddress($config['smtp_user'], 'Rühl & Rein');

    // Antwortadresse (wichtig!)
    $mail->addReplyTo($email, $name);

    // Betreff
    $mail->Subject = 'Neue Anfrage über ruehl-rein.com';

    // Inhalt
    $body  = "Neue Anfrage über das Kontaktformular\n\n";
    $body .= "Name: {$name}\n";
    $body .= "E-Mail: {$email}\n";
    $body .= "Telefon: {$phone}\n\n";
    $body .= "Nachricht:\n{$message}\n";

    $mail->Body = $body;
    $mail->isHTML(false);

    // Senden
    $mail->send();

    // Erfolg
    header('Location: contact-success.html');
    exit;

} catch (Exception $e) {
    // Fehler (optional: loggen)
    header('Location: kontakt.html?status=error');
    exit;
}