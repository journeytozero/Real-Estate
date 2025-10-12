<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";
require_once __DIR__ . "/../includes/navbar.php";

// ✅ Require login
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];

// ✅ Fetch ongoing (Pending) properties
$stmt = $conn->prepare("
    SELECT p.*, c.name AS category 
    FROM properties p
    LEFT JOIN property_categories c ON p.category_id = c.id
    WHERE p.agent_id=? AND p.status='Pending'
    ORDER BY p.created_at DESC
");
$stmt->execute([$agent_id]);
$ongoing_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2 class="mb-4">Ongoing Projects</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Posted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ongoing_properties): ?>
                        <?php foreach ($ongoing_properties as $prop): ?>
                            <tr>
                                <td><?= htmlspecialchars($prop['name']) ?></td>
                                <td><?= htmlspecialchars($prop['category'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($prop['location']) ?></td>
                                <td>$<?= number_format($prop['price'], 2) ?></td>
                                <td><?= date("M d, Y", strtotime($prop['created_at'])) ?></td>
                                <td>
                                    <a href="edit_property.php?id=<?= $prop['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="delete_property.php?id=<?= $prop['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this property?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No ongoing projects found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
