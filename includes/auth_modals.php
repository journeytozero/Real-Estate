<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Auth.php';

$auth = new Auth($conn);

// Handle AJAX Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';

    if ($action === 'login_modal') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $result = $auth->login($email, $password);

        if ($result['status']) {
            $role = $result['role'];
            $redirect = $role === 'admin' ? '/RS/admin/idx.php' :
                        ($role === 'agent' ? '/RS/agent/pages/dashboard.php' : '/RS/clients/dashboard.php');
            echo json_encode(['status' => true, 'redirect' => $redirect]);
        } else {
            echo json_encode(['status' => false, 'message' => $result['message'] ?? 'Invalid email or password.']);
        }
        exit;
    }

    if ($action === 'register_modal') {
        $result = $auth->register($_POST, $_FILES);
        if ($result['status']) {
            echo json_encode(['status' => true, 'message' => $result['message'], 'redirect' => null]);
        } else {
            echo json_encode(['status' => false, 'message' => $result['message']]);
        }
        exit;
    }
}
?>

<!-- ==================== LOGIN MODAL ==================== -->
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
            <span style="position:absolute; right:10px; top:35px; cursor:pointer;" onclick="togglePassword('modal_password')">👁️</span>
          </div>
        </div>
        <div class="modal-footer d-flex flex-column">
          <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
          <p class="mb-0 text-center">
            Don't have an account? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">
              Register here
            </a>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== REGISTER MODAL ==================== -->
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

<!-- ==================== JS ==================== -->
<script>
// Toggle password visibility
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Show/hide trade license for agent
document.getElementById('modalRoleSelect').addEventListener('change', function() {
    const agentDoc = document.getElementById('modalAgentDoc');
    const tradeInput = document.getElementById('modal_trade_license');
    if(this.value === 'agent') { agentDoc.style.display='block'; tradeInput.required=true; }
    else { agentDoc.style.display='none'; tradeInput.required=false; }
});

// AJAX login
document.getElementById('loginForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const loginError = document.getElementById('loginError');
    loginError.classList.add('d-none'); loginError.innerText='';

    fetch('', { method:'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status) window.location.href = data.redirect;
        else { loginError.innerText=data.message; loginError.classList.remove('d-none'); }
    })
    .catch(err => { loginError.innerText='Something went wrong'; loginError.classList.remove('d-none'); });
});

// AJAX register
document.getElementById('registerForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const registerError = document.getElementById('registerError');
    const registerSuccess = document.getElementById('registerSuccess');
    registerError.classList.add('d-none'); registerError.innerText='';
    registerSuccess.classList.add('d-none'); registerSuccess.innerText='';

    fetch('', { method:'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status){
            registerSuccess.innerText = data.message;
            registerSuccess.classList.remove('d-none');
            setTimeout(()=>{ 
                const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                registerModal.hide();
            }, 1500);
        } else {
            registerError.innerText = data.message;
            registerError.classList.remove('d-none');
        }
    })
    .catch(err => { registerError.innerText='Something went wrong'; registerError.classList.remove('d-none'); });
});
</script>
