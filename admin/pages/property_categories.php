<?php
require_once __DIR__ . "/../partials/db.php";

// Fetch all categories
$stmt = $conn->query("SELECT * FROM property_categories ORDER BY created_at DESC");
$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>🏷️ Property Categories</h3>
    <a href="idx.php?page=property_category_form" class="btn btn-primary">➕ Add Category</a>
  </div>

  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Description</th>
        <th>Photo</th>
        <th>Created</th>
        <th class="text-center" style="width:180px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($cats): foreach ($cats as $i => $c): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($c['name']) ?></td>
          <td><?= nl2br(htmlspecialchars($c['description'] ?? '')) ?></td>
          <td>
            <?php if (!empty($c['photo'])): ?>
              <img src="uploads/<?= htmlspecialchars($c['photo']) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
            <?php else: ?>
              <span class="text-muted">No photo</span>
            <?php endif; ?>
          </td>
          <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
          <td class="text-center">
            <a class="btn btn-sm btn-warning" href="idx.php?page=property_category_form&id=<?= $c['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this category?');"
               href="idx.php?page=property_category_delete&id=<?= $c['id'] ?>">Delete</a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center">No categories found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
