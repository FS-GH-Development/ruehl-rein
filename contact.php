<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kontakt.html');
    exit;
}

// Honeypot-Spamschutz
if (!empty($_POST['website'] ?? '')) {
    header('Location: contact-success.html');
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    header('Location: kontakt.html?status=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: kontakt.html?status=email');
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kontakt@ruehl-rein.com';
    $mail->Password = 'HIER_DEIN_MAIL_PASSWORT_EINTRAGEN';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('kontakt@ruehl-rein.com', 'Rühl & Rein Website');
    $mail->addAddress('kontakt@ruehl-rein.com', 'Rühl & Rein');
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'Neue Anfrage über ruehl-rein.com';

    $body  = "Neue Anfrage über das Kontaktformular\n\n";
    $body .= "Name: {$name}\n";
    $body .= "E-Mail: {$email}\n";
    $body .= "Telefon: {$phone}\n\n";
    $body .= "Nachricht:\n{$message}\n";

    $mail->Body = $body;
    $mail->isHTML(false);

    $mail->send();

    header('Location: contact-success.html');
    exit;
} catch (Exception $e) {
    header('Location: kontakt.html?status=error');
    exit;
}