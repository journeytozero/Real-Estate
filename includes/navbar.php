<?php
require_once __DIR__ . "/header.php";

// Include unified login/register modals (modals.php handles AJAX and session safely)
require_once __DIR__ . "/modals.php";

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Top Contact Bar (Optional, uncomment if needed) -->
<!--
<div class="bg-light py-2 d-none d-lg-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="contact-info">
                    <span class="me-4"><i class="fas fa-phone me-1"></i> (555) 123-4567</span>
                    <span class="me-4"><i class="fas fa-envelope me-1"></i> info@dreamhomes.com</span>
                    <span><i class="fas fa-map-marker-alt me-1"></i> 123 Main St, City, State 12345</span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="#" class="text-decoration-none me-3"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-decoration-none me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-decoration-none me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-decoration-none"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
</div>
-->

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="fas fa-home me-2"></i> DreamHomes
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto flex-nowrap text-nowrap">
                <li class="nav-item"><a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>"
                        href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page === 'all_properties.php' ? 'active' : '' ?>"
                        href="all_properties.php">All Properties</a></li>

                <!-- Properties Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="propertiesDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Properties
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="propertiesDropdown">
                        <li><a class="dropdown-item" href="properties.php">Search Properties</a></li>
                        <li><a class="dropdown-item" href="#">Residential</a></li>
                        <li><a class="dropdown-item" href="#">Commercial</a></li>
                        <li><a class="dropdown-item" href="#">Industrial</a></li>
                    </ul>
                </li>

                <!-- Services Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="buy.php">Buy</a></li>
                        <li><a class="dropdown-item" href="sell.php">Sell</a></li>
                        <li><a class="dropdown-item" href="rent.php">Rent</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>"
                        href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page === 'agents.php' ? 'active' : '' ?>"
                        href="agents.php">Agents</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page === 'blog.php' ? 'active' : '' ?>"
                        href="blog.php">Blog</a></li>
            </ul>

            <!-- Right-side Buttons -->
            <div class="d-flex align-items-center gap-2 ms-3">
                <!-- Login Button -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fas fa-user me-1"></i> Login
                </button>

                <!-- Contact Us Button -->
                <a href="contact.php" class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-1"></i> Contact Us
                </a>
            </div>
        </div>
    </div>
</nav>
