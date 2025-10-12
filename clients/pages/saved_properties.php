<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Only allow logged-in clients
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

$clientId = (int)$_SESSION['client_id'];

// ✅ Handle remove (unsave)
if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    if ($removeId > 0) {
        $del = $conn->prepare("DELETE FROM saved_properties WHERE client_id = ? AND property_id = ?");
        $del->execute([$clientId, $removeId]);
    }
    header("Location: saved_properties.php");
    exit;
}

// ✅ Fetch saved properties
$sql = "
    SELECT p.id, p.name, p.location, p.price, p.status, ph.photo
    FROM saved_properties sp
    JOIN properties p ON sp.property_id = p.id
    LEFT JOIN (
        SELECT property_id, MIN(id) AS first_photo
        FROM property_photos GROUP BY property_id
    ) x ON p.id = x.property_id
    LEFT JOIN property_photos ph ON ph.id = x.first_photo
    WHERE sp.client_id = ?
    ORDER BY sp.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute([$clientId]);
$saved = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h4 class="mb-4">My Saved Properties</h4>
    <div class="row">
        <?php if ($saved): ?>
            <?php foreach ($saved as $prop): ?>
                <?php
                    $img = $prop['photo']
                        ? "../../admin/uploads/" . $prop['photo']
                        : "https://via.placeholder.com/400x200?text=No+Image";

                    $cls = match ($prop['status']) {
                        'available' => 'success',
                        'pending'   => 'warning',
                        'rented'    => 'info',
                        'sold'      => 'danger',
                        default     => 'secondary'
                    };
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?= htmlspecialchars($img) ?>" 
                             alt="Property Image" 
                             class="card-img-top" 
                             style="height:200px;object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($prop['name']) ?></h5>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> 
                                <?= htmlspecialchars($prop['location']) ?>
                            </p>
                            <p class="fw-bold text-primary">
                                <?= number_format($prop['price']) ?> TK
                            </p>
                            <p>
                                <strong>Status:</strong> 
                                <span class="badge bg-<?= $cls ?>">
                                    <?= ucfirst($prop['status']) ?>
                                </span>
                            </p>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="property_detail.php?id=<?= $prop['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">View</a>
                                <a href="?remove=<?= $prop['id'] ?>" 
                                   onclick="return confirm('Remove from saved list?');"
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i> Remove
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">
                You haven’t saved any properties yet. 
                Browse the <a href="properties.php">property list</a> to add favorites.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
