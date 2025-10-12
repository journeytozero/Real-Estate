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

/* =======================
   Fetch unpaid installments
   ======================= */
$sqlUnpaid = "
    SELECT rp.id AS payment_id, rp.due_date, rp.amount, rp.status,
           p.name AS property_name, p.location
    FROM rental_payments rp
    JOIN rental_contracts rc ON rp.contract_id = rc.id
    JOIN rented r            ON rc.rented_id = r.id
    JOIN properties p        ON r.property_id = p.id
    WHERE rp.client_id = :cid
      AND rp.status <> 'Paid'
    ORDER BY rp.due_date ASC
";
$stmtUnpaid = $conn->prepare($sqlUnpaid);
$stmtUnpaid->execute([':cid' => $clientId]);
$unpaid = $stmtUnpaid->fetchAll(PDO::FETCH_ASSOC);

/* =======================
   Fetch payment history
   ======================= */
$sqlHistory = "
    SELECT rt.id, rt.method, rt.transaction_amount, rt.transaction_date, rt.reference_no,
           rp.due_date,
           p.name AS property_name, p.location
    FROM rental_transactions rt
    JOIN rental_payments rp ON rt.payment_id = rp.id
    JOIN rental_contracts rc ON rp.contract_id = rc.id
    JOIN rented r           ON rc.rented_id = r.id
    JOIN properties p       ON r.property_id = p.id
    WHERE rp.client_id = :cid
    ORDER BY rt.transaction_date DESC
";
$stmtHistory = $conn->prepare($sqlHistory);
$stmtHistory->execute([':cid' => $clientId]);
$transactions = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2 class="mb-4">My Transactions</h2>

    <!-- Unpaid Installments -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-3">Unpaid Installments</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($unpaid): ?>
                        <?php foreach ($unpaid as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['property_name']) ?>
                                    <small class="text-muted">(<?= htmlspecialchars($u['location']) ?>)</small>
                                </td>
                                <td><?= htmlspecialchars($u['due_date']) ?></td>
                                <td>$<?= number_format((float)$u['amount'], 2) ?></td>
                                <td>
                                    <span class="badge 
                                        <?php if ($u['status']=='Overdue') echo 'bg-danger';
                                              else echo 'bg-warning text-dark'; ?>">
                                        <?= htmlspecialchars($u['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="payments.php?id=<?= (int)$u['payment_id'] ?>" 
                                       class="btn btn-sm btn-primary">
                                       Pay Now
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">All installments are paid 🎉</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Payment History</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Due Date</th>
                        <th>Paid On</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions): ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['property_name']) ?>
                                    <small class="text-muted">(<?= htmlspecialchars($t['location']) ?>)</small>
                                </td>
                                <td><?= htmlspecialchars($t['due_date']) ?></td>
                                <td><?= date("M d, Y", strtotime($t['transaction_date'])) ?></td>
                                <td>$<?= number_format((float)$t['transaction_amount'], 2) ?></td>
                                <td><?= ucfirst(htmlspecialchars($t['method'])) ?></td>
                                <td><?= htmlspecialchars($t['reference_no'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No transactions yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>
