<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../config/db.php";   // DB connection
require_once __DIR__ . "/../../Auth.php";        // Auth class
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";
require_once __DIR__ . "/../includes/sidebar.php";

// ✅ Require login
if (!isset($_SESSION['agent_id'])) {
    header("Location: /RS/idx.php");  // redirect to login page if not logged in
    exit;
}

$agent_id = $_SESSION['agent_id'];

// ✅ Combined stats query
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) AS properties_count,
        SUM(CASE WHEN status='Sold' THEN 1 ELSE 0 END) AS deals_closed,
        SUM(CASE WHEN status='Rented' THEN 1 ELSE 0 END) AS rented_count,
        SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) AS ongoing_count,
        COALESCE(SUM(CASE WHEN status='Sold' THEN price ELSE 0 END), 0) AS earnings,
        (SELECT COUNT(*) FROM clients WHERE agent_id=?) AS clients_count
    FROM properties
    WHERE agent_id=?
");
$stmt->execute([$agent_id, $agent_id]);
$statsData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// ✅ Recent properties (latest 5)
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.location, p.price, p.status, p.created_at, 
           c.name AS category 
    FROM properties p 
    LEFT JOIN property_categories c ON p.category_id = c.id
    WHERE p.agent_id=? 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$stmt->execute([$agent_id]);
$recent_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Badge mapping
$statusBadge = [
    'Available' => 'success',
    'Pending'   => 'warning',
    'Sold'      => 'danger',
    'Rented'    => 'info'
];
?>

<div class="content">
    <h2 class="mb-4">Agent Dashboard</h2>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <?php
        $stats = [
            ['icon'=>'building', 'color'=>'primary', 'value'=>$statsData['properties_count'] ?? 0, 'label'=>'Properties Listed'],
            ['icon'=>'users', 'color'=>'success', 'value'=>$statsData['clients_count'] ?? 0, 'label'=>'Clients'],
            ['icon'=>'handshake', 'color'=>'warning', 'value'=>$statsData['deals_closed'] ?? 0, 'label'=>'Deals Closed'],
            ['icon'=>'file-invoice-dollar', 'color'=>'danger', 'value'=>"$".number_format($statsData['earnings'] ?? 0,2), 'label'=>'Earnings'],
            ['icon'=>'house-user', 'color'=>'info', 'value'=>$statsData['rented_count'] ?? 0, 'label'=>'Rented'],
            ['icon'=>'tasks', 'color'=>'secondary', 'value'=>$statsData['ongoing_count'] ?? 0, 'label'=>'Ongoing Projects'],
        ];

        foreach ($stats as $s): ?>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card shadow-sm p-3 text-center h-100">
                    <i class="fas fa-<?= $s['icon'] ?> fa-2x text-<?= $s['color'] ?> mb-2"></i>
                    <h5><?= htmlspecialchars($s['value']) ?></h5>
                    <p class="mb-0 text-muted small"><?= htmlspecialchars($s['label']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent Properties -->
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-list me-2"></i> Recent Properties
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Property</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_properties): ?>
                        <?php foreach ($recent_properties as $prop): ?>
                            <tr>
                                <td><?= htmlspecialchars($prop['name']) ?></td>
                                <td><?= htmlspecialchars($prop['category'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($prop['location']) ?></td>
                                <td>$<?= number_format($prop['price'], 2) ?></td>
                                <td>
                                    <?php $badge = $statusBadge[$prop['status']] ?? 'secondary'; ?>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= htmlspecialchars($prop['status']) ?>
                                    </span>
                                </td>
                                <td><?= date("M d, Y", strtotime($prop['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No recent properties found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


