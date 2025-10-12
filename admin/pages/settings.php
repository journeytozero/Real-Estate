<?php
// Start session and check login
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../partials/db.php";

// Fetch current admin info
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT name, email, photo FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Profile Photo Upload
    $photo = $admin['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $newFile = "uploads/admins/admin_{$admin_id}." . $ext;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $newFile)) {
            $photo = $newFile;
        } else {
            $error = "Failed to upload photo.";
        }
    }

    // Update database
    if (!$error) {
        $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, photo=? WHERE id=?");
        if ($stmt->execute([$name, $email, $photo, $admin_id])) {
            $success = "Profile updated successfully.";
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_photo'] = $photo;
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $conn->prepare("SELECT password FROM admins WHERE id=?");
    $stmt->execute([$admin_id]);
    $hashed = $stmt->fetchColumn();

    if (!password_verify($current, $hashed)) {
        $error = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "New password and confirm password do not match.";
    } else {
        $stmt = $conn->prepare("UPDATE admins SET password=? WHERE id=?");
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $admin_id]);
        $success = "Password updated successfully.";
    }
}
?>

<div class="container-fluid p-4">
    <h3>Settings</h3>
    <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">Update Profile</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button class="btn btn-warning" type="submit">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
