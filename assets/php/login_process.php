<?php
// assets/php/login_process.php
require_once 'db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        header("Location: ../../admin/login.html?error=empty");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Success!
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header("Location: ../../admin/dashboard.php");
            exit;
        } else {
            // Invalid credentials
            header("Location: ../../admin/login.html?error=invalid");
            exit;
        }
    } catch (PDOException $e) {
        // Database error
        header("Location: ../../admin/login.html?error=db");
        exit;
    }
} else {
    header("Location: ../../admin/login.html");
    exit;
}
