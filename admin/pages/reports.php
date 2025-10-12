<?php
require_once __DIR__ . "/../partials/db.php";

// ✅ Monthly Transactions
$transactions = $conn->query("
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, COUNT(*) AS total
    FROM transactions
    GROUP BY month
    ORDER BY month ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Properties by Status
$properties = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM properties
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Agent Performance
$agents = $conn->query("
    SELECT a.name, COUNT(t.id) AS total
    FROM agents a
    LEFT JOIN transactions t ON a.id = t.agent_id
    GROUP BY a.name
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Find top agent
$topAgent = max(array_column($agents, 'total'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports & Analytics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-4">
  <h2 class="text-center mb-4">📊 Reports & Analytics</h2>

  <!-- Export Buttons -->
  <div class="mb-4 text-center">
    <a href="export_excel.php" class="btn btn-success btn-sm">⬇️ Export Excel</a>
    <a href="export_pdf.php" class="btn btn-danger btn-sm">⬇️ Export PDF</a>
    <a href="export_txt.php" class="btn btn-secondary btn-sm">⬇️ Export TXT</a>
  </div>

  <div class="row">
    <!-- Transactions -->
    <div class="col-md-6">
      <div class="card p-3 mb-4 shadow">
        <h5 class="text-center">Monthly Transactions</h5>
        <canvas id="transactionChart"></canvas>

        <!-- Filter input -->
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search Transactions..." onkeyup="filterTable(this,'transactionTable')">

        <table id="transactionTable" class="table table-sm mt-3">
          <thead><tr><th>Month</th><th>Total</th></tr></thead>
          <tbody>
            <?php foreach ($transactions as $t): ?>
              <tr>
                <td><?= htmlspecialchars($t['month']) ?></td>
                <td><?= htmlspecialchars($t['total']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <button class="btn btn-outline-secondary btn-sm" onclick="exportTable('transactionTable')">⬇️ Export CSV</button>
      </div>
    </div>

    <!-- Properties -->
    <div class="col-md-6">
      <div class="card p-3 mb-4 shadow">
        <h5 class="text-center">Properties by Status</h5>
        <canvas id="propertyChart"></canvas>

        <!-- Filter input -->
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search Properties..." onkeyup="filterTable(this,'propertyTable')">

        <table id="propertyTable" class="table table-sm mt-3">
          <thead><tr><th>Status</th><th>Total</th></tr></thead>
          <tbody>
            <?php foreach ($properties as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['status']) ?></td>
                <td><?= htmlspecialchars($p['total']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <button class="btn btn-outline-secondary btn-sm" onclick="exportTable('propertyTable')">⬇️ Export CSV</button>
      </div>
    </div>

    <!-- Agent Performance -->
    <div class="col-md-12">
      <div class="card p-3 mb-4 shadow">
        <h5 class="text-center">Agent Performance</h5>
        <canvas id="agentChart"></canvas>

        <!-- Filter input -->
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search Agents..." onkeyup="filterTable(this,'agentTable')">

        <table id="agentTable" class="table table-sm mt-3">
          <thead><tr><th>Agent</th><th>Total Transactions</th></tr></thead>
          <tbody>
            <?php foreach ($agents as $a): ?>
              <tr class="<?= $a['total'] == $topAgent ? 'table-success fw-bold' : '' ?>">
                <td><?= htmlspecialchars($a['name']) ?></td>
                <td><?= htmlspecialchars($a['total']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <button class="btn btn-outline-secondary btn-sm" onclick="exportTable('agentTable')">⬇️ Export CSV</button>
      </div>
    </div>
  </div>
</div>

<script>
// ✅ Charts
new Chart(document.getElementById('transactionChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($transactions, 'month')) ?>,
    datasets: [{
      label: 'Transactions',
      data: <?= json_encode(array_column($transactions, 'total')) ?>,
      borderColor: '#007bff',
      backgroundColor: 'rgba(0,123,255,0.2)',
      fill: true
    }]
  }
});
new Chart(document.getElementById('propertyChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($properties, 'status')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($properties, 'total')) ?>,
      backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
    }]
  }
});
new Chart(document.getElementById('agentChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($agents, 'name')) ?>,
    datasets: [{
      label: 'Transactions',
      data: <?= json_encode(array_column($agents, 'total')) ?>,
      backgroundColor: '#007bff'
    }]
  },
  options: {
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});

// ✅ Filter function
function filterTable(input, tableId) {
  let filter = input.value.toLowerCase();
  document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
}

// ✅ Export table to CSV
function exportTable(tableId) {
  let rows = Array.from(document.querySelectorAll(`#${tableId} tr`));
  let csv = rows.map(r => Array.from(r.querySelectorAll('th,td')).map(td => td.innerText).join(',')).join('\n');
  let blob = new Blob([csv], { type: 'text/csv' });
  let link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = tableId + ".csv";
  link.click();
}
</script>
</body>
</html>
