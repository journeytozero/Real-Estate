<?php
require_once __DIR__ . "/../partials/db.php";
require_once __DIR__ . "/../partials/img_helper.php";

$id = $_GET['id'] ?? null;
$name = $description = $photo = "";

// ✅ Load existing category if editing
if ($id) {
  $stmt = $conn->prepare("SELECT * FROM property_categories WHERE id=? LIMIT 1");
  $stmt->execute([$id]);
  if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $name = $row['name'];
    $description = $row['description'];
    $photo = $row['photo'];
  } else {
    $_SESSION['error'] = "Category not found.";
    header("Location: idx.php?page=property_categories");
    exit;
  }
}

// ✅ Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $description = trim($_POST['description'] ?? '');

  // --------------------------
  // PHOTO UPLOAD HANDLER
  // --------------------------
  if (!empty($_FILES['photo']['name'])) {
    if ($_FILES['photo']['size'] > 5 * 1024 * 1024) { // 5MB limit
      $_SESSION['error'] = "Photo must be less than 5MB.";
      header("Location: idx.php?page=property_categories_form&id=" . ($id ?? ''));
      exit;
    }

    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed)) {
      $_SESSION['error'] = "Only JPG, PNG, or GIF files allowed.";
      header("Location: idx.php?page=property_categories_edit&id=" . ($id ?? ''));
      exit;
    }

    $newName = uniqid("cat_", true) . "." . $ext;
    $uploadPath = __DIR__ . "/../uploads/" . $newName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
      // ✅ Delete old photo if exists
      if (!empty($photo) && file_exists(__DIR__ . "/../uploads/" . $photo)) {
        unlink(__DIR__ . "/../uploads/" . $photo);
      }
      $photo = $newName;
    }
  }

  // --------------------------
  // CHECK DUPLICATE NAME
  // --------------------------
  $check = $conn->prepare("SELECT id FROM property_categories WHERE name=? AND id<>?");
  $check->execute([$name, $id ?? 0]);
  if ($check->rowCount() > 0) {
    $_SESSION['error'] = "Category name already exists.";
  } else {
    if ($id) {
      // ✅ Update existing category
      $q = $conn->prepare("UPDATE property_categories SET name=?, description=?, photo=? WHERE id=?");
      $q->execute([$name, $description, $photo, $id]);
      $_SESSION['success'] = "Category updated successfully.";
    } else {
      // ✅ Insert new category
      $q = $conn->prepare("INSERT INTO property_categories (name, description, photo) VALUES (?,?,?)");
      $q->execute([$name, $description, $photo]);
      $_SESSION['success'] = "Category created successfully.";
    }
    header("Location: idx.php?page=property_categories");
    exit;
  }
}
?>

<div class="container p-4">
  <h3><?= $id ? "✏️ Edit Category" : "➕ Add Category" ?></h3>
  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="p-3 border bg-light rounded">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description (optional)</label>
      <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Photo</label><br>
      <?php if (!empty($photo)): ?>
        <img src="uploads/<?= htmlspecialchars($photo) ?>" style="width:80px; height:80px; object-fit:cover; border-radius:8px;">
        <br><br>
      <?php endif; ?>
      <input type="file" name="photo" class="form-control">
    </div>
    <button class="btn btn-success">Save</button>
    <a href="idx.php?page=property_categories" class="btn btn-secondary">Cancel</a>
  </form>
</div>