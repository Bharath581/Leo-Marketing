<?php
// admin/delete_message.php
require_once '../assets/php/auth_check.php';
require_once '../assets/php/db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        // Handle error silently or log
    }
}

header("Location: dashboard.php");
exit;
