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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports & Analytics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
<div class="container py-4">
  <h2 class="text-center mb-4">📊 Reports & Analytics</h2>

  <!-- ✅ Export Buttons -->
  <div class="mb-4 text-center">
    <button class="btn btn-success me-2" onclick="exportExcel()">⬇️ Export Excel</button>
    <button class="btn btn-danger me-2" onclick="exportPDF()">⬇️ Export PDF</button>
    <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
  </div>

  <div class="row" id="reportContent">
    <!-- Transactions -->
    <div class="col-md-6">
      <div class="card p-3 mb-4 shadow">
        <h5 class="text-center">Monthly Transactions</h5>
        <canvas id="transactionChart"></canvas>
      </div>
    </div>

    <!-- Properties -->
    <div class="col-md-6">
      <div class="card p-3 mb-4 shadow">
        <h5 class="text-center">Properties by Status</h5>
        <canvas id="propertyChart"></canvas>
      </div>
    </div>

    <!-- Agent Performance -->
    <div class="col-md-12">
      <div class="card p-3 mb-4 shadow">
        <h5 class="text-center">Agent Performance</h5>
        <canvas id="agentChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
// ✅ Transactions Chart
const transactionCtx = document.getElementById('transactionChart').getContext('2d');
new Chart(transactionCtx, {
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

// ✅ Properties Chart
const propertyCtx = document.getElementById('propertyChart').getContext('2d');
new Chart(propertyCtx, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($properties, 'status')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($properties, 'total')) ?>,
      backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
    }]
  }
});

// ✅ Agent Performance Chart
const agentCtx = document.getElementById('agentChart').getContext('2d');
new Chart(agentCtx, {
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
    scales: {
      y: {
        beginAtZero: true,
        ticks: { stepSize: 1 }
      }
    }
  }
});

// ✅ Export to Excel
function exportExcel() {
  let wb = XLSX.utils.book_new();
  let ws_data = [
    ["📊 Reports & Analytics"],
    [],
    ["Month", "Transactions"],
    ...<?= json_encode($transactions) ?>.map(row => [row.month, row.total]),
    [],
    ["Status", "Total Properties"],
    ...<?= json_encode($properties) ?>.map(row => [row.status, row.total]),
    [],
    ["Agent", "Total Transactions"],
    ...<?= json_encode($agents) ?>.map(row => [row.name, row.total])
  ];
  let ws = XLSX.utils.aoa_to_sheet(ws_data);
  XLSX.utils.book_append_sheet(wb, ws, "Report");
  XLSX.writeFile(wb, "real_estate_report.xlsx");
}

// ✅ Export to PDF
function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  doc.setFontSize(16);
  doc.text("📊 Real Estate Report", 20, 20);
  doc.setFontSize(12);
  doc.text("Transactions, Properties, and Agent Performance overview.", 20, 30);
  doc.save("real_estate_report.pdf");
}
</script>
</body>
</html>
