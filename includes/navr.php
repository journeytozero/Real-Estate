<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/login_modal.php";
require_once __DIR__ . "/register_modal.php";

// Detect current page
$current_page = basename($_SERVER['PHP_SELF']);

// Check login status
$isLoggedIn = false;
$userName = '';
$userRole = '';

if (isset($_SESSION['user_role'])) {
    $isLoggedIn = true;
    $userRole = $_SESSION['user_role'];
    if ($userRole === 'admin') $userName = $_SESSION['admin_name'] ?? '';
    elseif ($userRole === 'agent') $userName = $_SESSION['agent_name'] ?? '';
    elseif ($userRole === 'buyer') $userName = $_SESSION['client_name'] ?? '';
}
?>

<!-- Main navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="fas fa-home me-2"></i> DreamHomes
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto flex-nowrap text-nowrap">
                <li class="nav-item"><a class="nav-link <?= $current_page==='index.php'?'active':'' ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page==='all_properties.php'?'active':'' ?>" href="all_properties.php">All Properties</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Properties</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="properties.php">Search Properties</a></li>
                        <li><a class="dropdown-item" href="#">Residential</a></li>
                        <li><a class="dropdown-item" href="#">Commercial</a></li>
                        <li><a class="dropdown-item" href="#">Industrial</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Services</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="buy.php">Buy</a></li>
                        <li><a class="dropdown-item" href="sell.php">Sell</a></li>
                        <li><a class="dropdown-item" href="rent.php">Rent</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link <?= $current_page==='about.php'?'active':'' ?>" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page==='agents.php'?'active':'' ?>" href="agents.php">Agents</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page==='blog.php'?'active':'' ?>" href="blog.php">Blog</a></li>
            </ul>

            <!-- Right-side Buttons -->
            <div class="d-flex align-items-center gap-2 ms-3">
                <?php if($isLoggedIn): ?>
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i> <?= htmlspecialchars($userName) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <?php if($userRole==='admin'): ?>
                                <li><a class="dropdown-item" href="/RS/admin/idx.php">Dashboard</a></li>
                            <?php elseif($userRole==='agent'): ?>
                                <li><a class="dropdown-item" href="/RS/agent/pages/dashboard.php">Dashboard</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/RS/clients/dashboard.php">Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/RS/includes/logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Login Button -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-user me-1"></i> Login
                    </button>
                    <!-- Contact Us Button -->
                    <a href="contact.php" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i> Contact Us
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
