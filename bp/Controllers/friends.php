<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $domena . "/auth/login");
    exit;
}

include 'Models/friends.php';
include 'Models/user.php';

$current_user_id = $_SESSION['user_id'];

// --- SEND FRIEND REQUEST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_send_request'])) {
    $receiver_id = ($_POST['receiver_id'] ?? 0);

    if (!$receiver_id) {
        setFlash('error', 'Invalid user.');
        header("Location: " . $domena . "/friends");
        exit;
    }

    $result = sendFriendRequest($pdo, $current_user_id, $receiver_id);

    if ($result) {
        setFlash('success', 'Friend request sent!');
    } else {
        setFlash('error', 'Could not send friend request. It may already exist.');
    }

    header("Location: " . $domena . "/friends");
    exit;
}

// --- ACCEPT REQUEST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_accept_request'])) {
    $friendship_id = ($_POST['friendship_id'] ?? 0);

    if (acceptFriendRequest($pdo, $friendship_id, $current_user_id)) {
        setFlash('success', 'Friend request accepted!');
    } else {
        setFlash('error', 'Could not accept friend request.');
    }

    header("Location: " . $domena . "/friends");
    exit;
}

// --- DECLINE REQUEST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_decline_request'])) {
    $friendship_id = ($_POST['friendship_id'] ?? 0);

    if (declineFriendRequest($pdo, $friendship_id, $current_user_id)) {
        setFlash('success', 'Friend request declined.');
    } else {
        setFlash('error', 'Could not decline friend request.');
    }

    header("Location: " . $domena . "/friends");
    exit;
}

// --- REMOVE FRIEND ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_remove_friend'])) {
    $friend_id = ($_POST['friend_id'] ?? 0);

    if (removeFriend($pdo, $current_user_id, $friend_id)) {
        setFlash('success', 'Friend removed.');
    } else {
        setFlash('error', 'Could not remove friend.');
    }

    header("Location: " . $domena . "/friends");
    exit;
}

// --- RENDER ---

include 'Views/includes/head.php';
include 'Views/includes/navbar.php';

// --- FRIEND PROFILE ---
if ($router['method'] === 'profile') {
    $friend_id = ($router['params'][0] ?? 0);

    if (!$friend_id) {
        setFlash('error', 'Invalid user ID.');
        header("Location: " . $domena . "/friends");
        exit;
    }

    $friend_data = getUserById($pdo, $friend_id);

    if (!$friend_data) {
        setFlash('error', 'User not found.');
        header("Location: " . $domena . "/friends");
        exit;
    }

    include 'Views/friends/profile.php';

    // --- SEARCH ---
} elseif ($router['method'] === 'search') {
    $search_result = null;
    $search_email = trim($_GET['email'] ?? '');

    if (!empty($search_email)) {
        if (!filter_var($search_email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
        } else {
            $search_result = searchUserByEmail($pdo, $search_email, $current_user_id);
            if (!$search_result) {
                setFlash('error', 'No user found with this email.');
            }
        }
    }

    include 'Views/friends/search.php';

    // --- FRIENDS LIST ---
} else {
    $friends = getAllUserFriends($pdo, $current_user_id);
    $pending_requests = getPendingRequests($pdo, $current_user_id);
    $sent_requests = getSentRequests($pdo, $current_user_id);

    include 'Views/friends/show.php';
}

include 'Views/includes/footer.php';
?>