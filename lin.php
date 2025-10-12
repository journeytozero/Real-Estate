<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/Auth.php';

$auth = new Auth($conn);

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $result = $auth->login($_POST['email'], $_POST['password']);

    if ($result['status']) {
        $role = $result['role'];

        // Redirect based on role
        if ($role === 'admin') {
            header("Location: admin/pages/dashboard.php");
        } elseif ($role === 'agent') {
            header("Location: agent/pages/dashboard.php");
        } elseif ($role === 'buyer') {
            header("Location: clients/dashboard.php");
        }
        exit;
    } else {
        $_SESSION['login_msg'] = $result['message'];
        header("Location: login.php");
        exit;
    }
}
?>

<!-- Optional: Include header -->
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4" style="max-width: 400px; width:100%;">
    <h3 class="text-center mb-3">Login</h3>

    <?php if (!empty($_SESSION['login_msg'])): ?>
      <div class="alert alert-danger">
        <?= $_SESSION['login_msg']; unset($_SESSION['login_msg']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="action" value="login">

      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

      <div class="mb-3 position-relative">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
        <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('password')">👁️</span>
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</div>

<!-- Optional: Include footer -->
<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
// Toggle password visibility
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
