<?php
/**
 * Maintenance requests page.
 *
 * Clients can view their existing maintenance requests and create new ones for
 * properties they are currently renting.  The request form is only shown if
 * the client has at least one active rental.  Submitted requests are stored
 * in the `maintenance_requests` table with a default status of `pending`.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Ensure the client is logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

$clientId = (int)$_SESSION['client_id'];
$message = '';

// Fetch active rentals for the dropdown (rented_end_date >= today)
$activeStmt = $conn->prepare(
    'SELECT r.property_id, p.name
     FROM rented r
     JOIN properties p ON p.id = r.property_id
     WHERE r.client_id = ? AND r.rented_end_date >= CURDATE()'
);
$activeStmt->execute([$clientId]);
$activeRentals = $activeStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Handle new request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertyId = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
    $desc = trim($_POST['description'] ?? '');
    if ($propertyId && $desc !== '') {
        $insert = $conn->prepare('INSERT INTO maintenance_requests (client_id, property_id, description, status, created_at) VALUES (?,?,?,?,NOW())');
        $insert->execute([$clientId, $propertyId, $desc, 'pending']);
        $message = '✅ Request submitted successfully!';
        // Refresh the active rentals in case of new request (not strictly necessary but keeps state consistent)
        $activeStmt->execute([$clientId]);
        $activeRentals = $activeStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}

// Fetch existing requests for the client
$requestsStmt = $conn->prepare(
    'SELECT mr.id, mr.description, mr.status, DATE_FORMAT(mr.created_at, "%b %d, %Y") AS created,
            p.name AS property_name
     FROM maintenance_requests mr
     JOIN properties p ON p.id = mr.property_id
     WHERE mr.client_id = ?
     ORDER BY mr.created_at DESC'
);
$requestsStmt->execute([$clientId]);
$requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);

// Include layout components
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h4 class="mb-4">Maintenance Requests</h4>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Only show the form if there are active rentals -->
    <?php if (!empty($activeRentals)): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">Create New Request</div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="property_id" class="form-label">Property</label>
                        <select id="property_id" name="property_id" class="form-select" required>
                            <option value="">-- Select Property --</option>
                            <?php foreach ($activeRentals as $id => $name): ?>
                                <option value="<?= (int)$id ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary">Submit Request</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted">You have no active rentals.  No maintenance requests can be submitted.</p>
    <?php endif; ?>

    <!-- Existing requests table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-tools me-2"></i> Your Requests
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Property</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($requests): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($req['id']) ?></td>
                                <td><?= htmlspecialchars($req['property_name']) ?></td>
                                <td><?= nl2br(htmlspecialchars($req['description'])) ?></td>
                                <td>
                                    <?php
                                        switch ($req['status']) {
                                            case 'resolved':
                                                $badgeClass = 'success';
                                                break;
                                            case 'in_progress':
                                                $badgeClass = 'warning';
                                                break;
                                            default:
                                                $badgeClass = 'secondary';
                                        }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> text-capitalize">
                                        <?= htmlspecialchars(str_replace('_', ' ', $req['status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($req['created']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No requests found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>