<?php
// includes/login_modal.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php'; // your DB connection
require_once __DIR__ . '/../Auth.php';

$auth = new Auth($conn);

// Only respond to POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login_modal') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $auth->login($email, $password);

    header('Content-Type: application/json; charset=utf-8');

    if ($result['status']) {
        $role = $result['role'];

        // Redirect based on role
        if ($role === 'admin') {
            $redirect = '/RS/admin/idx.php';
        } elseif ($role === 'agent') {
            $redirect = '/RS/agent/pages/dashboard.php';
        } elseif ($role === 'buyer') {
            $redirect = '/RS/clients/dashboard.php';
        }

        echo json_encode(['status' => true, 'redirect' => $redirect]);
        exit;
    } else {
        echo json_encode(['status' => false, 'message' => $result['message'] ?? 'Invalid email or password.']);
        exit;
    }
}
?>

<!-- Login Modal HTML -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="loginForm">
        <input type="hidden" name="action" value="login_modal">
        <div class="modal-header">
          <h5 class="modal-title">Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="loginError" class="alert alert-danger d-none"></div>

          <div class="mb-3">
            <label for="modal_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="modal_email" name="email" required>
          </div>

          <div class="mb-3 position-relative">
            <label for="modal_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="modal_password" name="password" required>
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('modal_password')"></span>
          </div>
        </div>
        <!-- ✅ Updated footer with Register switch -->
        <div class="modal-footer d-flex flex-column">
          <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
          <p class="mb-0">Don't have an account? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">
              Register here
            </a>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// AJAX login
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const loginError = document.getElementById('loginError');
    loginError.classList.add('d-none');
    loginError.innerText = '';

    fetch('/RS/includes/login_modal.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status) {
            window.location.href = data.redirect;
        } else {
            loginError.innerText = data.message;
            loginError.classList.remove('d-none');
        }
    })
    .catch(err => {
        loginError.innerText = 'Something went wrong. Please try again.';
        loginError.classList.remove('d-none');
        console.error(err);
    });
});
</script>
