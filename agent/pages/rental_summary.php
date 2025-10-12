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

// Payments summary
$stmt = $conn->prepare("
  SELECT vas.client_id, c.name AS client_name,
         COALESCE(vas.total_installments,0) AS total_installments,
         COALESCE(vas.paid_count,0)        AS paid_count,
         COALESCE(vas.pending_count,0)     AS pending_count,
         COALESCE(vas.overdue_count,0)     AS overdue_count,
         COALESCE(vas.total_amount,0)      AS total_amount,
         COALESCE(vas.total_paid,0)        AS total_paid,
         COALESCE(vas.total_due,0)         AS total_due
  FROM v_agent_rental_summary vas
  JOIN clients c ON c.id = vas.client_id
  WHERE vas.agent_id = :aid
  ORDER BY c.name
");
$stmt->execute([':aid' => $agentId]);
$money = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Rental durations
$stmt2 = $conn->prepare("
  SELECT r.id AS rented_id, c.name AS client_name, p.name AS property_name,
         r.rented_start_date, r.rented_end_date,
         DATEDIFF(COALESCE(r.rented_end_date, CURDATE()), r.rented_start_date) AS total_days
  FROM rented r
  JOIN clients c   ON r.client_id = c.id
  JOIN properties p ON r.property_id = p.id
  WHERE r.agent_id = :aid
  ORDER BY r.rented_start_date DESC
");
$stmt2->execute([':aid' => $agentId]);
$durations = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2 class="mb-4">Rental Summary</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-3">Payments by Client</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Installments</th>
                        <th>Paid</th>
                        <th>Pending</th>
                        <th>Overdue</th>
                        <th>Total Amount</th>
                        <th>Total Paid</th>
                        <th>Total Due</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($money): ?>
                        <?php foreach ($money as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['client_name']) ?></td>
                                <td><?= (int)$m['total_installments'] ?></td>
                                <td><?= (int)$m['paid_count'] ?></td>
                                <td><?= (int)$m['pending_count'] ?></td>
                                <td><?= (int)$m['overdue_count'] ?></td>
                                <td>$<?= number_format((float)$m['total_amount'], 2) ?></td>
                                <td>$<?= number_format((float)$m['total_paid'], 2) ?></td>
                                <td>$<?= number_format((float)$m['total_due'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No rental data available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Rental Durations</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Property</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Total Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($durations): ?>
                        <?php foreach ($durations as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['client_name']) ?></td>
                                <td><?= htmlspecialchars($d['property_name']) ?></td>
                                <td><?= htmlspecialchars($d['rented_start_date']) ?></td>
                                <td><?= htmlspecialchars($d['rented_end_date'] ?? '—') ?></td>
                                <td><?= (int)$d['total_days'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No rentals found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
