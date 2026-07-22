<?php
// config/database.php

$host = 'localhost';
$port = '3307';
$dbname = 'english_flashcard';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database does not exist, we will create it in setup.php
    // Here we can just die with an error unless it's a known 'unknown database' error
    if ($e->getCode() == 1049) { // 1049 is 'Unknown database'
        // Allow the script to continue so setup.php can handle creation
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
