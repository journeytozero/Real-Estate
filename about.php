<?php
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";
?>

<!-- Hero Section -->
<section class="hero-about d-flex align-items-center text-center text-white">
  <div class="container">
    <h1 class="display-4 fw-bold hero-title">About Our Khan Real Estate Agency</h1>
    <p class="lead mt-3 hero-subtitle">
      <span class="typed-text"></span>
    </p>
  </div>
</section>

<!-- About Company Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6 animate__animated animate__fadeInLeft">
        <img src="../RS/assets/photos/modern-minimalist-office.jpg" alt="Our Office" class="img-fluid rounded shadow">
      </div>
      <div class="col-lg-6 animate__animated animate__fadeInRight">
        <h2 class="fw-bold mb-3">Who We Are</h2>
        <p>
          We are a professional real estate agency dedicated to helping clients buy, sell, and rent properties efficiently.
          Our experienced team ensures transparency and personalized support every step of the way.
        </p>
        <ul class="list-unstyled mt-3">
          <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Trusted by hundreds of clients</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Experienced and professional agents</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Transparent process with fair pricing</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-6 animate__animated animate__zoomIn">
        <div class="card mission-card shadow border-0 h-100 p-4 text-center">
          <i class="bi bi-bullseye display-4 gradient-icon mb-3"></i>
          <h3 class="fw-bold mb-2">Our Mission</h3>
          <p>To connect people with their ideal properties through exceptional service and expert guidance.</p>
        </div>
      </div>
      <div class="col-md-6 animate__animated animate__zoomIn animate__delay-1s">
        <div class="card mission-card shadow border-0 h-100 p-4 text-center">
          <i class="bi bi-eye display-4 gradient-icon mb-3"></i>
          <h3 class="fw-bold mb-2">Our Vision</h3>
          <p>To be the most trusted and innovative real estate agency, making property dreams come true globally.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Team Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold mb-5">Meet Our Team</h2>
    <div class="row g-4">
      <?php
      $stmt = $conn->query("SELECT * FROM agents ORDER BY id ASC LIMIT 3");
      $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($agents as $agent):
        $photo = !empty($agent['photo']) ? "admin/uploads/agents/" . htmlspecialchars($agent['photo']) : "https://via.placeholder.com/250";
      ?>
      <div class="col-md-4 animate__animated animate__fadeInUp">
        <div class="card team-card h-100 border-0 p-3 shadow position-relative overflow-hidden">
          <div class="team-img-wrapper">
            <img src="<?= $photo ?>" alt="<?= htmlspecialchars($agent['name']) ?>" class="team-img rounded-circle mx-auto d-block">
            <div class="gradient-border"></div>
          </div>
          <h5 class="fw-bold mt-3"><?= htmlspecialchars($agent['name']) ?></h5>
          <p class="text-muted mb-1"><?= htmlspecialchars($agent['trade_license'] ?? '-') ?></p>
          <p class="text-primary mb-2"><?= htmlspecialchars($agent['email']) ?></p>
          <div class="d-flex justify-content-center gap-2">
            <?php if (!empty($agent['phone'])): ?>
              <a href="tel:<?= $agent['phone'] ?>" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-phone"></i></a>
              <a href="https://wa.me/<?= preg_replace('/\D/', '', $agent['phone']) ?>" target="_blank" class="btn btn-outline-success btn-sm"><i class="fa-brands fa-whatsapp"></i></a>
            <?php endif; ?>
            <a href="mailto:<?= $agent['email'] ?>" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-envelope"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . "/includes/footer.php"?>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
  new Typed(".typed-text", {
    strings: [
      "Connecting clients with their dream properties.",
      "Built on professionalism and trust.",
      "Your real estate journey starts with us."
    ],
    typeSpeed: 50,
    backSpeed: 30,
    backDelay: 2000,
    loop: true
  });
});

// Parallax effect for hero text
window.addEventListener("scroll", function(){
  let scrollY = window.scrollY;
  document.querySelector(".hero-title").style.transform = `translateY(${scrollY * 0.2}px)`;
  document.querySelector(".hero-subtitle").style.transform = `translateY(${scrollY * 0.3}px)`;
});
</script>

<!-- Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
/* Hero Section */
.hero-about {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 60vh;
  background: linear-gradient(rgba(13, 110, 253, 0.6), rgba(0, 201, 255, 0.6)),
              url('../RS/assets/photos/licensed-image.jfif') center/cover no-repeat;
  color: #fff;
  text-shadow: 0 3px 15px rgba(0,0,0,0.3);
  overflow: hidden;
}
.hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: 3rem;
  transition: transform 0.2s ease-out;
}
.hero-subtitle {
  font-size: 1.3rem;
  font-weight: 300;
  min-height: 30px;
  transition: transform 0.2s ease-out;
}
.typed-cursor {
  color: #00c9ff;
  font-weight: bold;
  animation: blink 0.7s infinite;
}
@keyframes blink { 50% { opacity: 0; } }

/* Mission & Vision Cards */
.mission-card {
  transition: transform 0.5s ease, box-shadow 0.5s ease;
  border-radius: 1rem;
  background: #fff;
  padding: 2rem 1rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}
.mission-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 45px rgba(0,0,0,0.15);
}
.gradient-icon {
  background: linear-gradient(135deg,#0d6efd,#00c9ff);
  
}

/* Team Cards */
.team-card {
  position: relative;
  background: #ffffffcc;
  border-radius: 1rem;
  text-align: center;
  padding: 2rem 1rem 1rem;
  transition: transform 0.5s ease, box-shadow 0.5s ease, background 0.5s ease;
  box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}
.team-card:hover {
  transform: translateY(-15px);
  box-shadow: 0 25px 60px rgba(0,0,0,0.18);
  background: #ffffffee;
}

/* Team Image & Border */
.team-img-wrapper {
  position: relative;
  width: 160px;
  height: 160px;
  margin: auto;
  overflow: visible;
}
.team-img {
  width: 160px;
  height: 160px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid transparent;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.team-card:hover .team-img {
  transform: scale(1.08) rotate(1deg);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.gradient-border {
  position: absolute;
  top: -6px;
  left: -6px;
  width: calc(100% + 12px);
  height: calc(100% + 12px);
  border-radius: 50%;
  background: linear-gradient(135deg,#0d6efd,#00c9ff);
  filter: blur(4px);
  opacity: 0.8;
  z-index: -1;
}

/* Agent Info & Buttons */
.team-card h5 {
  color: #0d6efd;
  font-family: 'Poppins', sans-serif;
  margin-top: 1rem;
  font-size: 1.2rem;
}
.team-card p {
  color: #555;
  font-size: 0.95rem;
  margin: 0.3rem 0;
}
.team-card .btn {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}
.team-card .btn:hover {
  transform: scale(1.15) rotate(5deg);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  background-color: #0d6efd;
  color: #fff;
}
</style>
