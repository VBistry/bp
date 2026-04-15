<?php
$host = "localhost";   // server
$db   = "planner";     // database name
$user = "root";        // database user
$pass = "";            // password
$charset = "utf8mb4";  // character set

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Chyba připojení: " . $e->getMessage();
}

?>
