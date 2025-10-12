<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['agent_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$agent_id = $_SESSION['agent_id'];
$stmt = $conn->prepare("SELECT name, photo FROM agents WHERE id = ?");
$stmt->execute([$agent_id]);
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

$agent_name = htmlspecialchars($agent['name'] ?? 'Agent');
$agent_photo = !empty($agent['photo']) 
    ? (str_starts_with($agent['photo'], 'uploads/') 
        ? "../" . htmlspecialchars($agent['photo']) 
        : "../uploads/agents/" . htmlspecialchars($agent['photo']))
    : "https://via.placeholder.com/80";

$current_page = basename($_SERVER['PHP_SELF']);
function isActive($page, $current_page){
    return $page === $current_page ? 'active' : '';
}
?>

<div class="sidebar d-flex flex-column p-3 text-white bg-dark" style="width: 250px; min-height: 100vh;">
    <!-- Agent Info -->
    <div class="text-center mb-4">
        <img src="<?= $agent_photo ?>" class="rounded-circle mb-2" width="80" height="80" alt="Agent Photo">
        <h5><?= $agent_name ?></h5>
    </div>

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="dashboard.php" class="nav-link text-white <?= isActive('dashboard.php', $current_page) ?>"><i class="fas fa-home me-2"></i> Dashboard</a></li>
        <li><a href="my_properties.php" class="nav-link text-white <?= isActive('my_properties.php', $current_page) ?>"><i class="fas fa-building me-2"></i> My Properties</a></li>
        <li><a href="add_property.php" class="nav-link text-white <?= isActive('add_property.php', $current_page) ?>"><i class="fas fa-plus-circle me-2"></i> Add Property</a></li>
        <li><a href="clients.php" class="nav-link text-white <?= isActive('clients.php', $current_page) ?>"><i class="fas fa-users me-2"></i> My Clients</a></li>
        <li><a href="ongoing.php" class="nav-link text-white <?= isActive('ongoing.php', $current_page) ?>"><i class="fas fa-tasks me-2"></i> Ongoing</a></li>
        <li><a href="rented.php" class="nav-link text-white <?= isActive('rented.php', $current_page) ?>"><i class="fas fa-handshake me-2"></i> Rented</a></li>
        <li><a href="add_rented.php" class="nav-link text-white <?= isActive('add_rented.php', $current_page) ?>"><i class="fas fa-plus-circle me-2"></i> Add Rented</a></li>
        
        <!-- 🔹 New Rental Features -->
        <li><a href="rental_summary.php" class="nav-link text-white <?= isActive('rental_summary.php', $current_page) ?>"><i class="fas fa-chart-line me-2"></i> Rental Summary</a></li>
        <li><a href="create_contract.php" class="nav-link text-white <?= isActive('create_contract.php', $current_page) ?>"><i class="fas fa-file-signature me-2"></i> Create Contract</a></li>
        
        <li><a href="transactions.php" class="nav-link text-white <?= isActive('transactions.php', $current_page) ?>"><i class="fas fa-file-invoice-dollar me-2"></i> Transactions</a></li>
        <li><a href="chat.php" class="nav-link text-white <?= isActive('chat.php', $current_page) ?>"><i class="fas fa-comments me-2"></i> Chat</a></li>
        <li><a href="settings.php" class="nav-link text-white <?= isActive('settings.php', $current_page) ?>"><i class="fas fa-cog me-2"></i> Settings</a></li>
        
        <li class="mt-3"><a href="../../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>
