<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../partials/db.php";

$adminId = $_SESSION['admin_id'];

// ✅ Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    // Fetch old photo
    $stmt = $conn->prepare("SELECT photo FROM admins WHERE id=?");
    $stmt->execute([$adminId]);
    $oldPhoto = $stmt->fetchColumn();

    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $targetFile = __DIR__ . "/../uploads/" . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        // Delete old photo if exists and not default
        if (!empty($oldPhoto) && $oldPhoto !== "default.png") {
            $oldFilePath = __DIR__ . "/../uploads/" . $oldPhoto;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Update DB
        $stmt = $conn->prepare("UPDATE admins SET photo=? WHERE id=?");
        $stmt->execute([$fileName, $adminId]);

        $_SESSION['admin_photo'] = $fileName; // update session
        $msg = "Profile photo updated!";
    } else {
        $msg = "Upload failed!";
    }
}

// ✅ Fetch admin info
$stmt = $conn->prepare("SELECT * FROM admins WHERE id=?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$profilePhoto = !empty($admin['photo']) ? $admin['photo'] : "default.png";
?>

<div class="container p-4">
  <h3>Profile</h3>

  <?php if (!empty($msg)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Profile Photo</label><br>
      <img src="uploads/<?= htmlspecialchars($profilePhoto) ?>" 
           style="width:100px; height:100px; object-fit:cover; border-radius:50%;"><br><br>
      <input type="file" name="photo" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
  </form>
  
</div>
