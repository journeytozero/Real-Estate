<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Redirect unauthenticated users
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

$clientId = (int)$_SESSION['client_id'];

// ✅ Fetch saved properties
$savedStmt = $conn->prepare("
    SELECT p.id, p.name, p.location, p.price, ph.photo
    FROM saved_properties sp
    JOIN properties p ON sp.property_id = p.id
    LEFT JOIN (
        SELECT property_id, MIN(id) as first_photo
        FROM property_photos GROUP BY property_id
    ) x ON p.id = x.property_id
    LEFT JOIN property_photos ph ON ph.id = x.first_photo
    WHERE sp.client_id = ?
    ORDER BY sp.created_at DESC
    LIMIT 3
");
$savedStmt->execute([$clientId]);
$saved = $savedStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch latest transactions
$txnStmt = $conn->prepare("
    SELECT t.id, t.amount, t.date, t.status, p.name AS property_name
    FROM transactions t
    JOIN properties p ON t.property_id = p.id
    WHERE t.client_id = ?
    ORDER BY t.date DESC
    LIMIT 3
");
$txnStmt->execute([$clientId]);
$transactions = $txnStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch notifications
$notifStmt = $conn->prepare("
    SELECT id, title, message, created_at, is_read
    FROM notifications
    WHERE client_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
$notifStmt->execute([$clientId]);
$notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h4 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['client_name'] ?? 'Client') ?></h4>

    <div class="row">
        <!-- ✅ Saved Properties -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Saved Properties</div>
                <div class="card-body">
                    <?php if ($saved): ?>
                        <?php foreach ($saved as $prop): ?>
                            <?php 
                              $img = $prop['photo'] 
                                  ? "../../admin/uploads/" . $prop['photo'] 
                                  : "https://via.placeholder.com/100x80?text=No+Image"; 
                            ?>
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?= $img ?>" alt="" class="me-3 rounded" style="width:100px;height:80px;object-fit:cover;">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($prop['name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($prop['location']) ?></small><br>
                                    <strong class="text-primary"><?= number_format($prop['price']) ?> TK</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="saved_properties.php" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                    <?php else: ?>
                        <p class="text-muted">No saved properties yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ✅ Recent Transactions -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Recent Transactions</div>
                <div class="card-body">
                    <?php if ($transactions): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($transactions as $txn): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <strong><?= htmlspecialchars($txn['property_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($txn['date']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold"><?= number_format($txn['amount']) ?> TK</span><br>
                                        <span class="badge 
                                            <?= $txn['status'] === 'Completed' ? 'bg-success' : ($txn['status'] === 'Pending' ? 'bg-warning' : 'bg-danger') ?>">
                                            <?= htmlspecialchars($txn['status']) ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="payments.php" class="btn btn-sm btn-outline-success mt-2">View All</a>
                    <?php else: ?>
                        <p class="text-muted">No transactions yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ✅ Notifications -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">Notifications</div>
                <div class="card-body">
                    <?php if ($notifications): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($notifications as $n): ?>
                                <li class="list-group-item">
                                    <strong><?= htmlspecialchars($n['title']) ?></strong><br>
                                    <small><?= htmlspecialchars($n['message']) ?></small><br>
                                    <span class="text-muted small"><?= htmlspecialchars($n['created_at']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No notifications.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ✅ Mortgage Calculator (Quick Tool) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">Mortgage Calculator</div>
                <div class="card-body">
                    <form id="mortgageForm">
                        <div class="mb-2">
                            <label class="form-label">Loan Amount</label>
                            <input type="number" id="principal" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Interest Rate (%)</label>
                            <input type="number" id="rate" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Years</label>
                            <input type="number" id="years" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Calculate</button>
                    </form>
                    <div id="result" class="mt-3 text-success fw-bold"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
document.getElementById("mortgageForm").addEventListener("submit", function(e){
    e.preventDefault();
    let P = parseFloat(document.getElementById("principal").value);
    let r = parseFloat(document.getElementById("rate").value) / 100 / 12;
    let n = parseInt(document.getElementById("years").value) * 12;

    let m = (P * r * Math.pow(1+r, n)) / (Math.pow(1+r, n) - 1);
    document.getElementById("result").innerText = "Monthly Payment: " + m.toFixed(2) + " TK";
});
</script>
