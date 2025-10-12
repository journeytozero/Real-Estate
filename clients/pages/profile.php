<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: ../login.php");
    exit;
}

$client_id = $_SESSION['client_id'];

// Fetch client info
$stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    $_SESSION['error'] = "Client not found.";
    header("Location: ../login.php");
    exit;
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    $photo_path = $client['photo'] ?? null;

    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = __DIR__ . "/../uploads/clients/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $photo_path = "uploads/clients/" . $fileName;
        }
    }

    $stmt = $conn->prepare("UPDATE clients SET name = ?, email = ?, photo = ? WHERE id = ?");
    $stmt->execute([$name, $email, $photo_path, $client_id]);

    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit;
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $client['password'])) {
        $_SESSION['error'] = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $_SESSION['error'] = "New passwords do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE clients SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $client_id]);
        $_SESSION['success'] = "Password updated successfully!";
    }

    header("Location: profile.php");
    exit;
}

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h2 class="mb-4">My Profile</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Profile Update -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Update Profile</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" class="form-control" value="<?= htmlspecialchars($client['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Profile Photo</label>
                    <?php if (!empty($client['photo'])): ?>
                        <div class="mb-2">
                            <img src="../<?= htmlspecialchars($client['photo']) ?>" alt="Profile" style="height:80px; border-radius:50%;">
                        </div>
                    <?php endif; ?>
                    <input id="photo" type="file" name="photo" class="form-control" accept="image/*">
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
                    <label for="current_password" class="form-label">Current Password</label>
                    <input id="current_password" type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input id="new_password" type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input id="confirm_password" type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>
