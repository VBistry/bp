<?php

// Find user by email - used for login and duplicate check
function findUserByEmail(PDO $pdo, string $email)
{
    $stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE email = ?
    AND deleted_at IS NULL");
    $stmt->execute([trim($email)]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Register a new user
function registerUser(
    PDO $pdo,
    string $firstName,
    string $surname,
    string $email,
    string $hashedPasswd,
    int $birthYear,
    string $tel,
    string $role,
    string $description_user,
    string $gender
) {
    $stmt = $pdo->prepare("
        INSERT INTO users 
            (first_name, surname, email, passwd, birth_year, tel, role, description_user, gender) 
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $firstName,
        $surname,
        $email,
        $hashedPasswd,
        $birthYear,
        $tel,
        $role,
        $description_user,
        $gender
    ]);
}

function verifyUserEmail(PDO $pdo, int $userId): bool
{
    $stmt = $pdo->prepare("
        UPDATE users 
        SET verified_email = true, 
            email_verified_at = NOW() 
        WHERE id = ?
    ");
    return $stmt->execute([$userId]);
}

// Save password reset token to DB
function savePasswordResetToken(PDO $pdo, int $userId, string $token, string $expires): bool
{
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password_reset_token = ?,
            password_reset_expires_at = ?
        WHERE id = ?
    ");
    return $stmt->execute([$token, $expires, $userId]);
}

// Find user by reset token
function getUserByResetToken(PDO $pdo, string $token)
{
    $stmt = $pdo->prepare("
        SELECT * FROM users 
        WHERE password_reset_token = ? 
        AND password_reset_expires_at > NOW()
        AND deleted_at IS NULL
    ");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update password and clear reset token
function updateUserPassword(PDO $pdo, int $userId, string $hashedPassword): bool
{
    $stmt = $pdo->prepare("
        UPDATE users 
        SET passwd = ?,
            password_reset_token = NULL,
            password_reset_expires_at = NULL,
            updated_at = NOW()
        WHERE id = ?
    ");
    return $stmt->execute([$hashedPassword, $userId]);
}

?>