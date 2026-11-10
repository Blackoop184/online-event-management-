<?php
// db.php - PDO database connection with prepared statements support
// All database interactions in this project use PDO prepared statements
// to prevent SQL injection (Security Requirement 5a).

$host = 'localhost';
$dbname = 'event_management';
$username = 'root';   // Change to your MySQL username
$password = '';       // Change to your MySQL password

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    // Throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch results as associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Enable emulated prepares off for real server-side prepared statements
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // In production, log this instead of showing the raw error
    die("Database connection failed: " . $e->getMessage());
}
?>