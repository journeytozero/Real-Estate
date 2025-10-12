<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total count
$totalStmt = $conn->query("SELECT COUNT(*) FROM properties");
$total = $totalStmt->fetchColumn();

// Fetch properties
$stmt = $conn->prepare("
    SELECT p.*, a.name AS agent_name, ph.photo AS main_photo
    FROM properties p
    LEFT JOIN agents a ON p.agent_id = a.id
    LEFT JOIN (
        SELECT property_id, MIN(id) AS first_photo
        FROM property_photos GROUP BY property_id
    ) x ON p.id = x.property_id
    LEFT JOIN property_photos ph ON ph.id = x.first_photo
    ORDER BY p.id DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = "";
foreach ($properties as $prop) {
    $photo = $prop['main_photo']
        ? " ../../admin/uploads/" . $prop['main_photo']
        : "https://via.placeholder.com/400x200?text=No+Image";

    $statusClass = match ($prop['status']) {
        'available' => 'success',
        'sold'      => 'danger',
        'pending'   => 'warning',
        default     => 'secondary'
    };

    // Saved?
    $isSaved = false;
    if (isset($_SESSION['client_id'])) {
        $chk = $conn->prepare("SELECT 1 FROM saved_properties WHERE client_id = ? AND property_id = ?");
        $chk->execute([$_SESSION['client_id'], $prop['id']]);
        $isSaved = $chk->fetchColumn() ? true : false;
    }
    $saveClass = $isSaved ? "btn-danger" : "btn-outline-danger";

    $html .= '
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <img src="' . htmlspecialchars($photo) . '" class="card-img-top" style="height:200px;object-fit:cover;">
            <div class="card-body d-flex flex-column">
                <h5>' . htmlspecialchars($prop['name']) . '</h5>
                <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>' . htmlspecialchars($prop['location']) . '</p>
                <p class="fw-bold text-primary">' . number_format($prop['price']) . ' TK</p>
                <p>Status: <span class="badge bg-' . $statusClass . '">' . ucfirst($prop['status']) . '</span></p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <a href="property_detail.php?id=' . $prop['id'] . '" class="btn btn-sm btn-outline-primary">View</a>
                    <button class="btn btn-sm ' . $saveClass . ' save-btn" data-id="' . $prop['id'] . '">
                        <i class="fas fa-heart"></i>
                    </button>
                    <input type="checkbox" class="form-check-input compare-check" data-id="' . $prop['id'] . '">
                </div>
            </div>
        </div>
    </div>';
}

header('Content-Type: application/json');
echo json_encode([
    "html" => $html,
    "hasMore" => ($total > $page * $limit)
]);
