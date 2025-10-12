<?php
require_once __DIR__ . "/admin/partials/db.php"; // ✅ adjust if db.php path is different

// Fetch categories
$stmt = $conn->query("SELECT * FROM property_categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Property Categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<div class="container py-5 bg-light">
  <h2 class="mb-4 text-center">Property Categories</h2>

  <div class="row g-4">
    <?php if ($categories): ?>
      <?php foreach ($categories as $cat): ?>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <?php if (!empty($cat['photo'])): ?>
              <img src="admin/uploads/<?= htmlspecialchars($cat['photo']) ?>" 
                   class="card-img-top" 
                   alt="<?= htmlspecialchars($cat['name']) ?>" 
                   style="height:200px; object-fit:cover;">
            <?php else: ?>
              <img src="https://via.placeholder.com/400x200?text=No+Image" 
                   class="card-img-top" 
                   alt="No image">
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($cat['name']) ?></h5>
              <p class="card-text text-muted">
                <?= htmlspecialchars(substr($cat['description'], 0, 100)) ?>...
              </p>
              <div class="mt-auto">
                <a href="property_list.php?cat_id=<?= $cat['id'] ?>" 
                   class="btn btn-outline-primary btn-sm">
                  View Properties →
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-center">
        <p class="text-muted">No categories found.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

