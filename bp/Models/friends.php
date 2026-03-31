<?php

// Pošle žádost o přátelství
function sendFriendRequest(PDO $pdo, int $sender_id, int $receiver_id)
{
    // Nelze poslat žádost sám sobě
    if ($sender_id === $receiver_id) {
        return false;
    }

    // Zkontrolujeme jestli přátelství/žádost už neexistuje (v obou směrech)
    $stmt = $pdo->prepare("
        SELECT id FROM friendships
        WHERE (user_id_sender = ? AND user_id_receiver = ?)
        OR (user_id_receiver = ? AND user_id_sender = ?)
    ");
    $stmt->execute([$sender_id, $receiver_id, $sender_id, $receiver_id]);

    if ($stmt->fetch()) {
        return false;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO friendships (user_id_sender, user_id_receiver, status)
            VALUES (?, ?, 'pending')
        ");
        return $stmt->execute([$sender_id, $receiver_id]);
    } catch (PDOException $e) {
        return false;
    }
}

// Přijme žádost o přátelství
function acceptFriendRequest(PDO $pdo, int $friendship_id, int $receiver_id)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE friendships
            SET status = 'accepted', friends_since = NOW()
            WHERE id = ? AND user_id_receiver = ? AND status = 'pending'
        ");
        return $stmt->execute([$friendship_id, $receiver_id]);
    } catch (PDOException $e) {
        return false;
    }
}

// Odmítne žádost o přátelství
function declineFriendRequest(PDO $pdo, int $friendship_id, int $receiver_id)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE friendships
            SET status = 'declined'
            WHERE id = ? AND user_id_receiver = ? AND status = 'pending'
        ");
        return $stmt->execute([$friendship_id, $receiver_id]);
    } catch (PDOException $e) {
        return false;
    }
}

// Odstraní přátelství (v obou směrech)
function removeFriend(PDO $pdo, int $current_user_id, int $friend_id)
{
    try {
        $stmt = $pdo->prepare("
            DELETE FROM friendships
            WHERE (user_id_sender = ? AND user_id_receiver = ?)
               OR (user_id_sender = ? AND user_id_receiver = ?)
        ");
        return $stmt->execute([$current_user_id, $friend_id, $friend_id, $current_user_id]);
    } catch (PDOException $e) {
        return false;
    }
}

// Vrátí všechny přátele uživatele (accepted)
function getAllUserFriends(PDO $pdo, int $current_user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.first_name, u.surname, u.email, f.friends_since
            FROM friendships f
            JOIN users u ON u.id = f.user_id_receiver
            WHERE f.user_id_sender = ? AND f.status = 'accepted' AND u.deleted_at IS NULL

            UNION

            SELECT u.id, u.first_name, u.surname, u.email, f.friends_since
            FROM friendships f
            JOIN users u ON u.id = f.user_id_sender
            WHERE f.user_id_receiver = ? AND f.status = 'accepted' AND u.deleted_at IS NULL

            ORDER BY first_name ASC
        ");
        $stmt->execute([$current_user_id, $current_user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Vrátí příchozí žádosti čekající na přijetí
function getPendingRequests(PDO $pdo, int $current_user_id)
{
    $stmt = $pdo->prepare("
        SELECT
            f.id AS friendship_id,
            u.id,
            u.first_name,
            u.surname,
            u.email
        FROM friendships f
        JOIN users u ON u.id = f.user_id_sender
        WHERE f.user_id_receiver = ?
        AND f.status = 'pending'
        AND u.deleted_at IS NULL
    ");
    $stmt->execute([$current_user_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// Vyhledá uživatele podle jména nebo emailu (pro poslání žádosti)
function searchUserByEmail(PDO $pdo, string $email, int $current_user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT id, first_name, surname, birth_year
            FROM users
            WHERE email = ? AND id != ? AND deleted_at IS NULL
        ");
        $stmt->execute([trim($email), $current_user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

function getPendingEventInvitations(PDO $pdo, int $current_user_id)
{
    $stmt = $pdo->prepare("
        SELECT e.id, e.event_name, e.start_at, e.end_at, e.status,
               er.id AS registration_id,
               u.first_name, u.surname
        FROM event_registrations er
        JOIN events e ON e.id = er.event_id
        JOIN users u ON u.id = e.creator
        WHERE er.user_id = ? AND er.status = 'pending'
          AND e.deleted_at IS NULL
        ORDER BY e.start_at ASC
    ");
    $stmt->execute([$current_user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSentRequests(PDO $pdo, int $current_user_id){
        $stmt = $pdo->prepare("
        SELECT
            f.id AS friendship_id,
            u.id,
            u.first_name,
            u.surname,
            u.email
        FROM friendships f
        JOIN users u ON u.id = f.user_id_receiver
        WHERE f.user_id_sender = ?
        AND f.status = 'pending'
        AND u.deleted_at IS NULL
    ");
    $stmt->execute([$current_user_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}



?>