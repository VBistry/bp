<?php
$host = "localhost";   // server
$db   = "planner";     // database name
$user = "root";        // database user
$pass = "";            // password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // throws exception on error
} catch (PDOException $e) {
    echo "Chyba připojení: " . $e->getMessage();
}

?>
