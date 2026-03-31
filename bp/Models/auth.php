<?php

// Najde uživatele podle emailu - používá se při přihlášení a kontrole duplicit
function findUserByEmail(PDO $pdo, string $email)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([trim($email)]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Zaregistruje nového uživatele
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

?>