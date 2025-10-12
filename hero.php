<?php
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";

?>

<!-- Hero Section with Background Video -->
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



<?php if (isset($_SESSION['register_success'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
  });
</script>
<?php endif; ?>

<?php
//require_once __DIR__ . "/includes/footer.php";
?>