<?php
// ✅ Secure admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<div class="container p-4">
  <!-- ✅ Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Properties</h3>
    <a href="idx.php?page=property_form" class="btn btn-sm btn-primary">+ Add Property</a>
  </div>

  <!-- ✅ Success / Error Messages -->
  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_GET['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- ✅ Properties Table -->
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Location</th>
        <th>Price</th>
        <th>Status</th>
        <th>Category</th>
        <th>Agent</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $conn->query("SELECT p.*, a.name AS agent_name, c.name AS category_name
                            FROM properties p
                            LEFT JOIN agents a ON p.agent_id = a.id
                            LEFT JOIN property_categories c ON p.category_id = c.id
                            ORDER BY p.id DESC");
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
          // ✅ Get first photo
          $photoStmt = $conn->prepare("SELECT photo FROM property_photos WHERE property_id=? LIMIT 1");
          $photoStmt->execute([$row['id']]);
          $photo = $photoStmt->fetchColumn();
      ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td>
            <?php if ($photo): ?>
              <img src="uploads/<?= htmlspecialchars($photo) ?>" alt="Photo"
                   style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
            <?php else: ?>
              <span class="text-muted">No Image</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['location']) ?></td>
          <td><?= number_format($row['price']) ?> TK</td>
          <td>
            <span class="badge bg-<?= $row['status']=='Available'?'success':($row['status']=='Sold'?'danger':'warning') ?>">
              <?= htmlspecialchars($row['status']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
          <td><?= htmlspecialchars($row['agent_name'] ?? '-') ?></td>
          <td>
            <a href="idx.php?page=property_form&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
            <a href="idx.php?page=property_delete&id=<?= $row['id'] ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure to delete this property?')"><i class="bi bi-folder-x"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
