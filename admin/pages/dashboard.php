<?php
// -----------------------------
// Dashboard Queries
// -----------------------------

// Total counts
$totalProperties = $conn->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$totalAgents     = $conn->query("SELECT COUNT(*) FROM agents")->fetchColumn();
$totalClients    = $conn->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$totalRevenue    = $conn->query("
    SELECT COALESCE(SUM(price), 0) 
    FROM properties 
    WHERE status = 'Sold'
")->fetchColumn();

// Rented revenue: SUM of currently active rentals
$rentedRevenue = $conn->query("
    SELECT COALESCE(SUM(p.price), 0)
    FROM rented r
    JOIN properties p ON r.property_id = p.id
    WHERE r.rented_start_date <= CURDATE()
      AND (r.rented_end_date IS NULL OR r.rented_end_date >= CURDATE())
")->fetchColumn();

// Ongoing Projects
$ongoingProjects = $conn->query("
    SELECT COUNT(*) 
    FROM properties 
    WHERE status = 'Ongoing'
")->fetchColumn();

// Property counts by status
$statusCounts = $conn->query("
    SELECT status, COUNT(*) AS count
    FROM properties
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Revenue by status (Sold & Rented only)
$revenueByStatus = $conn->query("
    SELECT status, COALESCE(SUM(price),0) AS total
    FROM properties
    WHERE status IN ('Sold','Rented')
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Define all statuses for consistent order
$allStatuses = ['Available','Sold','Rented','Ongoing'];
$counts = [];
$revenues = [];
foreach($allStatuses as $status){
    $counts[] = $statusCounts[$status] ?? 0;
    $revenues[] = $revenueByStatus[$status] ?? 0;
}

// Profile photo
$profilePhoto = !empty($_SESSION['admin_photo']) 
    ? htmlspecialchars($_SESSION['admin_photo']) 
    : "default.png";

// -----------------------------
// Monthly Revenue Trend (Last 12 Months)
// -----------------------------
$months = [];
for($i = 11; $i >= 0; $i--){
    $months[] = date('M Y', strtotime("-$i month"));
}

$soldRevenueMonthly = array_fill(0, 12, 0);
$rentedRevenueMonthly = array_fill(0, 12, 0);

// Sold revenue per month
$stmtSold = $conn->query("
    SELECT DATE_FORMAT(created_at, '%b %Y') AS month, SUM(price) AS total
    FROM properties
    WHERE status = 'Sold'
    GROUP BY month
");
$soldData = $stmtSold->fetchAll(PDO::FETCH_KEY_PAIR);

// Rented revenue per month
$stmtRented = $conn->query("
    SELECT DATE_FORMAT(rented_start_date, '%b %Y') AS month, SUM(p.price) AS total
    FROM rented r
    JOIN properties p ON r.property_id = p.id
    WHERE r.rented_start_date <= CURDATE()
      AND (r.rented_end_date IS NULL OR r.rented_end_date >= CURDATE())
    GROUP BY month
");

$grandTotalRevenue = $rentedRevenue + $totalRevenue;
// Add "Total Revenue" as extra dataset point
$allStatusesWithTotal = array_merge($allStatuses, ['Total Revenue']);
$countsWithTotal = array_merge($counts, [null]); // no property count for total
$revenuesWithTotal = array_merge($revenues, [$grandTotalRevenue]);

$rentedData = $stmtRented->fetchAll(PDO::FETCH_KEY_PAIR);

// Map revenues to last 12 months
foreach($months as $index => $month){
    $soldRevenueMonthly[$index] = $soldData[$month] ?? 0;
    $rentedRevenueMonthly[$index] = $rentedData[$month] ?? 0;
}

// Total revenue per month (Sold + Rented)
$totalRevenueMonthly = [];
foreach ($months as $index => $month) {
    $totalRevenueMonthly[$index] = ($soldRevenueMonthly[$index] ?? 0) + ($rentedRevenueMonthly[$index] ?? 0);
}

?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-3">
  <div class="container-fluid">
    <button class="btn btn-dark" id="menu-toggle">☰</button>
    <h5 class="ms-3 mb-0">Dashboard</h5>
    <div class="ms-auto d-flex align-items-center gap-2">

      <!-- Notifications -->
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">New property added</a></li>
          <li><a class="dropdown-item" href="#">Agent registered</a></li>
          <li><a class="dropdown-item text-muted" href="#">No new notifications</a></li>
        </ul>
      </div>

      <!-- Profile -->
      <div class="dropdown">
        <button class="btn p-0 border-0 bg-transparent dropdown-toggle" data-bs-toggle="dropdown">
          <img src="../admin/uploads/<?= $profilePhoto ?>" 
               alt="Profile" 
               class="rounded-circle" 
               style="width:40px; height:40px; object-fit:cover;">
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="idx.php?page=profile">Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
        </ul>
      </div>

    </div>
  </div>
</nav>

<!-- Dashboard Content -->
<div class="container-fluid p-4">

  <!-- Top Summary Cards -->
  <div class="row">
    <div class="col-md-3 mb-3">
      <div class="card text-bg-primary shadow">
        <div class="card-body">
          <h5>Total Properties</h5>
          <h3><?= (int)$totalProperties ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-bg-success shadow">
        <div class="card-body">
          <h5>Active Agents</h5>
          <h3><?= (int)$totalAgents ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-bg-warning shadow">
        <div class="card-body">
          <h5>Clients</h5>
          <h3><?= (int)$totalClients ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-bg-danger shadow">
        <div class="card-body">
          <h5>Ongoing Projects</h5>
          <h3><?= (int)$ongoingProjects ?></h3>
        </div>
      </div>
    </div>
  </div>

      <!-- Revenue Cards -->
  <div class="row mt-3">
    <div class="col-md-4 mb-3">
      <div class="card text-bg-info shadow">
        <div class="card-body">
          <h5>Rented Revenue</h5>
          <h3>$<?= number_format($rentedRevenue, 2) ?></h3>
          <small class="text-white">Current rentals</small>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card text-bg-secondary shadow">
        <div class="card-body">
          <h5>Property Revenue</h5>
          <h3>$<?= number_format($totalRevenue, 2) ?></h3>
          <small class="text-white">Property Revenue</small>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card text-bg-dark shadow">
        <div class="card-body">
          <h5>Total Revenue</h5>
          <h3>$<?= number_format($grandTotalRevenue, 2) ?></h3>
          <small class="text-white">Rented + Property</small>
        </div>
      </div>
    </div>
  </div>



  <!-- Charts Row -->
  <div class="row mt-4">
    <div class="col-lg-6 mb-4">
      <div class="card shadow">
        <div class="card-header"><h5 class="mb-0">Properties Overview</h5></div>
        <div class="card-body"><canvas id="dashboardChart" style="height:300px;"></canvas></div>
      </div>
    </div>
    <div class="col-lg-6 mb-4">
      <div class="card shadow">
        <div class="card-header"><h5 class="mb-0">Monthly Revenue Trend</h5></div>
        <div class="card-body"><canvas id="monthlyRevenueChart" style="height:300px;"></canvas></div>
      </div>
    </div>
  </div>

    <!-- Recent Properties Table -->
  <div class="card shadow mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Recent Properties</h5>
      <a href="idx.php?page=property_form" class="btn btn-sm btn-primary">Add Property</a>
    </div>
    <div class="card-body">
      <table class="table table-hover">
        <thead class="table-light">
          <tr>
            <th>Property</th>
            <th>Location</th>
            <th>Agent</th>
            <th>Price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $conn->query("
            SELECT p.name, p.location, a.name AS agent, p.price, p.status
            FROM properties p
            JOIN agents a ON p.agent_id = a.id
            ORDER BY p.id DESC
            LIMIT 5
          ");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td><?= htmlspecialchars($row['agent']) ?></td>
              <td>$<?= number_format($row['price'], 2) ?></td>
              <td>
                <?php
                switch($row['status']){
                    case 'Available': echo '<span class="badge bg-success">Available</span>'; break;
                    case 'Sold': echo '<span class="badge bg-danger">Sold</span>'; break;
                    case 'Rented': echo '<span class="badge bg-info">Rented</span>'; break;
                    default: echo '<span class="badge bg-warning">Ongoing</span>';
                }
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="3" class="text-end">Total Revenue:</th>
            <th colspan="2">
              $<?= number_format($grandTotalRevenue, 2) ?>
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// =============================
// Properties Overview Chart
// =============================
const ctx = document.getElementById('dashboardChart').getContext('2d');
const labels = <?= json_encode($allStatusesWithTotal) ?>; // includes "Total Revenue"
const propertyCounts = <?= json_encode($countsWithTotal) ?>;
const revenueData = <?= json_encode($revenuesWithTotal) ?>;

const statusColors = {
    'Available': 'rgba(75,192,192,0.7)',
    'Sold': 'rgba(255,99,132,0.7)',
    'Rented': 'rgba(54,162,235,0.7)',
    'Ongoing': 'rgba(255,206,86,0.7)',
    'Total Revenue': 'rgba(0,0,0,0.7)'
};
const borderColors = {
    'Available': 'rgba(75,192,192,1)',
    'Sold': 'rgba(255,99,132,1)',
    'Rented': 'rgba(54,162,235,1)',
    'Ongoing': 'rgba(255,206,86,1)',
    'Total Revenue': 'rgba(0,0,0,1)'
};

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Number of Properties',
                data: propertyCounts,
                backgroundColor: labels.map(l => statusColors[l]),
                borderColor: labels.map(l => borderColors[l]),
                borderWidth: 1
            },
            {
                label: 'Revenue ($)',
                data: revenueData,
                type: 'line',
                borderColor: 'rgba(153,102,255,1)',
                backgroundColor: 'rgba(153,102,255,0.2)',
                yAxisID: 'y1',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive:true,
        interaction:{mode:'index',intersect:false},
        stacked:false,
        plugins:{
            tooltip:{
                callbacks:{
                    label:function(ctx){
                        return ctx.dataset.label==='Revenue ($)'?
                            `${ctx.dataset.label}: $${ctx.raw?.toLocaleString()}`:
                            (ctx.raw !== null ? `${ctx.dataset.label}: ${ctx.raw}` : '');
                    }
                }
            }
        },
        scales:{
            y:{beginAtZero:true,title:{display:true,text:'Property Count'}},
            y1:{beginAtZero:true,position:'right',title:{display:true,text:'Revenue ($)'},grid:{drawOnChartArea:false}}
        }
    }
});

// =============================
// Monthly Revenue Trend Chart
// =============================
const ctxMonth = document.getElementById('monthlyRevenueChart').getContext('2d');
new Chart(ctxMonth,{
    type:'line',
    data:{
        labels: <?= json_encode($months) ?>,
        datasets:[
            {
                label:'Sold Revenue ($)',
                data: <?= json_encode($soldRevenueMonthly) ?>,
                borderColor:'rgba(255,99,132,1)',
                backgroundColor:'rgba(255,99,132,0.2)',
                tension:0.3,
                fill:true
            },
            {
                label:'Rented Revenue ($)',
                data: <?= json_encode($rentedRevenueMonthly) ?>,
                borderColor:'rgba(54,162,235,1)',
                backgroundColor:'rgba(54,162,235,0.2)',
                tension:0.3,
                fill:true
            },
            {
                label:'Total Revenue ($)',
                data: <?= json_encode($totalRevenueMonthly) ?>,
                borderColor:'rgba(0,0,0,1)',
                backgroundColor:'rgba(0,0,0,0.2)',
                borderWidth:2,
                tension:0.3,
                fill:true
            }
        ]
    },
    options:{
        responsive:true,
        plugins:{
            tooltip:{
                callbacks:{
                    label:function(ctx){
                        return `${ctx.dataset.label}: $${ctx.raw.toLocaleString()}`;
                    }
                }
            }
        },
        scales:{y:{beginAtZero:true,title:{display:true,text:'Revenue ($)'}}}
    }
});
</script>
