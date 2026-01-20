<?php
// assets/php/mark_read.php
require_once 'auth_check.php';
require_once 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && isset($data['read'])) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = :read WHERE id = :id");
            $stmt->execute([
                ':read' => $data['read'] ? 1 : 0,
                ':id' => $data['id']
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
