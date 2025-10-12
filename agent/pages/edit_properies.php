<?php
session_start();
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}
require_once "../db.php";

$id = $_GET['id'] ?? null;
$agent_id = $_SESSION['agent_id'];

if (!$id) {
    header("Location: my_properties.php");
    exit;
}

// Fetch property
$stmt = $conn->prepare("SELECT * FROM properties WHERE id=? AND agent_id=?");
$stmt->execute([$id, $agent_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header("Location: my_properties.php");
    exit;
}

// Fetch categories
$catStmt = $conn->query("SELECT id, name FROM property_categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch property photos
$photoStmt = $conn->prepare("SELECT * FROM property_photos WHERE property_id=?");
$photoStmt->execute([$id]);
$photos = $photoStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle property update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_property'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE properties 
                            SET name=?, category_id=?, location=?, price=?, status=?, description=? 
                            WHERE id=? AND agent_id=?");
    $stmt->execute([$name, $category_id, $location, $price, $status, $description, $id, $agent_id]);

    header("Location: edit_property.php?id=$id&updated=1");
    exit;
}

// Handle photo upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_photo'])) {
    if (!empty($_FILES['photo']['name'])) {
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetDir = "../uploads/";
        $targetFile = $targetDir . $fileName;

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt) && move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO property_photos (property_id, photo) VALUES (?, ?)");
            $stmt->execute([$id, $fileName]);
            header("Location: edit_property.php?id=$id&photo_added=1");
            exit;
        }
    }
}

// Handle photo delete
if (isset($_GET['delete_photo'])) {
    $photo_id = $_GET['delete_photo'];

    $stmt = $conn->prepare("SELECT photo FROM property_photos WHERE id=? AND property_id=?");
    $stmt->execute([$photo_id, $id]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($photo) {
        $filePath = "../uploads/" . $photo['photo'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $delStmt = $conn->prepare("DELETE FROM property_photos WHERE id=? AND property_id=?");
        $delStmt->execute([$photo_id, $id]);
    }
    header("Location: edit_property.php?id=$id&photo_deleted=1");
    exit;
}
?>
<?php include "../header.php"; ?>
<?php include "../sidebar.php"; ?>

<div class="content">
    <h2 class="mb-4">Edit Property</h2>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Property updated successfully!</div>
    <?php elseif (isset($_GET['photo_added'])): ?>
        <div class="alert alert-info">Photo uploaded successfully!</div>
    <?php elseif (isset($_GET['photo_deleted'])): ?>
        <div class="alert alert-danger">Photo deleted successfully!</div>
    <?php endif; ?>

    <!-- Update Property Form -->
    <form method="POST" class="card p-4 shadow-sm mb-4">
        <input type="hidden" name="update_property" value="1">

        <div class="mb-3">
            <label class="form-label">Property Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($property['name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $property['category_id']==$cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($property['location']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" value="<?= $property['price'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="Available" <?= $property['status']=='Available'?'selected':'' ?>>Available</option>
                <option value="Pending" <?= $property['status']=='Pending'?'selected':'' ?>>Pending</option>
                <option value="Sold" <?= $property['status']=='Sold'?'selected':'' ?>>Sold</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($property['description']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Property</button>
        <a href="my_properties.php" class="btn btn-secondary">Cancel</a>
    </form>

    <!-- Manage Photos -->
    <div class="card p-4 shadow-sm">
        <h4>Property Photos</h4>
        <div class="row">
            <?php foreach ($photos as $photo): ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <img src="../uploads/<?= htmlspecialchars($photo['photo']) ?>" class="card-img-top" alt="Property Photo">
                        <div class="card-body text-center">
                            <a href="edit_property.php?id=<?= $id ?>&delete_photo=<?= $photo['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this photo?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="upload_photo" value="1">
            <div class="mb-3">
                <label class="form-label">Upload New Photo</label>
                <input type="file" name="photo" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Upload</button>
        </form>
    </div>
</div>

<?php include "../footer.php"; ?>
