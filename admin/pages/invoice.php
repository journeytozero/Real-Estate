<?php
require_once __DIR__ . "/../partials/db.php";
$id = $_GET['id'] ?? null;
if (!$id) die("Invalid invoice request!");

// Fetch transaction details
$stmt = $conn->prepare("
    SELECT t.*, 
           p.name AS property_name, p.location, 
           c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
           a.name AS agent_name, a.email AS agent_email
    FROM transactions t
    LEFT JOIN properties p ON t.property_id = p.id
    LEFT JOIN clients c ON t.client_id = c.id
    LEFT JOIN agents a ON t.agent_id = a.id
    WHERE t.id=?
");
$stmt->execute([$id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$invoice) die("Invoice not found!");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice #<?= $invoice['id'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body onload="window.print()">
<div class="container p-4 border">
  <h2 class="text-center">🏠 Real Estate Invoice</h2>
  <hr>
  <p><strong>Invoice ID:</strong> <?= $invoice['id'] ?><br>
     <strong>Date:</strong> <?= date("d M Y", strtotime($invoice['date'])) ?></p>
  <h5>Client:</h5>
  <p><?= $invoice['client_name'] ?><br><?= $invoice['client_email'] ?><br><?= $invoice['client_phone'] ?></p>

  <h5>Property:</h5>
  <p><?= $invoice['property_name'] ?> <br> Location: <?= $invoice['location'] ?></p>

  <h5>Agent:</h5>
  <p><?= $invoice['agent_name'] ?> (<?= $invoice['agent_email'] ?>)</p>

  <table class="table table-bordered">
    <thead>
      <tr><th>Description</th><th>Amount</th><th>Status</th></tr>
    </thead>
    <tbody>
      <tr>
        <td>Property Transaction</td>
        <td>$<?= number_format($invoice['amount'], 2) ?></td>
        <td><?= $invoice['status'] ?></td>
      </tr>
    </tbody>
  </table>
</div>
</body>
</html>
