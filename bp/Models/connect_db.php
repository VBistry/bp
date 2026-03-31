<?php
$host = "localhost";   // server
$db   = "planner";     // jméno databáze
$user = "root";        // uživatel databáze
$pass = "";            // heslo

// phpinfo();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Říká PDO: „Když se něco pokazí (např. špatný SQL dotaz, neexistující tabulka…), vyhoď výjimku.“
} catch (PDOException $e) {
    echo "Chyba připojení: " . $e->getMessage();
}

?>
