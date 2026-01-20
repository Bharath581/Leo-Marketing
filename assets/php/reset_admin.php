<?php
// assets/php/reset_admin.php
require_once 'db_config.php';

$new_password = 'admin123';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$username = 'admin';

try {
    // Check if user exists
    $check = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $check->execute([':username' => $username]);

    if ($check->rowCount() > 0) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE username = :username");
        $stmt->execute([':hash' => $new_hash, ':username' => $username]);
        echo "<h1>SUCCESS!</h1><p>Admin password reset to: <strong>$new_password</strong></p>";
    } else {
        // Create user if missing
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :hash)");
        $stmt->execute([':username' => $username, ':hash' => $new_hash]);
        echo "<h1>SUCCESS!</h1><p>Admin user created with password: <strong>$new_password</strong></p>";
    }
    echo "<p><a href='../../admin/login.html'>Go to Login Page</a></p>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
