<?php

include 'frameworks/PHPMailer/src/PHPMailer.php';
include 'frameworks/PHPMailer/src/SMTP.php';
include 'frameworks/PHPMailer/src/Exception.php';

// Set a flash message(to Session) to be displayed on the next page load
function setFlash(string $type, string $message): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function generateVerificationUrl(int $userId, string $email): string
{
    $expires = time() + (60 * 60); // 1 hour expiration
    $hash = sha1($email);  // prevention against one link working for multiple users

    $url = "auth/verify/{$userId}/{$hash}?expires={$expires}";

    // prevents modification of any parameter in the URL
    $signature = hash_hmac('sha256', $url, APP_SECRET_KEY);

    return $url . "&signature={$signature}";
}

function verifySignedUrl(int $userId, string $hash, int $expires, string $signature): bool
{
    // expiration check
    if (time() > $expires) {
        return false;
    }

    // reconstruct URL
    $url = "auth/verify/{$userId}/{$hash}?expires={$expires}";

    $expectedSignature = hash_hmac('sha256', $url, APP_SECRET_KEY);

    return $expectedSignature === $signature;
}

function sendVerificationEmail(string $toEmail, string $toName, string $verificationUrl): void
{

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP settings from constants (.env)
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->Port = MAIL_PORT;

        // sender and recipient
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // email content
        $mail->isHTML(true);
        $mail->Subject = 'Please verify your email address';
        $mail->Body = "
            <h1>Hello {$toName}!</h1>
            <p>Please click the button below to verify your email address:</p>
            <a href='{$verificationUrl}'
            style='background:#198754; color:white; padding:12px 24px;
                    border-radius:4px; text-decoration:none; font-weight:bold;'>
                Verify Email
            </a>
            <p>This link will expire in <strong>1 hour</strong>.</p>
            <p>If you did not create an account, ignore this email.</p>
        ";
        $mail->send();

    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
    }
}
function generatePasswordResetUrl(PDO $pdo, int $userId, string $email): string
{
    $token   = bin2hex(random_bytes(32)); // random token
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // save token to DB
    savePasswordResetToken($pdo, $userId, $token, $expires);

    return "auth/reset-password?token={$token}";
}

function sendPasswordResetEmail(string $toEmail, string $toName, string $resetUrl): void{

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP settings from constants (.env)
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->Port = MAIL_PORT;

        // sender and recipient
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // email content
        $mail->isHTML(true);
        $mail->Subject = 'Here is your password reset link';
        $mail->Body = "
            <h1>Hello {$toName}!</h1>
            <p>Please click the button below to reset your password:</p>
            <a href='{$resetUrl}'
            style='background:#198754; color:white; padding:12px 24px;
                    border-radius:4px; text-decoration:none; font-weight:bold;'>
                Reset Password
            </a>
            <p>This link will expire in <strong>1 hour</strong>.</p>
            <p>If you did not request a password reset, ignore this email.</p>
        ";
        $mail->send();

    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
    }
    

}

?>