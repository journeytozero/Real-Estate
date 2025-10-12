<?php
session_start(); // Always start session first
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";

// Hero Section

// Modals
require_once __DIR__. '/includes/modals.php';
// Main content
?>

<section class="hero position-relative">
  <!-- Background Video -->
  <video autoplay muted loop playsinline class="w-100 vh-100 object-fit-cover">
    <source src="assets/videos/hero.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
  </video>

  <!-- Overlay -->
  <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

  <!-- Content -->
  <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
    <h1 class="display-3 fw-bold">Find Your Dream Home</h1>
    <p class="lead mb-4">Buy, Sell, or Rent properties with ease</p>
    <a href="categories.php" class="btn btn-primary btn-lg me-2">
      <i class="fas fa-search me-1"></i> Browse Properties
    </a>

  </div>
</section>

<?php
require_once __DIR__ . "/categories.php";
require_once __DIR__ . "/all_properties.php";
require_once __DIR__ . "/agents.php";
require __DIR__ . "/blog.php";
require __DIR__ . "/concern.php";
require_once __DIR__ . "/contact.php";

require __DIR__ . "/includes/footer.php";

?>
