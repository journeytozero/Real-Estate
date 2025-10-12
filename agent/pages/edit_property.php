<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";
require_once __DIR__ . "/../includes/navbar.php";

// Require login
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];
$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    header("Location: properties.php");
    exit;
}

// Fetch property
$stmt = $conn->prepare("SELECT * FROM properties WHERE id=? AND agent_id=?");
$stmt->execute([$property_id, $agent_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    $_SESSION['error'] = "Property not found.";
    header("Location: properties.php");
    exit;
}

// Fetch categories
$categories = $conn->query("SELECT id, name FROM property_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE properties SET name=?, location=?, price=?, category_id=?, status=? WHERE id=? AND agent_id=?");
    $stmt->execute([$name, $location, $price, $category_id, $status, $property_id, $agent_id]);

    $_SESSION['success'] = "Property updated successfully!";
    header("Location: my_properties.php");
    exit;
}
?>

<div class="content">
    <h2 class="mb-4">Edit Property</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="POST">

                <div class="mb-3">
                    <label class="form-label">Property Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($property['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($property['location']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($property['price']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $property['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <?php 
                        $statuses = ['Available','Ongoing','Rented','Sold'];
                        foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $property['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update Property</button>
                <a href="my_properties.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
