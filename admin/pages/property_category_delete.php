<?php
require_once __DIR__ . "/../partials/db.php";

$id = $_GET['id'] ?? null;

if ($id) {
    // fetch category to get photo
    $stmt = $conn->prepare("SELECT photo FROM property_categories WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // delete photo file if exists
        if (!empty($row['photo']) && file_exists(__DIR__ . "/../uploads/" . $row['photo'])) {
            unlink(__DIR__ . "/../uploads/" . $row['photo']);
        }

        // delete db record
        $del = $conn->prepare("DELETE FROM property_categories WHERE id=?");
        $del->execute([$id]);

        $_SESSION['success'] = "Category deleted successfully.";
    } else {
        $_SESSION['error'] = "Category not found.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: idx.php?page=property_categories");
exit;
