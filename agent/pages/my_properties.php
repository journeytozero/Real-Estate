<?php
session_start();
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}
require_once __DIR__ . "/../config/db.php";

$agent_id = $_SESSION['agent_id'];

// ✅ Fetch properties for this agent
$stmt = $conn->prepare("SELECT p.*, c.name AS category 
                        FROM properties p
                        LEFT JOIN property_categories c ON p.category_id = c.id
                        WHERE p.agent_id = ?
                        ORDER BY p.created_at DESC");
$stmt->execute([$agent_id]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
require_once __DIR__ . "/../includes/header.php"; 
?>
<?php require_once __DIR__ . "/../includes/sidebar.php"; ?>

<div class="content">
    <h2 class="mb-4">My Properties</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Property added successfully!</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-info">Property updated successfully!</div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Price</th>
                <th>Status</th>
                <th>Posted</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($properties): ?>
                <?php foreach ($properties as $prop): ?>
                <tr>
                    <td><?= htmlspecialchars($prop['name']) ?></td>
                    <td><?= htmlspecialchars($prop['category'] ?? 'Uncategorized') ?></td>
                    <td><?= htmlspecialchars($prop['location']) ?></td>
                    <td>$<?= number_format($prop['price'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $prop['status'] == 'Available' ? 'success' :
                            ($prop['status'] == 'Pending' ? 'warning' : 'danger')
                        ?>">
                            <?= htmlspecialchars($prop['status']) ?>
                        </span>
                    </td>
                    <td><?= date("M d, Y", strtotime($prop['created_at'])) ?></td>
                    <td>
                        <a href="edit_property.php?id=<?= $prop['id'] ?>" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No properties found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__. "/../includes/footer.php"; ?>
