<?php
session_start();

// ✅ Only allow logged-in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Include essential partials
require_once __DIR__ . "/partials/db.php";
require_once __DIR__ . "/partials/header.php";
require_once __DIR__ . "/partials/sidebar.php";

// -----------------------------
// Load dynamic page
// -----------------------------
$page = $_GET['page'] ?? 'dashboard';

// Sanitize page parameter to prevent directory traversal
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

$pageFile = __DIR__ . "/pages/{$page}.php";

// Display page if exists, otherwise show 404 message
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    echo "<div class='p-4'><h3 class='text-danger'>Page not found</h3></div>";
}

// Include footer
require_once __DIR__ . "/partials/footer.php";
