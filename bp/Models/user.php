<?php

function getUserById(PDO $pdo, int $user_id)
{
    try {
        $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE id = ?
        AND deleted_at IS NULL");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

function updateUser(PDO $pdo, int $user_id, array $data)
{
    // Check if email is already used by another active user
    $stmt = $pdo->prepare("
    SELECT id
    FROM users
    WHERE email = ?
    AND id != ? AND deleted_at IS NULL");
    $stmt->execute([$data['email'], $user_id]);

    if ($stmt->fetch()) {
        return "Another user with this email already exists.";
    }

    try {
        if (!empty($data['passwd'])) {
            // User is also changing password
            $sql = "
            UPDATE users SET 
                        first_name = ?, 
                        surname = ?, 
                        email = ?, 
                        tel = ?, 
                        gender = ?, 
                        birth_year = ?, 
                        passwd = ?, 
                        description_user = ?,
                        updated_at = NOW() 
                    WHERE id = ?
                    AND deleted_at IS NULL";

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
            // User is not changing password
            $sql = "
            UPDATE users SET 
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

// Soft delete — sets deleted_at instead of actually deleting
function deleteUserById(PDO $pdo, int $user_id)
{
    try {
        $stmt = $pdo->prepare("
        UPDATE users
        SET deleted_at = NOW()
        WHERE id = ?");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        return false;
    }
}


function updateUserAvatar(PDO $pdo, int $user_id, string $avatar_url)
{
    try {
        $stmt = $pdo->prepare("
        UPDATE users
        SET avatar_url = ?, updated_at = NOW()
        WHERE id = ?
        AND deleted_at IS NULL");
        return $stmt->execute([$avatar_url, $user_id]);
    } catch (PDOException $e) {
        return false;
    }
}

?>