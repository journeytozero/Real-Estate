<?php
require_once __DIR__ . "/../partials/db.php";

$id = $_GET['id'] ?? null;
$property = null;

// ✅ If editing, fetch property
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ✅ Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $price       = $_POST['price'] ?? 0;
    $status      = $_POST['status'] ?? 'Available';
    $category_id = $_POST['category_id'] ?? null;
    $agent_id    = $_POST['agent_id'] ?? null;
    $description = trim($_POST['description'] ?? '');

    if ($id) {
        $stmt = $conn->prepare("UPDATE properties 
                                SET category_id=?, name=?, location=?, agent_id=?, price=?, status=?, description=? 
                                WHERE id=?");
        $stmt->execute([$category_id, $name, $location, $agent_id, $price, $status, $description, $id]);
        $msg = "Property updated successfully";
    } else {
        $stmt = $conn->prepare("INSERT INTO properties 
                                (category_id, name, location, agent_id, price, status, description) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $location, $agent_id, $price, $status, $description]);
        $id = $conn->lastInsertId();
        $msg = "Property saved successfully";
    }

    // ✅ Handle photos
    if (!empty($_FILES['photos']['name'][0])) {
        $uploadDir = __DIR__ . "/../uploads/";
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $ext      = pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION);
                $fileName = uniqid("prop_", true) . "." . strtolower($ext);
                $target   = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $target)) {
                    $photoStmt = $conn->prepare("INSERT INTO property_photos (property_id, photo) VALUES (?, ?)");
                    $photoStmt->execute([$id, $fileName]);
                }
            }
        }
    }

    header("Location: idx.php?page=properties&msg=" . urlencode($msg));
    exit;
}
?>

<!-- ✅ Property Form UI -->
<div class="container-fluid p-4" style="height:100vh; overflow-y:auto;">
  <div class="card shadow-lg" style="min-height:95vh;">
    <div class="card-header bg-primary text-white sticky-top">
      <h4 class="mb-0"><?= $id ? "Edit Property" : "Add Property" ?></h4>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">

        <!-- Name -->
        <div class="mb-3">
          <label class="form-label">Property Name</label>
          <input type="text" name="name" class="form-control"
                 value="<?= htmlspecialchars($property['name'] ?? '') ?>" required>
        </div>

        <!-- Location -->
        <div class="mb-3">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control"
                 value="<?= htmlspecialchars($property['location'] ?? '') ?>" required>
        </div>

        <!-- Price -->
        <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" class="form-control"
                 value="<?= htmlspecialchars($property['price'] ?? '') ?>" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php
            $statuses = ['Available', 'Sold', 'Ongoing','Rented'];
            foreach ($statuses as $s) {
                $selected = ($property['status'] ?? '') === $s ? 'selected' : '';
                echo "<option value='$s' $selected>$s</option>";
            }
            ?>
          </select>
        </div>

        <!-- Category -->
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select name="category_id" class="form-control">
            <?php
            $cats = $conn->query("SELECT id, name FROM property_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cats as $c) {
                $selected = ($property['category_id'] ?? '') == $c['id'] ? 'selected' : '';
                echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Agent -->
        <div class="mb-3">
          <label class="form-label">Agent</label>
          <select name="agent_id" class="form-control">
            <?php
            $agents = $conn->query("SELECT id, name FROM agents ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($agents as $a) {
                $selected = ($property['agent_id'] ?? '') == $a['id'] ? 'selected' : '';
                echo "<option value='{$a['id']}' $selected>{$a['name']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Description -->
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($property['description'] ?? '') ?></textarea>
        </div>

        <!-- Upload Photos -->
        <div class="mb-3">
          <label class="form-label">Upload New Photos</label>
          <input type="file" name="photos[]" class="form-control" multiple>
        </div>

        <!-- Existing Photos -->
        <?php if ($id): ?>
          <div class="mb-3">
            <label class="form-label">Existing Photos</label>
            <div class="d-flex flex-wrap gap-2" style="max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:6px;">
              <?php
              $photos = $conn->prepare("SELECT * FROM property_photos WHERE property_id=?");
              $photos->execute([$id]);
              foreach ($photos as $p): ?>
                <img src="uploads/<?= htmlspecialchars($p['photo']) ?>"
                     style="width:100px; height:100px; object-fit:cover; border-radius:6px;">
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Submit -->
        <div class="text-end">
          <button type="submit" class="btn btn-success px-4">
            <?= $id ? "Update Property" : "Save Property" ?>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
