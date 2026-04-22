<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

$config = require dirname(__DIR__) . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kontakt.html');
    exit;
}

// Honeypot
if (!empty($_POST['website'] ?? '')) {
    header('Location: kontakt.html?status=success');
    exit;
}

// Mindestzeit gegen Bots
$formTime = (int)($_POST['form_time'] ?? 0);
if ($formTime > 0 && (time() - $formTime) < 3) {
    header('Location: kontakt.html?status=spam');
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$location = trim((string)($_POST['location'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    header('Location: kontakt.html?status=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: kontakt.html?status=email');
    exit;
}

if (
    mb_strlen($name) > 120 ||
    mb_strlen($email) > 190 ||
    mb_strlen($phone) > 60 ||
    mb_strlen($service) > 120 ||
    mb_strlen($location) > 160 ||
    mb_strlen($message) > 5000
) {
    header('Location: kontakt.html?status=toolong');
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_user'];
    $mail->Password = $config['smtp_pass'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = (int)$config['smtp_port'];
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($config['smtp_user'], 'Rühl & Rein Website');
    $mail->addAddress($config['smtp_user'], 'Rühl & Rein');
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'Neue Anfrage über ruehl-rein.com';

    $body  = "Neue Anfrage über das Kontaktformular\n\n";
    $body .= "Name: {$name}\n";
    $body .= "E-Mail: {$email}\n";
    $body .= "Telefon: {$phone}\n\n";
    $body .= "Gewünschte Leistung: {$service}\n";
    $body .= "Objektort: {$location}\n\n";
    $body .= "Nachricht:\n{$message}\n";

    $mail->Body = $body;
    $mail->isHTML(false);

    $mail->send();

    header('Location: kontakt.html?status=success');
    exit;
} catch (Exception $e) {
    error_log('Kontaktformular Fehler: ' . $mail->ErrorInfo);
    header('Location: kontakt.html?status=error');
    exit;
}
