<?php
$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id=?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "❌ Transaction deleted!";
}

header("Location: index.php?page=transactions");
exit;
