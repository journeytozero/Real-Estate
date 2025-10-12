<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$clientId = (int)($_GET['client_id'] ?? 0);
$agentId  = (int)($_GET['agent_id'] ?? 0);

if ($clientId > 0 && $agentId > 0) {
    $stmt = $conn->prepare("
        SELECT sender, message, created_at
        FROM chat_messages
        WHERE client_id = ? AND agent_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$clientId, $agentId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
