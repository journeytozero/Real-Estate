<?php
ob_start();

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Profile info fallback
$profilePhoto = $_SESSION['admin_photo'] ?? 'default.png';
$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Determine current page for active link highlighting
$current_page = $_GET['page'] ?? 'dashboard';

// Helper function for active class
function isActive($page, $current_page){
    return $page === $current_page ? 'active bg-secondary' : '';
}
?>

<!-- Sidebar -->
<div class="d-flex flex-column bg-dark text-white p-3 vh-100" id="sidebar" style="width: 250px;">
    
    <!-- Admin Info -->
    <div class="text-center mb-4">
        <img src="../admin/uploads/<?= htmlspecialchars($profilePhoto) ?>" 
     class="rounded-circle border border-2 mb-2" 
     width="80" height="80" alt="Admin Photo">

        <h6 class="mt-2"><?= htmlspecialchars($adminName) ?></h6>
        <span class="text-primary small">Administrator</span>
        <hr class="bg-secondary">
    </div>

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="idx.php?page=dashboard" class="nav-link text-white <?= isActive('dashboard', $current_page) ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=properties" class="nav-link text-white <?= isActive('properties', $current_page) ?>">
                <i class="bi bi-building me-2"></i> Properties
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=agents" class="nav-link text-white <?= isActive('agents', $current_page) ?>">
                <i class="bi bi-person me-2"></i> Agents
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=clients" class="nav-link text-white <?= isActive('clients', $current_page) ?>">
                <i class="bi bi-people me-2"></i> Clients
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=transactions" class="nav-link text-white <?= isActive('transactions', $current_page) ?>">
                <i class="bi bi-cash-coin me-2"></i> Transactions
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=reports" class="nav-link text-white <?= isActive('reports', $current_page) ?>">
                <i class="bi bi-graph-up me-2"></i> Reports
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=settings" class="nav-link text-white <?= isActive('settings', $current_page) ?>">
                <i class="bi bi-gear me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=property_categories" class="nav-link text-white <?= isActive('property_categories', $current_page) ?>">
                <i class="bi bi-tags me-2"></i> Categories
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=chat" class="nav-link text-white <?= isActive('chat', $current_page) ?>">
                <i class="bi bi-chat-dots me-2"></i> Chat
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=blog" class="nav-link text-white <?= isActive('blog', $current_page) ?>">
                <i class="bi bi-journal-text me-2"></i> Blog
            </a>
        </li>
        <li class="nav-item">
            <a href="idx.php?page=concern_groups" class="nav-link text-white <?= isActive('blog', $current_page) ?>">
                <i class="bi bi-node-plus"></i> Concern
            </a>
        </li>
    </ul>

    <!-- Logout at bottom -->
    <div class="mt-auto text-center">
        <a href="logout.php" class="btn btn-danger w-100 mt-3">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
    </div>
</div>

<!-- Main Content Start -->
<div class="flex-grow-1 p-3" id="page-content">
