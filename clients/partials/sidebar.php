<?php
$client_name = $_SESSION['client_name'] ?? 'Client';
$client_photo = $_SESSION['client_photo'] ?? 'https://via.placeholder.com/80?text=👤';
$current_page = basename($_SERVER['PHP_SELF']);

$menu = [
  ['Dashboard', 'dashboard.php', 'fa-home'],
  ['My Properties', 'properties.php', 'fa-search'],
  ['Saved Properties', 'saved_properties.php', 'fa-heart'],
  ['Rented', 'rented.php', 'fa-house-user'],
  ['Transactions', 'transaction.php', 'fa-file-invoice-dollar'],
  ['Payments', 'payments.php', 'fa-solid fa-credit-card'],
  ['Contact', 'chat.php', 'fa-headset'],
  ['Profile', 'profile.php', 'fa-user-cog']
];

// Build profile photo path: use absolute URL if provided, otherwise use local upload path.
// The local path is relative to the sidebar file (../uploads/clients/<filename>).
$photo_path = filter_var($client_photo, FILTER_VALIDATE_URL)
    ? $client_photo
    : '../uploads/clients/' . $client_photo;
?>

<nav id="sidebar" class="sidebar bg-dark text-white">
  <div class="text-center py-4">
    <img src="<?= $photo_path ?>" alt="Profile" class="rounded-circle mb-2" width="80" height="80" />
    <h6><?= htmlspecialchars($client_name) ?></h6>
  </div>

  <ul class="nav flex-column px-3">
    <?php foreach ($menu as [$label, $link, $icon]): 
      $active = ($current_page === $link) ? 'active' : '';
    ?>
      <li class="nav-item mb-1">
        <a href="<?= htmlspecialchars($link) ?>" class="nav-link text-white <?= $active ?>">
          <i class="fas <?= $icon ?> me-2"></i> <?= $label ?>
        </a>
      </li>
    <?php endforeach; ?>
    <li class="nav-item mt-auto">
      <a href="/RS/idx.php" class="nav-link text-danger">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
      </a>
    </li>
  </ul>
</nav>
