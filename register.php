<?php
session_start();
require_once __DIR__ . "/admin/partials/db.php";

// Redirect if already logged in
if (isset($_SESSION['client_id'])) {
    header("Location: clients/pages/dashboard.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm_password'] ?? '');

    if ($name && $email && $password && $confirm) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            // ✅ Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM clients WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email is already registered.";
            } else {
                // ✅ Insert new client
                $hashed = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO clients (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashed]);

                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow p-4" style="max-width: 450px; width:100%;">
    <h4 class="text-center mb-4">Create Account</h4>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input id="name" type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input id="confirm_password" type="password" name="confirm_password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-success w-100">Register</button>
    </form>

    <div class="text-center mt-3">
      <small>Already have an account? <a href="login.php">Login</a></small>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
