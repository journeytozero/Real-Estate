<?php
// register_modal.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Auth.php';

$auth = new Auth($conn);

// Handle AJAX registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register_modal') {
    header('Content-Type: application/json; charset=utf-8');

    $result = $auth->register($_POST, $_FILES);

    // Optional redirect based on role
    if ($result['status']) {
        $role = $_POST['role'] ?? 'buyer';
        if ($role === 'agent') {
            $result['redirect'] = '/RS/agent/pages/dashboard.php';
        } elseif ($role === 'buyer') {
            $result['redirect'] = '/RS/clients/dashboard.php';
        } else {
            $result['redirect'] = '/RS/admin/idx.php';
        }
    }

    echo json_encode($result);
    exit;
}
?>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="registerForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="register_modal">
        <div class="modal-header">
          <h5 class="modal-title">Register</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Live messages -->
          <div id="registerError" class="alert alert-danger d-none"></div>
          <div id="registerSuccess" class="alert alert-success d-none"></div>

          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3 position-relative">
            <label>Password</label>
            <input type="password" name="password" id="reg_password" class="form-control" required>
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('reg_password')">👁️</span>
          </div>

          <div class="mb-3 position-relative">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="reg_confirm_password" class="form-control" required>
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('reg_confirm_password')">👁️</span>
          </div>

          <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required id="modalRoleSelect">
              <option value="">Select Role</option>
              <option value="buyer">Buyer</option>
              <option value="agent">Agent</option>
            </select>
          </div>

          <div class="mb-3" id="modalAgentDoc" style="display:none;">
            <label>Trade License (PDF/JPG/PNG) <span style="color:red;">*</span></label>
            <input type="file" name="trade_license" id="modal_trade_license" class="form-control">
          </div>

        </div>
        <!-- Footer -->
        <div class="modal-footer d-flex flex-column">
          <button type="submit" class="btn btn-primary w-100 mb-2">Register</button>
          <p class="mb-0 text-center">
            Already have an account? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
              Log in
            </a>
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

// Show/hide trade license input for agent role
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

// AJAX registration
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const registerError = document.getElementById('registerError');
    const registerSuccess = document.getElementById('registerSuccess');

    // Reset messages
    registerError.classList.add('d-none');
    registerSuccess.classList.add('d-none');
    registerError.innerText = '';
    registerSuccess.innerText = '';

    fetch('/RS/includes/register_modal.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status) {
            registerSuccess.innerText = data.message;
            registerSuccess.classList.remove('d-none');

            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 2000);
            }
        } else {
            registerError.innerText = data.message;
            registerError.classList.remove('d-none');
        }
    })
    .catch(err => {
        registerError.innerText = 'Something went wrong. Please try again.';
        registerError.classList.remove('d-none');
        console.error(err);
    });
});
</script>
