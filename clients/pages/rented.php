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

// ✅ Fetch installments
$filter = $_GET['status'] ?? 'unpaid';
$params = [':cid' => $clientId];

$sql = "
  SELECT rp.id AS payment_id, rp.due_date, rp.amount, rp.status,
         p.name AS property_name, p.location
  FROM rental_payments rp
  JOIN rental_contracts rc ON rp.contract_id = rc.id
  JOIN rented r            ON rc.rented_id = r.id
  JOIN properties p        ON r.property_id = p.id
  WHERE rp.client_id = :cid
";
if ($filter !== 'all') {
    $sql .= " AND rp.status <> 'Paid'";
}
$sql .= " ORDER BY rp.due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2 class="mb-4">My Installments</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <a href="?status=unpaid" class="btn btn-outline-primary btn-sm">Unpaid Only</a>
                <a href="?status=all" class="btn btn-outline-secondary btn-sm">Show All</a>
            </div>

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
                    <?php if ($rows): ?>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['property_name']) ?> <small class="text-muted">(<?= htmlspecialchars($r['location']) ?>)</small></td>
                                <td><?= htmlspecialchars($r['due_date']) ?></td>
                                <td>$<?= number_format((float)$r['amount'], 2) ?></td>
                                <td>
                                    <span class="badge 
                                        <?php if ($r['status']=='Paid') echo 'bg-success';
                                              elseif ($r['status']=='Overdue') echo 'bg-danger';
                                              else echo 'bg-warning text-dark'; ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if ($r['status'] !== 'Paid'): ?>
                                        <form method="post" action="transaction.php" style="display:inline">
                                            <input type="hidden" name="payment_id" value="<?= (int)$r['payment_id'] ?>">
                                            <input type="hidden" name="method" value="cash">
                                            <button type="submit" class="btn btn-sm btn-primary">Pay Now</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No installments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>
