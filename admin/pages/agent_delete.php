<?php
include '../db.php';
$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM agents WHERE id=?");
    $stmt->execute([$id]);
}
header("Location: ../index.php?page=agents");
exit;
