<?php
// Fetch all transactions with joined info
$stmt = $conn->query("
    SELECT t.*, 
           p.name AS property_name, 
           c.name AS client_name, 
           a.name AS agent_name
    FROM transactions t
    LEFT JOIN properties p ON t.property_id = p.id
    LEFT JOIN clients c ON t.client_id = c.id
    LEFT JOIN agents a ON t.agent_id = a.id
    ORDER BY t.date DESC
");
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Transactions</h3>
    <a href="idx.php?page=transaction_form" class="btn btn-primary">➕ Add Transaction</a>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Property</th>
        <th>Client</th>
        <th>Agent</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
        <th>Print</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($transactions): ?>
        <?php foreach ($transactions as $i => $t): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($t['property_name']) ?></td>
            <td><?= htmlspecialchars($t['client_name']) ?></td>
            <td><?= htmlspecialchars($t['agent_name']) ?></td>
            <td>$<?= number_format($t['amount'], 2) ?></td>
            <td>
              <?php if ($t['status'] == 'Completed'): ?>
                <span class="badge bg-success">Completed</span>
              <?php elseif ($t['status'] == 'Pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php else: ?>
                <span class="badge bg-danger">Cancelled</span>
              <?php endif; ?>
            </td>
            <td><?= date("d M Y", strtotime($t['date'])) ?></td>
            <td>
              <a href="idx.php?page=transaction_form&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="index.php?page=transaction_delete&id=<?= $t['id'] ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Are you sure you want to delete this transaction?');">
                Delete
              </a>
            </td>
            <td>
              <a href="pages/invoice.php?id=<?= $t['id'] ?>" target="_blank" class="btn btn-sm btn-secondary">🖨 Print</a>
              <a href="pages/invoice_pdf.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-info">⬇ PDF</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" class="text-center">No transactions found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
