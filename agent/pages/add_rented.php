<?php
// agent/pages/add_rented.php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit;
}
$agentId = (int)$_SESSION['agent_id'];

require_once __DIR__ . '/../config/db.php';

$msg = null;

/* Load properties */
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.location, p.status
    FROM properties p
    WHERE p.agent_id = ?
      AND p.status = 'Available'
      AND NOT EXISTS (
          SELECT 1 FROM rented r
          WHERE r.property_id = p.id
            AND (r.rented_end_date IS NULL OR r.rented_end_date >= CURDATE())
      )
    ORDER BY p.created_at DESC
");
$stmt->execute([$agentId]);
$properties = $stmt->fetchAll();

/* Load clients */
$stmt2 = $conn->prepare("SELECT id, name, email FROM clients WHERE agent_id = ? ORDER BY created_at DESC");
$stmt2->execute([$agentId]);
$clients = $stmt2->fetchAll();

/* Handle form */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertyId  = (int)($_POST['property_id'] ?? 0);
    $clientId    = (int)($_POST['client_id'] ?? 0);
    $category    = trim($_POST['category'] ?? '');
    $startDate   = $_POST['rented_start_date'] ?? date('Y-m-d');

    $monthlyRent = (float)($_POST['monthly_rent'] ?? 0);
    $totalMonths = (int)($_POST['total_months'] ?? 0);
    $frequency   = $_POST['payment_frequency'] ?? 'Monthly';

    if ($propertyId && $clientId && $monthlyRent > 0 && $totalMonths > 0) {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("
                INSERT INTO rented (property_id, agent_id, client_id, category, rented_start_date)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$propertyId, $agentId, $clientId, $category, $startDate]);
            $rentedId = (int)$conn->lastInsertId();

            $stmt2 = $conn->prepare("CALL sp_create_rental_contract_and_schedule(?,?,?,?,?)");
            $stmt2->execute([$rentedId, $monthlyRent, $totalMonths, $frequency, $startDate]);

            $conn->commit();
            $msg = "✅ Rental and contract created successfully for client ID {$clientId}.";
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            $msg = "❌ Error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $msg = "⚠️ Please fill all required fields.";
    }
}

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";
require_once __DIR__ . "/../includes/navbar.php";
?>

<div class="content">
    <h2 class="mb-4">Add Rented</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Property</label>
                        <select name="property_id" class="form-select" required>
                            <option value="">-- Select Property --</option>
                            <?php foreach ($properties as $p): ?>
                                <option value="<?= (int)$p['id'] ?>">
                                    <?= htmlspecialchars($p['name'] . " (" . $p['location'] . ")") ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Client</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">-- Select Client --</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= (int)$c['id'] ?>">
                                    <?= htmlspecialchars($c['name'] . " (" . $c['email'] . ")") ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="rented_start_date" value="<?= date('Y-m-d') ?>" class="form-control" required>
                    </div>
                </div>

                <hr>
                <h4 class="mb-3">Contract Details</h4>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Monthly Rent</label>
                        <input type="number" step="0.01" name="monthly_rent" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Months</label>
                        <input type="number" name="total_months" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Frequency</label>
                        <select name="payment_frequency" class="form-select">
                            <option>Monthly</option>
                            <option>Quarterly</option>
                            <option>Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save Rental & Create Contract</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
