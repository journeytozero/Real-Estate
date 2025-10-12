<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM clients WHERE id=?");
    $stmt->execute([$id]);
    session_start();
    $_SESSION['success'] = "🗑️ Client deleted!";
}
header("Location: index.php?page=clients");
exit;
