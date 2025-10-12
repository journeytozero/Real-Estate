<?php
require_once __DIR__ . "/../partials/db.php";

$id = $_GET['id'] ?? null;

if ($id) {
    // ✅ Fetch all photos before deleting
    $photoStmt = $conn->prepare("SELECT photo FROM property_photos WHERE property_id=?");
    $photoStmt->execute([$id]);
    $photos = $photoStmt->fetchAll(PDO::FETCH_COLUMN);

    // ✅ Delete photo files from uploads folder
    $uploadDir = __DIR__ . "/../uploads/";
    foreach ($photos as $file) {
        $filePath = $uploadDir . $file;
        if (is_file($filePath)) {
            unlink($filePath); // delete file
        }
    }

    // ✅ Delete photos from DB
    $delPhotos = $conn->prepare("DELETE FROM property_photos WHERE property_id=?");
    $delPhotos->execute([$id]);

    // ✅ Delete the property
    $stmt = $conn->prepare("DELETE FROM properties WHERE id=?");
    $stmt->execute([$id]);

    $msg = "Property and related photos deleted successfully";
} else {
    $msg = "Invalid property ID";
}

header("Location: ../idx.php?page=properties&msg=" . urlencode($msg));
exit;
