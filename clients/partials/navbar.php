<?php
// Client info
$user_name  = $_SESSION['client_name'] ?? 'Guest';
$user_photo = $_SESSION['client_photo'] ?? 'https://via.placeholder.com/36?text=👤';

// Resolve user photo: if URL use as-is, otherwise use local upload path (relative to navbar file)
if (!filter_var($user_photo, FILTER_VALIDATE_URL)) {
    $user_photo = '../uploads/clients/' . $user_photo;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-outline-primary d-lg-none me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-home me-2"></i> <?= htmlspecialchars("My Properties") ?>
        </a>

        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= htmlspecialchars($user_photo) ?>" alt="Profile" class="rounded-circle me-2" width="36" height="36" />
                    <span><?= htmlspecialchars($user_name) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("sidebar-collapsed");
    });
});
</script>
