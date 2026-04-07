<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $domena . "/auth/login");
    exit;
}
include 'Models/events.php';
$events_array = getUserEvents($pdo, $_SESSION['user_id']); //events for fullcalendar

$events_today = getEventsTodayForUser($pdo, $_SESSION['user_id']);

$events_json = json_encode($events_array);

// --- RENDER ---

include 'Views/includes/head.php';
include 'Views/includes/navbar.php';

include 'Views/dashboard/show.php';
include 'Views/includes/footer.php';
?>