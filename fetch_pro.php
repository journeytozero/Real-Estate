<?php
require_once __DIR__ . "/config/db.php";

$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$category_id = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int) $_GET['category_id'] : null;
$location = isset($_GET['location']) ? trim($_GET['location']) : null;
$status = isset($_GET['status']) ? trim($_GET['status']) : null;

// Base query to get properties and first photo
$sql = "
    SELECT 
        p.*,
        ph.photo AS main_photo
    FROM properties p
    LEFT JOIN (
        SELECT property_id, MIN(id) AS first_photo_id
        FROM property_photos
        GROUP BY property_id
    ) first_photo ON p.id = first_photo.property_id
    LEFT JOIN property_photos ph ON ph.id = first_photo.first_photo_id
    WHERE 1
";

$params = [];

// Apply filters
if ($category_id !== null) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($location)) {
    $sql .= " AND p.location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($status)) {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}

// Count total rows matching filters for pagination
$countSql = "SELECT COUNT(*) FROM properties p WHERE 1";
$countParams = [];

if ($category_id !== null) {
    $countSql .= " AND p.category_id = ?";
    $countParams[] = $category_id;
}

if (!empty($location)) {
    $countSql .= " AND p.location LIKE ?";
    $countParams[] = "%$location%";
}

if (!empty($status)) {
    $countSql .= " AND p.status = ?";
    $countParams[] = $status;
}

// Execute count query
$countStmt = $conn->prepare($countSql);
$countStmt->execute($countParams);
$total = (int) $countStmt->fetchColumn();

// Append ordering and pagination (limit, offset) - here we must inject directly as integers, not as bound params
$sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);

// Bind filter params only (limit and offset are inlined)
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}

$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare HTML output
$html = '';
$statusClassMap = [
    'Available' => 'primary',
    'Sold' => 'danger',
    'Pending' => 'warning',
    'Rented' => 'info',
];

foreach ($properties as $prop) {
    $photo = $prop['main_photo']
        ? "admin/uploads/" . htmlspecialchars($prop['main_photo'])
        : "https://via.placeholder.com/400x200?text=No+Image";

    $statusClass = $statusClassMap[$prop['status']] ?? 'secondary';

    $html .= '
    <div class="col-md-4">
        <div class="card property-card shadow-sm border-0 h-100">
            <img src="' . $photo . '" 
                 class="card-img-top" 
                 alt="' . htmlspecialchars($prop['name']) . '" 
                 style="height:220px; object-fit:cover;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">' . htmlspecialchars($prop['name']) . '</h5>
                <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>' . htmlspecialchars($prop['location']) . '</p>
                <p class="fw-bold text-primary">' . number_format($prop['price']) . ' TK</p>
                <p class="mb-1">
                    <strong>Status:</strong> <span class="text-' . $statusClass . '">' . htmlspecialchars($prop['status']) . '</span>
                </p>
                <div class="mt-auto">
                    <a href="property_detail.php?id=' . (int)$prop['id'] . '" class="btn btn-outline-primary btn-sm mt-2">View Details</a>
                </div>
            </div>
        </div>
    </div>';
}

header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'hasMore' => ($total > $page * $limit)
]);
