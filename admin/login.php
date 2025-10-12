<?php
session_start();
require_once __DIR__ . "/partials/db.php";

$error = "";

// If already logged in → redirect to admin dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: idx.php");
    exit;
}

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true); // security

        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_name']  = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];

        // ✅ Redirect to admin index page
        header("Location: idx.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f5f6fa; }
    .login-box { max-width: 400px; margin: 80px auto; }
    .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="card p-4">
      <h3 class="text-center mb-3">Real Estate Admin</h3>
      <h5 class="text-center text-muted mb-4">Login</h5>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="admin@mail.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
