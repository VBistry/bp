<?php

include 'frameworks/PHPMailer/src/PHPMailer.php';
include 'frameworks/PHPMailer/src/SMTP.php';
include 'frameworks/PHPMailer/src/Exception.php';

function setFlash(string $type, string $message): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function generateVerificationUrl(int $userId, string $email): string
{
    $expires = time() + (60 * 60); // 1 hodina
    $hash = sha1($email);             // hash emailu zabrání, aby jeden odkaz fungoval pro více uživatelů

    $url = "auth/verify/{$userId}/{$hash}?expires={$expires}";

    // HMAC podpis — zabrání úpravě jakéhokoliv parametru v URL
    $signature = hash_hmac('sha256', $url, APP_SECRET_KEY);

    // vysledan URL pro jednoho uzivatele 
    return $url . "&signature={$signature}";
}

function verifySignedUrl(int $userId, string $hash, int $expires, string $signature): bool
{
    // 1. zkontroluj expiraci
    if (time() > $expires) {
        return false;
    }

    // 2. znovu sestav URL bez signature
    $url = "auth/verify/{$userId}/{$hash}?expires={$expires}";

    // 3. vypočítej očekávaný podpis
    $expectedSignature = hash_hmac('sha256', $url, APP_SECRET_KEY);

    // 4. porovnej pomocí hash_equals (bezpečné porovnání)
    return hash_equals($expectedSignature, $signature);
}

function sendVerificationEmail(string $toEmail, string $toName, string $verificationUrl): void
{
    // načtení PHPMailer tříd
    //equire_once __DIR__ . '/frameworks/PHPMailer/src/PHPMailer.php';
    //require_once __DIR__ . '/frameworks/PHPMailer/src/SMTP.php';
    //require_once __DIR__ . '/frameworks/PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP nastavení z konstant (.env)
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->Port = MAIL_PORT;

        // odesílatel a příjemce
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // obsah emailu
        $mail->isHTML(true);
        $mail->Subject = 'Please verify your email address';
        $mail->Body = "
            <h2>Hello {$toName}!</h2>
            <p>Please click the button below to verify your email address:</p>
            <a href='{$verificationUrl}' 
               style='background:#198754; color:white; padding:12px 24px; 
                      border-radius:4px; text-decoration:none; font-weight:bold;'>
                Verify Email
            </a>
            <p>This link will expire in <strong>1 hour</strong>.</p>
            <p>If you did not create an account, ignore this email.</p>
        ";
        $mail->AltBody = "Hello {$toName},\n\nVerify your email: {$verificationUrl}\n\nExpires in 1 hour.";

        $mail->send();

    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
    }
}
function generatePasswordResetUrl(PDO $pdo, int $userId, string $email): string
{
    $token   = bin2hex(random_bytes(32)); // náhodný token
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // uložíme token do DB
    savePasswordResetToken($pdo, $userId, $token, $expires);

    // sestavíme URL s tokenem
    return "auth/reset-password?token={$token}";
}

function sendPasswordResetEmail(string $toEmail, string $toName, string $resetUrl): void{

    // načtení PHPMailer tříd
    //equire_once __DIR__ . '/frameworks/PHPMailer/src/PHPMailer.php';
    //require_once __DIR__ . '/frameworks/PHPMailer/src/SMTP.php';
    //require_once __DIR__ . '/frameworks/PHPMailer/src/Exception.php';


    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP nastavení z konstant (.env)
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->Port = MAIL_PORT;

        // odesílatel a příjemce
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // obsah emailu
        $mail->isHTML(true);
        $mail->Subject = 'Here is your password reset link';
        $mail->Body = "
            <h2>Hello {$toName}!</h2>
            <p>Please click the button below to reset your password:</p>
            <a href='{$resetUrl}' 
               style='background:#198754; color:white; padding:12px 24px; 
                      border-radius:4px; text-decoration:none; font-weight:bold;'>
                Reset Password
            </a>
            <p>This link will expire in <strong>1 hour</strong>.</p>
            <p>If you did not create an account, ignore this email.</p>
        ";
        $mail->AltBody = "Hello {$toName},\n\nReset your password: {$resetUrl}\n\nExpires in 1 hour.";

        $mail->send();

    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
    }
    


}

?>