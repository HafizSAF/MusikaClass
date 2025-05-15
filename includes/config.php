<?php
$host = 'localhost';
$dbname = 'musik_db'; // Nama database Anda
$username = 'root'; // Username default XAMPP
$password = ''; // Password default XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>