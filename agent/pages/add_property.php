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
$message = "";

// ✅ Fetch property categories
$catStmt = $conn->query("SELECT id, name FROM property_categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name       = trim($_POST['name']);
    $location   = trim($_POST['location']);
    $price      = trim($_POST['price']);
    $status     = trim($_POST['status']);
    $category   = $_POST['category'] ?? null;
    $photoPath  = null;

    // ✅ Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = __DIR__ . "/../uploads/properties/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = "uploads/properties/" . $fileName;
        }
    }

    // ✅ Insert into DB
    $stmt = $conn->prepare("
        INSERT INTO properties (agent_id, category_id, name, location, price, status, photo, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$agent_id, $category, $name, $location, $price, $status, $photoPath]);

    $message = "✅ Property added successfully!";
}
?>

<div class="content">
    <h2 class="mb-4">Add Property</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <!-- Property Name -->
                <div class="mb-3">
                    <label class="form-label">Property Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Location -->
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Available">Available</option>
                        <option value="Pending">Pending</option>
                        <option value="Sold">Sold</option>
                        <option value="Rented">Rented</option>
                    </select>
                </div>

                <!-- Photo -->
                <div class="mb-3">
                    <label class="form-label">Property Photo</label>
                    <input type="file" name="photo" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Add Property</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
