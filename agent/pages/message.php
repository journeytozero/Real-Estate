<?php
session_start();
require_once __DIR__ . "/admin/partials/db.php";

// নিশ্চিত করুন এজেন্ট লগইন আছে
if (!isset($_SESSION['agent_id'])) {
    die("You must be logged in as an agent to view messages.");
}

$agent_id = $_SESSION['agent_id'];

// প্রপার্টি আইডি নিন (optional, সব প্রপার্টির মেসেজ দেখাতে চাইলে null রাখুন)
$property_id = $_GET['property_id'] ?? null;

// বেসিক কোয়েরি
$sql = "
    SELECT m.id, m.message, m.created_at, p.name AS property_name, u.name AS client_name, u.email AS client_email
    FROM messages m
    INNER JOIN properties p ON m.property_id = p.id
    INNER JOIN users u ON m.client_id = u.id
    WHERE m.agent_id = :agent_id
";

// প্রপার্টি আইডি দিলে WHERE এ যোগ করুন
$params = [':agent_id' => $agent_id];

if ($property_id) {
    $sql .= " AND m.property_id = :property_id";
    $params[':property_id'] = $property_id;
}

$sql .= " ORDER BY m.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Agent Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
    <h2>Your Messages</h2>

    <?php if (count($messages) === 0): ?>
        <div class="alert alert-info">No messages found.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped bg-white shadow-sm">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Message</th>
                    <th>Received At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['property_name']) ?></td>
                        <td><?= htmlspecialchars($msg['client_name']) ?></td>
                        <td><?= htmlspecialchars($msg['client_email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                        <td><?= htmlspecialchars($msg['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="agent_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
