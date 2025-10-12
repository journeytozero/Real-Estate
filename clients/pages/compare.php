<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Redirect unauthenticated users
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get property IDs to compare (?ids=1,2,3)
$ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
$ids = array_filter(array_map('intval', $ids));

$properties = [];
if ($ids) {
    $in  = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "
        SELECT p.id, p.name, p.location, p.price, p.status, p.description,
               a.name AS agent_name,
               ph.photo
        FROM properties p
        LEFT JOIN agents a ON p.agent_id = a.id
        LEFT JOIN (
            SELECT property_id, MIN(id) AS first_photo
            FROM property_photos GROUP BY property_id
        ) x ON p.id = x.property_id
        LEFT JOIN property_photos ph ON ph.id = x.first_photo
        WHERE p.id IN ($in)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute($ids);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h4 class="mb-4">Compare Properties</h4>

    <?php if ($properties): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Feature</th>
                        <?php foreach ($properties as $p): ?>
                            <th>
                                <?= htmlspecialchars($p['name']) ?><br>
                                <img src="<?= $p['photo'] ? '../../admin/uploads/'.$p['photo'] : 'https://via.placeholder.com/200x120?text=No+Image' ?>" 
                                     class="img-fluid rounded" 
                                     style="height:120px;object-fit:cover;">
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Location</th>
                        <?php foreach ($properties as $p): ?>
                            <td><?= htmlspecialchars($p['location']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <?php foreach ($properties as $p): ?>
                            <td class="fw-bold text-primary"><?= number_format($p['price']) ?> TK</td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <?php foreach ($properties as $p): ?>
                            <?php 
                              $cls = $p['status']=='available' ? 'success' :
                                     ($p['status']=='pending' ? 'warning' : 'danger');
                            ?>
                            <td><span class="badge bg-<?= $cls ?>"><?= ucfirst($p['status']) ?></span></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Agent</th>
                        <?php foreach ($properties as $p): ?>
                            <td><?= htmlspecialchars($p['agent_name'] ?? 'N/A') ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <?php foreach ($properties as $p): ?>
                            <td><?= nl2br(htmlspecialchars(substr($p['description'],0,120))) ?>...</td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Actions</th>
                        <?php foreach ($properties as $p): ?>
                            <td>
                                <a href="property_detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                <button class="btn btn-sm btn-outline-danger save-btn" data-id="<?= $p['id'] ?>">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No properties selected for comparison. Go back to <a href="properties.php">Properties</a> and select some.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on("click", ".save-btn", function(){
    let id = $(this).data("id");
    $.post("../save_property.php", { property_id: id }, function(res){
        alert("Property saved!");
    });
});
</script>
