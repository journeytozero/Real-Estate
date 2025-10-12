<?php
require_once __DIR__ . "/admin/partials/db.php";

// Get category ID from URL
$cat_id = $_GET['cat_id'] ?? null;

if (!$cat_id) {
    die("Category not specified.");
}

// Fetch category info
$catStmt = $conn->prepare("SELECT * FROM property_categories WHERE id=? LIMIT 1");
$catStmt->execute([$cat_id]);
$category = $catStmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found.");
}

// ✅ Fetch properties + first photo in ONE query
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
    WHERE p.category_id = ?
    ORDER BY p.id DESC
");
$stmt->execute([$cat_id]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($category['name']) ?> - Properties</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="mb-4 text-center">
    <?= htmlspecialchars($category['name']) ?> - Properties
  </h2>

  <div class="row g-4">
    <?php if ($properties): ?>
      <?php foreach ($properties as $prop): ?>
        <?php
          $photo = $prop['main_photo']
              ? "admin/uploads/" . $prop['main_photo']
              : "https://via.placeholder.com/400x200?text=No+Image";

          $statusClass = match(strtolower($prop['status'])) {
              'available' => 'primary',
              'pending'   => 'warning',
              'sold'      => 'danger',
              default     => 'secondary'
          };
        ?>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <img src="<?= htmlspecialchars($photo) ?>" 
                 class="card-img-top" 
                 alt="<?= htmlspecialchars($prop['name']) ?>" 
                 style="height:200px; object-fit:cover;">

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($prop['name']) ?></h5>

              <p class="card-text text-muted">
                Location: <?= htmlspecialchars($prop['location']) ?><br>
                Price: <?= number_format($prop['price']) ?> TK<br>
                Agent: <?= htmlspecialchars($prop['agent_name'] ?? "N/A") ?>
              </p>

              <div class="mt-auto d-flex justify-content-between">
                <a href="property_detail.php?id=<?= $prop['id'] ?>" 
                   class="btn btn-primary btn-sm">
                  View Details
                </a>

                <span class="btn btn-<?= $statusClass ?> btn-sm">
                  <?= ucfirst($prop['status']) ?>
                </span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-center">
        <p class="text-muted">No properties found in this category.</p>
      </div>
    <?php endif; ?>
  </div>

  <div class="mt-4 text-center">
    <a href="categories.php" class="btn btn-secondary btn-sm">← Back to Categories</a>
  </div>
</div>

</body>
</html>
