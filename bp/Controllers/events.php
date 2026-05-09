<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $domena . "/auth/login");
    exit;
}
include 'Models/user.php';
include 'Models/events.php';
include 'Models/friends.php';

$user_data = getUserById($pdo, $_SESSION['user_id']);

// --- CREATE ---
if ($router['method'] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_create_event'])) {

    $event_name = trim($_POST['event_name'] ?? '');
    $location = !empty($_POST['location']) ? trim($_POST['location']) : null;
    $start_at = !empty($_POST['start_at']) ? $_POST['start_at'] : null;
    $end_at = !empty($_POST['end_at']) ? $_POST['end_at'] : null;
    $status = $_POST['status'] ?? 'planning';
    $description = !empty($_POST['description_event']) ? trim($_POST['description_event']) : null;
    $voting_deadline = !empty($_POST['voting_deadline']) ? $_POST['voting_deadline'] : null;

    $allowed_statuses = ['confirmed', 'planning'];
    if (!in_array($status, $allowed_statuses)) {
        setFlash('error', 'Invalid status value.');
        header("Location: " . $domena . "/events/create");
        exit;
    }

    if (empty($event_name)) {
        setFlash('error', 'Event name is required.');
        header("Location: " . $domena . "/events/create");
        exit;
    }

    if (strlen($event_name) > 150) {
        setFlash('error', 'Event name is too long (max 150 characters).');
        header("Location: " . $domena . "/events/create");
        exit;
    }

    if ($description && strlen($description) > 1000) {
        setFlash('error', 'Description is too long (max 1000 characters).');
        header("Location: " . $domena . "/events/create");
        exit;
    }

    if ($start_at && $end_at) {
        $start = new DateTime($start_at);
        $end = new DateTime($end_at);
        if ($end <= $start) {
            setFlash('error', 'End time must be after start time.');
            header("Location: " . $domena . "/events/create");
            exit;
        }
    }

    // Deadline cannot be in the past
    if ($status === 'planning' && $voting_deadline && new DateTime($voting_deadline) <= new DateTime()) {
        setFlash('error', 'Voting deadline cannot be in the past.');
        header("Location: " . $domena . "/events/create");
        exit;
    }

    // Deadline must be before all proposed poll dates
    if ($status === 'planning' && $voting_deadline && !empty($_POST['poll_starts'])) {
        $deadline = new DateTime($voting_deadline);
        foreach ($_POST['poll_starts'] as $ps) {
            if (!empty($ps) && $deadline >= new DateTime($ps)) {
                setFlash('error', 'Voting deadline must be earlier than the earliest proposed date.');
                header("Location: " . $domena . "/events/create");
                exit;
            }
        }
    }

    // Validate invitees
    $invited_ids = array_map('intval', $_POST['invitee_ids'] ?? []);
    if (!empty($invited_ids)) {
        $friends = getAllUserFriends($pdo, $_SESSION['user_id']);
        $friend_ids = array_column($friends, 'id');
        foreach ($invited_ids as $inv_id) {
            if (!in_array($inv_id, $friend_ids)) {
                setFlash('error', 'You can only invite friends.');
                header("Location: " . $domena . "/events/create");
                exit;
            }
        }
    }

    $isSaved = createEvent($pdo, $_SESSION['user_id'], $event_name, $location, $start_at, $end_at, $status, $description, $voting_deadline);

    if ($isSaved) {
        $new_event_id = $pdo->lastInsertId();

        // Creator is automatically attending
        addEventRegistration($pdo, $new_event_id, $_SESSION['user_id'], 'attending');

        foreach ($invited_ids as $inv_id) {
            addEventRegistration($pdo, $new_event_id, $inv_id, 'pending');
        }

        if ($status === 'planning' && isset($_POST['poll_starts']) && isset($_POST['poll_ends'])) {
            $starts = $_POST['poll_starts'];
            $ends = $_POST['poll_ends'];

            for ($i = 0; $i < count($starts); $i++) {
                $poll_start = trim($starts[$i]);
                $poll_end = trim($ends[$i]);
                if (!empty($poll_start) && !empty($poll_end)) {
                    $ps = new DateTime($poll_start);
                    $pe = new DateTime($poll_end);
                    if ($pe > $ps) {
                        addEventToPollOption($pdo, $new_event_id, $poll_start, $poll_end);
                    }
                }
            }
        }

        setFlash('success', 'Event successfully created!');
        header("Location: " . $domena . "/events");
        exit;
    } else {
        setFlash('error', 'Error: Event could not be saved.');
        header("Location: " . $domena . "/events/create");
        exit;
    }
}

