<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// Must be logged in
if (!isset($_SESSION['client_id'])) {
    echo "login";
    exit;
}

$clientId = (int)$_SESSION['client_id'];
$propertyId = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;

if ($propertyId <= 0) {
    exit;
}

// Toggle save
$chk = $conn->prepare("SELECT id FROM saved_properties WHERE client_id = ? AND property_id = ?");
$chk->execute([$clientId, $propertyId]);
$exists = $chk->fetchColumn();

if ($exists) {
    $del = $conn->prepare("DELETE FROM saved_properties WHERE client_id = ? AND property_id = ?");
    $del->execute([$clientId, $propertyId]);
    echo "unsaved";
} else {
    $ins = $conn->prepare("INSERT INTO saved_properties (client_id, property_id) VALUES (?, ?)");
    $ins->execute([$clientId, $propertyId]);
    echo "saved";
}
