<?php
require_once __DIR__ . "/admin/partials/db.php";

$limit = 6; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total properties
$totalStmt = $conn->query("SELECT COUNT(*) FROM properties");
$total = $totalStmt->fetchColumn();

// ✅ Fetch properties with first photo
$stmt = $conn->prepare("
    SELECT 
        p.*, 
        a.name AS agent_name,
        ph.photo AS main_photo
    FROM properties p
    LEFT JOIN agents a ON p.agent_id = a.id
    LEFT JOIN (
        SELECT property_id, MIN(id) AS first_photo_id
        FROM property_photos
        GROUP BY property_id
    ) x ON p.id = x.property_id
    LEFT JOIN property_photos ph ON ph.id = x.first_photo_id
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
        ? "admin/uploads/" . $prop['main_photo'] 
        : "https://via.placeholder.com/400x200?text=No+Image";

    $statusClass = match ($prop['status']) {
        'available' => 'primary',
        'sold' => 'danger',
        'pending' => 'warning',
        default => 'secondary'
    };

    $html .= '
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <img src="' . htmlspecialchars($photo) . '" 
                 class="card-img-top" 
                 alt="' . htmlspecialchars($prop['name']) . '" 
                 style="height:200px; object-fit:cover;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">' . htmlspecialchars($prop['name']) . '</h5>
                <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>' . htmlspecialchars($prop['location']) . '</p>
                <p class="fw-bold text-primary">' . number_format($prop['price']) . ' TK</p>
                <p class="mb-1">
                    <strong>Status:</strong> <span class="text-' . $statusClass . '">' . ucfirst($prop['status']) . '</span>
                </p>
                <div class="mt-auto">
                    <a href="property_detail.php?id=' . $prop['id'] . '" class="btn btn-outline-primary btn-sm mt-2">View Details</a>
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
