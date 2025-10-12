<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

$clientId = (int)$_SESSION['client_id'];
$tid = isset($_GET['tid']) ? (int)$_GET['tid'] : 0;

if ($tid <= 0) {
    header('Location: payments.php');
    exit;
}

// Fetch transaction
$sql = "
    SELECT t.id, t.amount, t.date, t.status, t.method, p.name AS property_name
    FROM transactions t
    JOIN properties p ON t.property_id = p.id
    WHERE t.id = ? AND t.client_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$tid, $clientId]);
$txn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$txn) {
    header('Location: payments.php');
    exit;
}

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i> Payment Receipt</h5>
        </div>
        <div class="card-body">
            <p><strong>Transaction ID:</strong> #<?= htmlspecialchars($txn['id']) ?></p>
            <p><strong>Property:</strong> <?= htmlspecialchars($txn['property_name']) ?></p>
            <p><strong>Amount Paid:</strong> <?= number_format((float)$txn['amount'], 2) ?> TK</p>
            <p><strong>Date:</strong> <?= htmlspecialchars($txn['date']) ?></p>
            <p><strong>Method:</strong> <?= ucfirst(htmlspecialchars($txn['method'] ?? 'N/A')) ?></p>
            <p><strong>Status:</strong> 
                <span class="badge bg-success"><?= htmlspecialchars($txn['status']) ?></span>
            </p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="payments.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Payments
            </a>
            <div>
                <!-- ✅ Print -->
                <button onclick="window.print()" class="btn btn-outline-dark me-2">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <!-- ✅ Download PDF -->
                <a href="receipt_pdf.php?tid=<?= $txn['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
