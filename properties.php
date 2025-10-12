<?php
// FILE: properties.php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

// --- 1. Database Fetching Logic ---

// Set pagination parameters
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Prepare a single, efficient query to fetch property details,
// including the first photo and agent name.
$stmt = $conn->prepare("
    SELECT 
        p.id, p.name, p.location, p.price, p.status, 
        a.name AS agent_name,
        ph.photo AS main_photo
    FROM properties p
    LEFT JOIN agents a ON p.agent_id = a.id
    LEFT JOIN (
        SELECT property_id, MIN(id) AS first_photo_id
        FROM property_photos
        GROUP BY property_id
    ) AS first_photo_subquery ON p.id = first_photo_subquery.property_id
    LEFT JOIN property_photos ph ON ph.id = first_photo_subquery.first_photo_id
    ORDER BY p.id DESC
    LIMIT ? OFFSET ?
");

// Bind values and execute the query securely
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container my-5">
    <h2 class="mb-4 text-primary">All Properties</h2>

    <div class="row g-4">
        <?php if ($properties): ?>
            <?php foreach ($properties as $prop): ?>
                <?php
                // Set the correct photo URL or a placeholder if none exists.
                $photoUrl = !empty($prop['main_photo'])
                    ? "admin/uploads/" . htmlspecialchars($prop['main_photo'])
                    : "https://via.placeholder.com/400x250?text=No+Image+Available";

                // Use a 'match' expression for clean status styling.
                $statusClass = match ($prop['status']) {
                    'sold' => 'danger',
                    'pending' => 'warning',
                    'rented' => 'info',
                    default => 'success', // 'available' and others
                };
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <img src="<?= $photoUrl ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($prop['name']) ?>" 
                             style="height: 250px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($prop['name']) ?></h5>
                            
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= htmlspecialchars($prop['location']) ?>
                            </p>
                            
                            <h6 class="fw-bold text-primary mb-3"><?= number_format($prop['price']) ?> TK</h6>
                            
                            <p class="mb-3">
                                <strong>Status:</strong> 
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst(htmlspecialchars($prop['status'])) ?>
                                </span>
                            </p>

                            <div class="mt-auto text-end">
                                <a href="property_detail.php?id=<?= $prop['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col">
                <p class="text-center text-muted">No properties found at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>