<?php
include 'Models/events.php';
$events_array = getUserEvents($pdo, $_SESSION['user_id']); //slouzi pouze pro fullcalendar, musi byt zde start/end/title

$events_today = getEventsTodayForUser($pdo, $_SESSION['user_id']);

$events_json = json_encode($events_array);

include 'Views/includes/head.php';
include 'Views/includes/navbar.php';

include 'Views/dashboard/show.php';
include 'Views/includes/footer.php';
?>