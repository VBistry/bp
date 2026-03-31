<?php
if ($router['method'] === 'validate-emails') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $emails = $data['emails'] ?? [];

    include 'Models/events.php';

    foreach ($emails as $email) {
        $email = trim($email);
        if (empty($email)) continue;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['valid' => false, 'error' => '"' . $email . '" is not a valid email address.']);
            exit;
        }

        if (!getUserIdByEmail($pdo, $email)) {
            echo json_encode(['valid' => false, 'error' => 'User "' . $email . '" does not exist.']);
            exit;
        }
    }

    echo json_encode(['valid' => true]);
    exit;
}