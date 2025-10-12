<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";
require_once __DIR__ . "/../includes/navbar.php";

if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];

// Handle add/edit client form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $client_id = $_POST['client_id'] ?? null;

    if ($client_id) {
        // Edit client
        $stmt = $conn->prepare("UPDATE clients SET name=?, email=?, phone=? WHERE id=? AND agent_id=?");
        $stmt->execute([$name, $email, $phone, $client_id, $agent_id]);
        $_SESSION['success'] = "Client updated successfully!";
    } else {
        // Add client
        $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, agent_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $agent_id]);
        $_SESSION['success'] = "Client added successfully!";
    }

    header("Location: clients.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM clients WHERE id=? AND agent_id=?");
    $stmt->execute([$_GET['delete'], $agent_id]);
    $_SESSION['success'] = "Client deleted successfully!";
    header("Location: clients.php");
    exit;
}

// Fetch clients for this agent
$stmt = $conn->prepare("SELECT * FROM clients WHERE agent_id=? ORDER BY name ASC");
$stmt->execute([$agent_id]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2 class="mb-4">Clients</h2>

    <!-- Add Client Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#clientModal">
        <i class="fas fa-plus"></i> Add Client
    </button>

    <!-- Client Modal -->
    <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="clientModalLabel">Add/Edit Client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <input type="hidden" name="client_id" id="client_id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Client List</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clients): ?>
                        <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['phone']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-client" 
                                            data-id="<?= $c['id'] ?>" 
                                            data-name="<?= htmlspecialchars($c['name']) ?>"
                                            data-email="<?= htmlspecialchars($c['email']) ?>"
                                            data-phone="<?= htmlspecialchars($c['phone']) ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#clientModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="clients.php?delete=<?= $c['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this client?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No clients found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.edit-client').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('client_id').value = btn.dataset.id;
        document.getElementById('name').value = btn.dataset.name;
        document.getElementById('email').value = btn.dataset.email;
        document.getElementById('phone').value = btn.dataset.phone;
    });
});
</script>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
