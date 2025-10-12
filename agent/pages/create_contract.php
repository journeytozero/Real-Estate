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
$agentId = (int)$_SESSION['agent_id'];

$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rentedId  = (int)($_POST['rented_id'] ?? 0);
    $rent      = (float)($_POST['monthly_rent'] ?? 0);
    $months    = (int)($_POST['total_months'] ?? 0);
    $freq      = $_POST['payment_frequency'] ?? 'Monthly';
    $startDate = $_POST['start_date'] ?? null;

    if ($rentedId && $rent > 0 && $months > 0 && $startDate) {
        try {
            $stmt = $conn->prepare("CALL sp_create_rental_contract_and_schedule(?,?,?,?,?)");
            $stmt->execute([$rentedId, $rent, $months, $freq, $startDate]);
            $msg = "✅ Contract & schedule created successfully.";
        } catch (Throwable $e) {
            $msg = "❌ Error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $msg = "⚠️ Please fill all fields.";
    }
}
?>

<div class="content">
    <h2 class="mb-4">Create Rental Contract</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Rented ID</label>
                        <input type="number" name="rented_id" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monthly Rent</label>
                        <input type="number" step="0.01" name="monthly_rent" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Months</label>
                        <input type="number" name="total_months" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Frequency</label>
                        <select name="payment_frequency" class="form-select">
                            <option>Monthly</option>
                            <option>Quarterly</option>
                            <option>Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Create Contract & Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
