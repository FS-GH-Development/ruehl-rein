<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require dirname(__DIR__) . '/lib/phpmailer/src/Exception.php';
require dirname(__DIR__) . '/lib/phpmailer/src/PHPMailer.php';
require dirname(__DIR__) . '/lib/phpmailer/src/SMTP.php';

$config = require dirname(__DIR__) . '/config.php';

function redirectWithStatus(string $status)
{
    header('Location: kontakt.html?status=' . rawurlencode($status));
    exit;
}

function sanitizeSingleLine(string $value): string
{
    return preg_replace('/[\r\n]+/', ' ', trim($value)) ?? '';
}

function hasInvalidRequestOrigin(): bool
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host === '') {
        return false;
    }
    $host = strtolower((string)preg_replace('/:\d+$/', '', $host));

    foreach (['HTTP_ORIGIN', 'HTTP_REFERER'] as $header) {
        $value = $_SERVER[$header] ?? '';
        if ($value === '') {
            continue;
        }

        $requestHost = parse_url($value, PHP_URL_HOST);
        if (!is_string($requestHost) || strcasecmp($requestHost, $host) !== 0) {
            return true;
        }
    }

    return false;
}

function exceedsRateLimit(): bool
{
    $windowSeconds = 3600;
    $maxRequests = 5;
    $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = hash('sha256', 'ruehl-rein-contact-form|' . $remoteAddress);
    $file = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'ruehl_rein_contact_' . $key . '.json';
    $now = time();
    $state = ['first' => $now, 'count' => 0];

    if (is_file($file)) {
        $stored = json_decode((string)file_get_contents($file), true);
        if (is_array($stored) && isset($stored['first'], $stored['count'])) {
            $state = [
                'first' => (int)$stored['first'],
                'count' => (int)$stored['count'],
            ];
        }
    }

    if (($now - $state['first']) > $windowSeconds) {
        $state = ['first' => $now, 'count' => 0];
    }

    $state['count']++;
    file_put_contents($file, json_encode($state), LOCK_EX);

    return $state['count'] > $maxRequests;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kontakt.html');
    exit;
}

if (hasInvalidRequestOrigin()) {
    redirectWithStatus('spam');
}

if (((int)($_SERVER['CONTENT_LENGTH'] ?? 0)) > 20000) {
    redirectWithStatus('toolong');
}

if (exceedsRateLimit()) {
    redirectWithStatus('spam');
}

// Honeypot
if (!empty($_POST['website'] ?? '')) {
    header('Location: kontakt.html?status=success');
    exit;
}

// Mindestzeit gegen Bots
$formTime = (int)($_POST['form_time'] ?? 0);
if ($formTime > 0 && (time() - $formTime) < 3) {
    redirectWithStatus('spam');
}

$name = sanitizeSingleLine((string)($_POST['name'] ?? ''));
$email = sanitizeSingleLine((string)($_POST['email'] ?? ''));
$phone = sanitizeSingleLine((string)($_POST['phone'] ?? ''));
$service = sanitizeSingleLine((string)($_POST['service'] ?? ''));
$location = sanitizeSingleLine((string)($_POST['location'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    redirectWithStatus('missing');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithStatus('email');
}

if (
    mb_strlen($name) > 120 ||
    mb_strlen($email) > 190 ||
    mb_strlen($phone) > 60 ||
    mb_strlen($service) > 120 ||
    mb_strlen($location) > 160 ||
    mb_strlen($message) > 5000
) {
    redirectWithStatus('toolong');
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

    redirectWithStatus('success');
} catch (Exception $e) {
    error_log('Kontaktformular Fehler: ' . $mail->ErrorInfo);
    redirectWithStatus('error');
}
