<?php

function getUserById(PDO $pdo, int $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

function updateUser(PDO $pdo, int $user_id, array $data)
{
    // Kontrola, zda email už nepoužívá někdo jiný (aktivní uživatel)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL");
    $stmt->execute([$data['email'], $user_id]);

    if ($stmt->fetch()) {
        return "Another user with this email already exists.";
    }

    try {
        if (!empty($data['passwd'])) {
            // Uživatel mění i heslo
            $sql = "UPDATE users SET 
                        first_name = ?, 
                        surname = ?, 
                        email = ?, 
                        tel = ?, 
                        gender = ?, 
                        birth_year = ?, 
                        passwd = ?, 
                        description_user = ?,
                        updated_at = NOW() 
                    WHERE id = ? AND deleted_at IS NULL";

            $params = [
                $data['first_name'],
                $data['surname'],
                $data['email'],
                $data['tel'],
                $data['gender'],
                $data['birth_year'],
                $data['passwd'],
                $data['description_user'],
                $user_id
            ];
        } else {
            // Uživatel nemění heslo
            $sql = "UPDATE users SET 
                        first_name = ?, 
                        surname = ?, 
                        email = ?, 
                        tel = ?, 
                        gender = ?, 
                        birth_year = ?, 
                        description_user = ?,
                        updated_at = NOW() 
                    WHERE id = ? AND deleted_at IS NULL";

            $params = [
                $data['first_name'],
                $data['surname'],
                $data['email'],
                $data['tel'],
                $data['gender'],
                $data['birth_year'],
                $data['description_user'],
                $user_id
            ];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return true;

    } catch (PDOException $e) {
        return "Database error: " . $e->getMessage();
    }
}

// SOFT DELETE — nastaví deleted_at místo skutečného smazání
function deleteUserById(PDO $pdo, int $user_id)
{
    try {
        $stmt = $pdo->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        return false;
    }
}

function createUser(PDO $pdo, array $data)
{
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return "A user with this email already exists.";
    }

    try {
        $hash_hesla = password_hash($data['heslo'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, surname, email, passwd, tel, role)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['first_name'],
            $data['surname'],
            $data['email'],
            $hash_hesla,
            $data['tel'],
            $data['role'] ?? 'user'
        ]);
        return true;
    } catch (PDOException $e) {
        return "Database error: " . $e->getMessage();
    }
}

function updateUserAvatar(PDO $pdo, int $user_id, string $avatar_url)
{
    try {
        $stmt = $pdo->prepare("UPDATE users SET avatar_url = ?, updated_at = NOW() WHERE id = ? AND deleted_at IS NULL");
        return $stmt->execute([$avatar_url, $user_id]);
    } catch (PDOException $e) {
        return false;
    }
}

?>