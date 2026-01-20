<?php
// assets/php/config.php

$host     = "localhost";      // Usually 'localhost'
$dbname   = "leomarketing";   // Same as created above
$username = "root";           // Your MySQL username
$password = "";               // Your MySQL password (often empty on localhost)

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // In production, don't echo full error; maybe log it instead.
    die("Database connection failed: " . $e->getMessage());
}
