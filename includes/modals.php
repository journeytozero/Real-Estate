<?php
// Start session
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Auth.php';

$auth = new Auth($conn);

// -----------------------------
// Handle AJAX POST Requests
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';

    // ---------- LOGIN ----------
    if ($action === 'login_modal') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['status' => false, 'message' => 'Email and password are required.']);
            exit;
        }

        $result = $auth->login($email, $password);

        if ($result['status']) {
            $redirect = match ($result['role']) {
                'admin' => '/RS/admin/idx.php',
                'agent' => '/RS/agent/pages/dashboard.php',
                'buyer' => '/RS/clients/pages/dashboard.php',
                default => '/RS/index.php',
            };

            echo json_encode(['status' => true, 'redirect' => $redirect]);
        } else {
            echo json_encode(['status' => false, 'message' => $result['message']]);
        }
        exit;
    }

    // ---------- REGISTER ----------
    if ($action === 'register_modal') {
        $result = $auth->register($_POST, $_FILES);

        if ($result['status']) {
            // After registration, go to login page instead of auto-login
            $result['redirect'] = '/RS/index.php'; // login page
        }

        echo json_encode($result);
        exit;
    }

    // Invalid action
    echo json_encode(['status' => false, 'message' => 'Invalid request.']);
    exit;
}
?>

<!-- ================= LOGIN MODAL ================= -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="loginForm" autocomplete="off">
        <input type="hidden" name="action" value="login_modal">
        <div class="modal-header">
          <h5 class="modal-title">Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="loginError" class="alert alert-danger d-none"></div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3 position-relative">
            <label>Password</label>
            <input type="password" id="login_password" name="password" class="form-control" required>
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('login_password')">👁️</span>
          </div>
        </div>
        <div class="modal-footer d-flex flex-column">
          <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
          <p class="mb-0 text-center">
            Don't have an account? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register here</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ================= REGISTER MODAL ================= -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="registerForm" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="action" value="register_modal">
        <div class="modal-header">
          <h5 class="modal-title">Register</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="registerError" class="alert alert-danger d-none"></div>
          <div id="registerSuccess" class="alert alert-success d-none"></div>

          <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
          <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3 position-relative">
            <label>Password</label>
            <input type="password" id="reg_password" name="password" class="form-control" required>
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('reg_password')">👁️</span>
          </div>
          <div class="mb-3 position-relative">
            <label>Confirm Password</label>
            <input type="password" id="reg_confirm_password" name="confirm_password" class="form-control" required>
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('reg_confirm_password')">👁️</span>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select name="role" id="modalRoleSelect" class="form-control" required>
              <option value="">Select Role</option>
              <option value="buyer">Buyer</option>
              <option value="agent">Agent</option>
            </select>
          </div>
          <div class="mb-3" id="modalAgentDoc" style="display:none;">
            <label>Trade License (PDF/JPG/PNG)</label>
            <input type="file" name="trade_license" id="modal_trade_license" class="form-control">
          </div>
        </div>
        <div class="modal-footer d-flex flex-column">
          <button type="submit" class="btn btn-primary w-100 mb-2">Register</button>
          <p class="mb-0 text-center">
            Already have an account? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Log in</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Toggle password visibility
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Show/hide trade license input for agents
document.getElementById('modalRoleSelect').addEventListener('change', function() {
    const agentDoc = document.getElementById('modalAgentDoc');
    const tradeInput = document.getElementById('modal_trade_license');
    if (this.value === 'agent') {
        agentDoc.style.display = 'block';
        tradeInput.required = true;
    } else {
        agentDoc.style.display = 'none';
        tradeInput.required = false;
    }
});

// AJAX helper function
function ajaxFormSubmit(form, errorEl, successEl=null, isRegister=false) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        errorEl.classList.add('d-none'); errorEl.innerText = '';
        if (successEl) { successEl.classList.add('d-none'); successEl.innerText = ''; }

        fetch('/RS/includes/modals.php', { method:'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                if (isRegister) {
                    // Show success message
                    if (successEl) {
                        successEl.innerText = data.message;
                        successEl.classList.remove('d-none');
                    }
                    // Redirect to login page after 1.5s
                    setTimeout(() => {
                        window.location.href = '/RS/idx.php'; // login page
                    }, 1500);
                } else {
                    if (data.redirect) window.location.href = data.redirect;
                }
            } else {
                errorEl.innerText = data.message;
                errorEl.classList.remove('d-none');
            }
        }).catch(err => {
            errorEl.innerText = 'An error occurred. Please try again.';
            errorEl.classList.remove('d-none');
        });
    });
}

// Attach AJAX
ajaxFormSubmit(document.getElementById('loginForm'), document.getElementById('loginError'));
ajaxFormSubmit(document.getElementById('registerForm'), document.getElementById('registerError'), document.getElementById('registerSuccess'), true);
</script>
