<?php

function getEventById(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$event_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Vrátí pole eventů pro FullCalendar (vlastní i pozvané)
function getUserEvents(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("
        SELECT
            id,
            event_name AS title,
            start_at AS start,
            end_at AS end
        FROM events
        WHERE (creator = ? OR id IN (
            SELECT event_id FROM event_registrations
            WHERE user_id = ? AND status IN ('attending', 'maybe')
        ))
        AND deleted_at IS NULL
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Vrátí všechny eventy pro seznam (vlastní i pozvané)
function getAllEventsForUser(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("
        SELECT * FROM events
        WHERE (creator = ? OR id IN (
            SELECT event_id FROM event_registrations
            WHERE user_id = ? AND status IN ('attending', 'maybe')
        ))
        AND deleted_at IS NULL
        ORDER BY start_at ASC
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createEvent(PDO $pdo, int $user_id, string $event_name, ?string $location, ?string $start_at, ?string $end_at, string $status, ?string $description_event, ?string $voting_deadline = null)
{
    $stmt = $pdo->prepare("
        INSERT INTO events (event_name, location, start_at, end_at, status, description_event, creator, voting_deadline) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $event_name,
        $location,
        $start_at,
        $end_at,
        $status,
        $description_event,
        $user_id,
        $voting_deadline
    ]);
}

function updateEvent(PDO $pdo, int $event_id, string $event_name, ?string $location, ?string $start_at, ?string $end_at, string $status, ?string $description_event, ?string $voting_deadline = null)
{
    $stmt = $pdo->prepare("
        UPDATE events
        SET event_name = ?, location = ?, start_at = ?, end_at = ?, status = ?, description_event = ?, voting_deadline = ?
        WHERE id = ? AND deleted_at IS NULL
    ");
    return $stmt->execute([
        $event_name,
        $location,
        $start_at,
        $end_at,
        $status,
        $description_event,
        $voting_deadline,
        $event_id
    ]);
}

// SOFT DELETE — nastaví deleted_at místo skutečného smazání
function deleteEvent(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("UPDATE events SET deleted_at = NOW() WHERE id = ?");
    return $stmt->execute([$event_id]);
}

//==========================================================================
//                          Funkce pro ankety
//==========================================================================

function getPollOptionsByEventId(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("SELECT * FROM event_poll_options WHERE event_id = ?");
    $stmt->execute([$event_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addEventToPollOption(PDO $pdo, int $event_id, string $start_at, string $end_at)
{
    $stmt = $pdo->prepare("INSERT INTO event_poll_options (event_id, start_at, end_at) VALUES (?, ?, ?)");
    return $stmt->execute([$event_id, $start_at, $end_at]);
}

function deletePollOptionsByEventId(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("DELETE FROM event_poll_options WHERE event_id = ?");
    return $stmt->execute([$event_id]);
}

function addEventRegistration(PDO $pdo, int $event_id, int $user_id, string $status = 'pending')
{
    $stmt = $pdo->prepare("SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    if ($stmt->fetch()) {
        return false;
    }
    $stmt = $pdo->prepare("INSERT INTO event_registrations (event_id, user_id, status) VALUES (?, ?, ?)");
    return $stmt->execute([$event_id, $user_id, $status]);
}

function getUserIdByEmail(PDO $pdo, string $email)
{
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute([trim($email)]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function updateParticipation(PDO $pdo, int $event_id, int $user_id, string $status)
{
    $stmt = $pdo->prepare("UPDATE event_registrations SET status = ? WHERE event_id = ? AND user_id = ?");
    return $stmt->execute([$status, $event_id, $user_id]);
}

function getEventCreatorById(PDO $pdo, int $creator_id)
{
    $stmt = $pdo->prepare("SELECT first_name, surname FROM users WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$creator_id]);
    $creator = $stmt->fetch(PDO::FETCH_ASSOC);
    return $creator ? $creator['first_name'] . ' ' . $creator['surname'] : 'Unknown';
}

// --- FUNKCE PRO HLASOVÁNÍ ---

function castPollVote(PDO $pdo, int $event_id, int $user_id, int $option_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO event_poll_votes (event_id, user_id, option_id) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE option_id = VALUES(option_id)
    ");
    return $stmt->execute([$event_id, $user_id, $option_id]);
}

function getPollVoteCounts(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("SELECT option_id, COUNT(*) as vote_count FROM event_poll_votes WHERE event_id = ? GROUP BY option_id");
    $stmt->execute([$event_id]);
    $counts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $counts[$row['option_id']] = $row['vote_count'];
    }
    return $counts;
}

function getUserVote(PDO $pdo, int $event_id, int $user_id)
{
    $stmt = $pdo->prepare("SELECT option_id FROM event_poll_votes WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['option_id'] : null;
}

function getPollOptionWinner(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("
        SELECT option_id, COUNT(*) as vote_count 
        FROM event_poll_votes 
        WHERE event_id = ? 
        GROUP BY option_id 
        ORDER BY vote_count DESC 
        LIMIT 1
    ");
    $stmt->execute([$event_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function closePollAndConfirmEvent(PDO $pdo, int $event_id)
{
    $winner = getPollOptionWinner($pdo, $event_id);

    if ($winner && !empty($winner['option_id'])) {
        $option = getPollOptionById($pdo, $winner['option_id']);
        $stmt = $pdo->prepare("UPDATE events SET start_at = ?, end_at = ?, status = 'confirmed' WHERE id = ?");
        return $stmt->execute([$option['start_at'], $option['end_at'], $event_id]);
    } else {
        $all_options = getPollOptionsByEventId($pdo, $event_id);
        if (!empty($all_options)) {
            $first_option = $all_options[0];
            $stmt = $pdo->prepare("UPDATE events SET start_at = ?, end_at = ?, status = 'confirmed' WHERE id = ?");
            return $stmt->execute([$first_option['start_at'], $first_option['end_at'], $event_id]);
        }
    }
    return false;
}

function getPollOptionById(PDO $pdo, int $option_id)
{
    $stmt = $pdo->prepare("SELECT * FROM event_poll_options WHERE id = ?");
    $stmt->execute([$option_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getEventsParticipants(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.surname
        FROM users u
        JOIN event_registrations er ON u.id = er.user_id
        WHERE er.event_id = ? AND er.status = 'attending'
        AND u.deleted_at IS NULL
    ");
    $stmt->execute([$event_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEventsDeclinedInvitations(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.surname
        FROM users u
        JOIN event_registrations er ON u.id = er.user_id
        WHERE er.event_id = ? AND er.status = 'declined'
        AND u.deleted_at IS NULL
    ");
    $stmt->execute([$event_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEventsInvitations(PDO $pdo, int $event_id)
{
    $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.surname 
        FROM users u
        JOIN event_registrations er ON u.id = er.user_id
        WHERE er.event_id = ? AND er.status IN ('pending', 'declined')
        AND u.deleted_at IS NULL
    ");
    $stmt->execute([$event_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEventsTodayForUser(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("
        SELECT * FROM events 
        WHERE (creator = ? OR id IN (
            SELECT event_id FROM event_registrations
            WHERE user_id = ? AND status IN ('attending', 'maybe')
        ))
        AND DATE(start_at) = CURDATE()
        AND deleted_at IS NULL
        ORDER BY start_at ASC
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateEventStatus(PDO $pdo, int $event_id, string $status)
{
    $stmt = $pdo->prepare("
        UPDATE events
        SET status = ?
        WHERE id = ? AND deleted_at IS NULL
    ");
    return $stmt->execute([$status, $event_id]);
}

?>