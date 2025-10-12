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

// Fetch agent info
$stmt = $conn->prepare("SELECT * FROM agents WHERE id=?");
$stmt->execute([$agent_id]);
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agent) {
    $_SESSION['error'] = "Agent not found.";
    header("Location: ../login.php");
    exit;
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    // Handle profile photo upload
    $photo_path = $agent['photo'] ?? null; // existing photo
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = __DIR__ . "/../uploads/agents/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $photo_path = "uploads/agents/" . $fileName;
        }
    }

    $stmt = $conn->prepare("UPDATE agents SET name=?, email=?, photo=? WHERE id=?");
    $stmt->execute([$name, $email, $photo_path, $agent_id]);

    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: settings.php");
    exit;
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $agent['password'])) {
        $_SESSION['error'] = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $_SESSION['error'] = "New passwords do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE agents SET password=? WHERE id=?");
        $stmt->execute([$hash, $agent_id]);
        $_SESSION['success'] = "Password updated successfully!";
    }

    header("Location: settings.php");
    exit;
}
?>

<div class="content">
    <h2 class="mb-4">Settings</h2>

    <!-- Profile Update -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Update Profile</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($agent['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($agent['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Profile Photo</label>
                    <?php if (!empty($agent['photo'])): ?>
                        <div class="mb-2">
                            <img src="../<?= $agent['photo'] ?>" alt="Profile" style="height:80px;border-radius:50%;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- Password Change -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">Change Password</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
