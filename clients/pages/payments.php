<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/sidebar.php";
require_once __DIR__ . "/../partials/navbar.php";

// ✅ Require login
if (!isset($_SESSION['client_id'])) {
    header("Location: ../login.php");
    exit;
}
$clientId = (int)$_SESSION['client_id'];

$msg = null;

// ✅ Get payment_id
$paymentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ✅ Fetch installment details
$stmt = $conn->prepare("
    SELECT rp.id AS payment_id, rp.due_date, rp.amount, rp.status,
           p.name AS property_name, p.location
    FROM rental_payments rp
    JOIN rental_contracts rc ON rp.contract_id = rc.id
    JOIN rented r            ON rc.rented_id = r.id
    JOIN properties p        ON r.property_id = p.id
    WHERE rp.id = ? AND rp.client_id = ?
    LIMIT 1
");
$stmt->execute([$paymentId, $clientId]);
$installment = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $installment) {
    $method    = $_POST['method'] ?? 'cash';
    $reference = $_POST['reference_no'] ?? null;

    if ($installment['status'] !== 'Paid') {
        try {
            $conn->beginTransaction();

            $amount = (float)$installment['amount'];

            // Insert transaction
            $ins = $conn->prepare("
                INSERT INTO rental_transactions (payment_id, method, transaction_amount, reference_no)
                VALUES (?, ?, ?, ?)
            ");
            $ins->execute([$paymentId, $method, $amount, $reference]);

            // Trigger marks as Paid
            $conn->commit();

            $msg = "✅ Payment successful for {$installment['property_name']}.";

            // Refresh installment status
            $stmt->execute([$paymentId, $clientId]);
            $installment = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            $msg = "❌ Error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $msg = "⚠️ This installment is already paid.";
    }
}
?>

<div class="content">
    <h2 class="mb-4">Make Payment</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <?php if ($installment): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-3">Installment Details</h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Property:</strong> <?= htmlspecialchars($installment['property_name']) ?> (<?= htmlspecialchars($installment['location']) ?>)</li>
                    <li class="list-group-item"><strong>Due Date:</strong> <?= htmlspecialchars($installment['due_date']) ?></li>
                    <li class="list-group-item"><strong>Amount:</strong> $<?= number_format((float)$installment['amount'], 2) ?></li>
                    <li class="list-group-item"><strong>Status:</strong> 
                        <span class="badge 
                            <?php if ($installment['status']=='Paid') echo 'bg-success';
                                  elseif ($installment['status']=='Overdue') echo 'bg-danger';
                                  else echo 'bg-warning text-dark'; ?>">
                            <?= htmlspecialchars($installment['status']) ?>
                        </span>
                    </li>
                </ul>

                <?php if ($installment['status'] !== 'Paid'): ?>
                    <h4 class="mb-3">Payment Method</h4>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Method</label>
                            <select name="method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                                <option value="mastercard">MasterCard</option>
                                <option value="visa">Visa</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference No (optional)</label>
                            <input type="text" name="reference_no" class="form-control">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Confirm Payment</button>
                            <a href="transaction.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success">This installment has already been paid.</div>
                    <a href="transaction.php" class="btn btn-secondary">Back to Transactions</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">⚠️ Invalid installment selected.</div>
        <a href="transaction.php" class="btn btn-secondary">Back to Transactions</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>
