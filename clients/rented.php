<?php
// clients/rented.php
declare(strict_types=1);
session_start();

// ---- auth guard (adjust to your auth) ----
if (empty($_SESSION['client_id'])) {
  header('Location: login.php'); // or your client login
  exit;
}
$clientId = (int)$_SESSION['client_id'];

// ---- CSRF helpers ----
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

// ---- DB ----
require_once __DIR__ . '/config/db.php'; // must set $pdo (PDO)

$filter = $_GET['status'] ?? 'unpaid'; // unpaid|all
$params = [':cid' => $clientId];

$sql = "
  SELECT rp.id AS payment_id, rp.due_date, rp.amount, rp.status,
         p.name AS property_name, p.location,
         rc.id AS contract_id, r.rented_start_date, r.rented_end_date
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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// quick totals
$totals = [
  'amount' => 0.0,
  'pending' => 0.0,
  'paid' => 0.0,
  'overdue' => 0.0,
];
if ($rows) {
  foreach ($rows as $r) {
    $totals['amount'] += (float)$r['amount'];
    if ($r['status'] === 'Paid')    $totals['paid']    += (float)$r['amount'];
    if ($r['status'] === 'Pending') $totals['pending'] += (float)$r['amount'];
    if ($r['status'] === 'Overdue') $totals['overdue'] += (float)$r['amount'];
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>My Rentals & Installments</title>
  <style>
    :root { font-family: system-ui, Arial, sans-serif; }
    body { margin: 24px; }
    .wrap { max-width: 1000px; margin: 0 auto; }
    h1 { margin-bottom: 6px; }
    .muted { color:#666; }
    .toolbar { margin: 16px 0; display:flex; gap:8px; align-items:center; }
    a.btn { padding:8px 12px; border:1px solid #ddd; border-radius:8px; text-decoration:none; }
    table { width:100%; border-collapse: collapse; }
    th, td { text-align:left; padding:10px; border-bottom:1px solid #eee; }
    .badge { padding:3px 8px; border-radius:999px; font-size:12px; }
    .Pending { background:#fff5cc; border:1px solid #ffe08a; }
    .Paid { background:#d6f5e5; border:1px solid #9ad8be; }
    .Overdue { background:#ffd6d6; border:1px solid #ff9d9d; }
    .right { text-align:right; }
    .paybtn { padding:6px 10px; border:1px solid #333; border-radius:8px; background:#fafafa; cursor:pointer; }
    .paybtn[disabled] { opacity:.5; cursor:not-allowed; }
    .totals { margin-top:16px; display:flex; gap:16px; flex-wrap:wrap; }
    .chip { border:1px solid #eee; padding:8px 12px; border-radius:10px; background:#fcfcfc; }
  </style>
</head>
<body>
<div class="wrap">
  <h1>My Installments</h1>
  <div class="muted">Client #<?= htmlspecialchars((string)$clientId) ?></div>

  <div class="toolbar">
    <a class="btn" href="?status=unpaid">Show Unpaid</a>
    <a class="btn" href="?status=all">Show All</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>Property</th>
        <th>Due Date</th>
        <th class="right">Amount</th>
        <th>Status</th>
        <th class="right">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="5" class="muted">No installments found.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['property_name']) ?> <span class="muted">(<?= htmlspecialchars($r['location']) ?>)</span></td>
          <td><?= htmlspecialchars($r['due_date']) ?></td>
          <td class="right"><?= number_format((float)$r['amount'], 2) ?></td>
          <td><span class="badge <?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
          <td class="right">
            <?php if ($r['status'] !== 'Paid'): ?>
              <form method="post" action="transaction.php" style="display:inline">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="payment_id" value="<?= (int)$r['payment_id'] ?>">
                <input type="hidden" name="method" value="cash">
                <button class="paybtn" type="submit">Pay Now</button>
              </form>
            <?php else: ?>
              <button class="paybtn" type="button" disabled>Paid</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <div class="totals">
    <div class="chip">Total Listed: <b><?= number_format($totals['amount'],2) ?></b></div>
    <div class="chip">Paid: <b><?= number_format($totals['paid'],2) ?></b></div>
    <div class="chip">Pending: <b><?= number_format($totals['pending'],2) ?></b></div>
    <div class="chip">Overdue: <b><?= number_format($totals['overdue'],2) ?></b></div>
  </div>
</div>
</body>
</html>
