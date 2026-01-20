<?php
// assets/php/newsletter_submit.php
require_once __DIR__ . '/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Error handling
    echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO newsletter_subscribers (email) VALUES (:email)");
    $stmt->execute([':email' => $email]);

    // Redirect back to home with success flag
    header("Location: ../../index.html?newsletter=success");
    exit;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