// --- UPDATE ---
if ($router['method'] === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_update_event'])) {

    $event_id = ($router['params'][0] ?? 0);

    if (!$event_id) {
        setFlash('error', 'Invalid event ID.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $event_to_edit = getEventById($pdo, $event_id);
    if (!$event_to_edit) {
        setFlash('error', 'Event not found.');
        header("Location: " . $domena . "/events");
        exit;
    }
    if ($event_to_edit['creator'] !== $_SESSION['user_id'] && $user_data['role'] !== 'admin') {
        setFlash('error', 'You are not allowed to edit this event.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $event_name = trim($_POST['event_name'] ?? '');
    $location = !empty($_POST['location']) ? trim($_POST['location']) : null;
    $start_at = !empty($_POST['start_at']) ? $_POST['start_at'] : null;
    $end_at = !empty($_POST['end_at']) ? $_POST['end_at'] : null;
    $status = $_POST['status'] ?? 'planning';
    $description = !empty($_POST['description_event']) ? trim($_POST['description_event']) : null;
    $voting_deadline = !empty($_POST['voting_deadline']) ? $_POST['voting_deadline'] : null;

    $allowed_statuses = ['confirmed', 'planning', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        setFlash('error', 'Invalid status value.');
        header("Location: " . $domena . "/events/edit/" . $event_id);
        exit;
    }

    if (empty($event_name)) {
        setFlash('error', 'Event name is required.');
        header("Location: " . $domena . "/events/edit/" . $event_id);
        exit;
    }

    if (strlen($event_name) > 150) {
        setFlash('error', 'Event name is too long (max 150 characters).');
        header("Location: " . $domena . "/events/edit/" . $event_id);
        exit;
    }

    if ($status === 'planning' && $voting_deadline == null) {
        setFlash('error', 'You need to set deadline for voting!');
        header("Location: " . $domena . "/events/edit/" . $event_id);
        exit;
    }

    // Deadline cannot be in the past
    if ($status === 'planning' && $voting_deadline && new DateTime($voting_deadline) <= new DateTime()) {
        setFlash('error', 'Voting deadline cannot be in the past.');
        header("Location: " . $domena . "/events/edit/" . $event_id);
        exit;
    }

    if ($start_at && $end_at) {
        $start = new DateTime($start_at);
        $end = new DateTime($end_at);
        if ($end <= $start) {
            setFlash('error', 'End time must be after start time.');
            header("Location: " . $domena . "/events/edit/" . $event_id);
            exit;
        }
    }

    // Deadline must be before all proposed poll dates
    if ($status === 'planning' && $voting_deadline && !empty($_POST['poll_starts'])) {
        $deadline = new DateTime($voting_deadline);
        foreach ($_POST['poll_starts'] as $ps) {
            if (!empty($ps) && $deadline >= new DateTime($ps)) {
                setFlash('error', 'Voting deadline must be earlier than the earliest proposed date.');
                header("Location: " . $domena . "/events/edit/" . $event_id);
                exit;
            }
        }
    }

    // Validate invitees - must be friends
    $invited_ids = array_map('intval', $_POST['invitee_ids'] ?? []);
    if (!empty($invited_ids)) {
        $friends = getAllUserFriends($pdo, $_SESSION['user_id']);
        $friend_ids = array_column($friends, 'id');
        foreach ($invited_ids as $inv_id) {
            if (!in_array($inv_id, $friend_ids)) {
                setFlash('error', 'You can only invite friends.');
                header("Location: " . $domena . "/events/edit/" . $event_id);
                exit;
            }
        }
    }

    $isUpdated = updateEvent($pdo, $event_id, $event_name, $location, $start_at, $end_at, $status, $description, $voting_deadline);

    if ($isUpdated) {
        foreach ($invited_ids as $inv_id) {
            addEventRegistration($pdo, $event_id, $inv_id, 'pending');
        }

        if ($status === 'planning' && isset($_POST['poll_starts']) && isset($_POST['poll_ends'])) {
            deletePollOptionsByEventId($pdo, $event_id);
            $starts = $_POST['poll_starts'];
            $ends = $_POST['poll_ends'];
            for ($i = 0; $i < count($starts); $i++) {
                $poll_start = trim($starts[$i]);
                $poll_end = trim($ends[$i] ?? '');
                if (!empty($poll_start) && !empty($poll_end)) {
                    $ps = new DateTime($poll_start);
                    $pe = new DateTime($poll_end);
                    if ($pe > $ps) {
                        addEventToPollOption($pdo, $event_id, $poll_start, $poll_end);
                    }
                }
            }
        }

        setFlash('success', 'Event was successfully updated!');
        header("Location: " . $domena . "/events/detail/" . $event_id);
        exit;
    } else {
        setFlash('error', 'Error: Event could not be updated.');
        header("Location: " . $domena . "/events/edit/" . $event_id);
        exit;
    }
}

// --- DELETE ---
if ($router['method'] === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_delete_event'])) {

    $event_id = ($router['params'][0] ?? 0);

    if (!$event_id) {
        setFlash('error', 'Invalid event ID.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $event_to_delete = getEventById($pdo, $event_id);
    if (!$event_to_delete) {
        setFlash('error', 'Event not found.');
        header("Location: " . $domena . "/events");
        exit;
    }
    if ($event_to_delete['creator'] !== $_SESSION['user_id'] && $user_data['role'] !== 'admin') {
        setFlash('error', 'You are not allowed to delete this event.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $isDeleted = deleteEvent($pdo, $event_id);

    if ($isDeleted) {
        setFlash('success', 'Event was successfully deleted!');
    } else {
        setFlash('error', 'Error: Event could not be deleted.');
    }
    header("Location: " . $domena . "/events");
    exit;
}

// --- ACCEPT INVITATION / CHANGE BACK TO ATTENDING ---
if ($router['method'] === 'show' && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit_accept_invitation']) || isset($_POST['submit_change_to_attending']))) {
    $registration_id = intval($_POST['registration_id'] ?? 0);

    if (acceptInvitation($pdo, $registration_id, $_SESSION['user_id'])) {
        setFlash('success', 'You are now attending this event!');
    } else {
        setFlash('error', 'Could not accept invitation.');
    }
    header("Location: " . $domena . "/events");
    exit;
}

// --- DECLINE INVITATION ---
if ($router['method'] === 'show' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_decline_invitation'])) {
    $registration_id = intval($_POST['registration_id'] ?? 0);

    if (declineInvitation($pdo, $registration_id, $_SESSION['user_id'])) {
        setFlash('success', 'Invitation declined.');
    } else {
        setFlash('error', 'Could not decline invitation.');
    }
    header("Location: " . $domena . "/events");
    exit;
}

// --- PARTICIPATION ---
if ($router['method'] === 'detail' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_participation'])) {

    $participation_status = $_POST['submit_participation'];
    $target_event_id = ($_POST['event_id'] ?? 0);
    $current_user_id = $_SESSION['user_id'];

    $allowed_statuses = ['attending', 'declined'];

    if ($target_event_id && in_array($participation_status, $allowed_statuses)) {
        updateParticipation($pdo, $target_event_id, $current_user_id, $participation_status);
        setFlash('success', 'Your participation status has been updated!');
    } else {
        setFlash('error', 'Invalid participation status.');
    }

    header("Location: " . $domena . "/events/detail/" . $target_event_id);
    exit;
}

// --- CANCEL EVENT ---
if ($router['method'] === 'detail' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_cancel_event'])) {

    $target_event_id = ($router['params'][0] ?? 0);

    if (!$target_event_id) {
        setFlash('error', 'Invalid event ID.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $event_to_cancel = getEventById($pdo, $target_event_id);
    if (!$event_to_cancel) {
        setFlash('error', 'Event not found.');
        header("Location: " . $domena . "/events");
        exit;
    }
    if ($event_to_cancel['creator'] !== $_SESSION['user_id'] && $user_data['role'] !== 'admin') {
        setFlash('error', 'You are not allowed to cancel this event.');
        header("Location: " . $domena . "/events/detail/" . $target_event_id);
        exit;
    }

    $isCancelled = updateEventStatus($pdo, $target_event_id, 'cancelled');
    if ($isCancelled) {
        setFlash('success', 'Event has been cancelled.');
    } else {
        setFlash('error', 'Event could not be cancelled.');
    }
    header("Location: " . $domena . "/events/detail/" . $target_event_id);
    exit;
}

// --- POLL VOTE ---
if ($router['method'] === 'detail' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vote'])) {

    $option_id = ($_POST['poll_option'] ?? 0);
    $target_event_id = ($_POST['event_id'] ?? 0);
    $current_user_id = $_SESSION['user_id'];

    if ($option_id && $target_event_id) {
        castPollVote($pdo, $target_event_id, $current_user_id, $option_id);
        setFlash('success', 'Your vote has been recorded!');
    } else {
        setFlash('error', 'Please select an option before voting.');
    }

    header("Location: " . $domena . "/events/detail/" . $target_event_id);
    exit;
}

// --- RENDER ---

include 'Views/includes/head.php';
include 'Views/includes/navbar.php';

if ($router['method'] === 'create') {
    $friends_list = getAllUserFriends($pdo, $_SESSION['user_id']);
    include 'Views/events/create.php';

} elseif ($router['method'] === 'edit') {

    $event_id = ($router['params'][0] ?? 0);

    if (!$event_id) {
        setFlash('error', 'No event ID specified.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $event_data = getEventById($pdo, $event_id);

    if (!$event_data) {
        setFlash('error', 'This event does not exist.');
        header("Location: " . $domena . "/events");
        exit;
    }

    if ($event_data['creator'] !== $_SESSION['user_id'] && $user_data['role'] !== 'admin') {
        setFlash('error', 'You are not allowed to edit this event.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $friends_list = getAllUserFriends($pdo, $_SESSION['user_id']);
    include 'Views/events/edit.php';

} elseif ($router['method'] === 'detail') {

    $event_id = ($router['params'][0] ?? 0);

    if (!$event_id) {
        setFlash('error', 'No event ID specified.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $event_data = getEventById($pdo, $event_id);

    if (!$event_data) {
        setFlash('warning', 'This event does not exist.');
        header("Location: " . $domena . "/events");
        exit;
    }

    $voting_deadline = !empty($event_data['voting_deadline']) ? strtotime($event_data['voting_deadline']) : null;
    if ($event_data['status'] === 'planning' && $voting_deadline && time() > $voting_deadline) {
        closePollAndConfirmEvent($pdo, $event_id);
        $event_data = getEventById($pdo, $event_id);
    }

    if ($event_data['status'] === 'planning') {
        $poll_options = getPollOptionsByEventId($pdo, $event_id);
        $vote_counts = getPollVoteCounts($pdo, $event_id);
        $user_current_vote = getUserVote($pdo, $event_id, $_SESSION['user_id']);
    }

    $now = time();
    $start_ts = !empty($event_data['start_at']) ? strtotime($event_data['start_at']) : null;
    $end_ts = !empty($event_data['end_at']) ? strtotime($event_data['end_at']) : $start_ts;

    if ($event_data['status'] === 'cancelled') {
        $display_status = ['label' => 'Cancelled', 'badge' => 'ended'];
    } elseif ($event_data['status'] === 'planning') {
        $display_status = ['label' => 'Planning', 'badge' => 'planning'];
    } elseif ($start_ts && $end_ts && $start_ts <= $now && $end_ts >= $now) {
        $display_status = ['label' => 'Running now', 'badge' => 'running'];
    } elseif ($end_ts && $end_ts < $now) {
        $display_status = ['label' => 'Ended', 'badge' => 'ended'];
    } else {
        $display_status = ['label' => 'Upcoming', 'badge' => 'upcoming'];
    }

    $user_participation_status = getUserRegistrationStatus($pdo, $event_id, $_SESSION['user_id']);

    include 'Views/events/details.php';

} elseif ($router['method'] === null || $router['method'] === 'show') {

    $all_events = getAllEventsForUser($pdo, $_SESSION['user_id']);
    $pending_invitations = getPendingEventInvitations($pdo, $_SESSION['user_id']);
    $voting_events = [];
    $running_events = [];
    $upcoming_events = [];
    $past_events = [];
    $cancelled_events = [];
    $declined_events = [];
    $current_time = time();

    foreach ($all_events as $event) {
        if (($event['my_status'] ?? null) === 'declined') {
            $declined_events[] = $event;
            continue;
        }

        if ($event['status'] === 'cancelled') {
            $cancelled_events[] = $event;
            continue;
        }

        if ($event['status'] === 'planning' || empty($event['start_at'])) {
            $voting_events[] = $event;
            continue;
        }

        $start_time = strtotime($event['start_at']);
        $end_time = !empty($event['end_at']) ? strtotime($event['end_at']) : $start_time;

        if ($start_time <= $current_time && $end_time >= $current_time) {
            $running_events[] = $event;
        } elseif ($end_time < $current_time) {
            $past_events[] = $event;
        } else {
            $upcoming_events[] = $event;
        }
    }

    include 'Views/events/show.php';

} else {
    include 'Views/error.php';
}

include 'Views/includes/footer.php';
?>