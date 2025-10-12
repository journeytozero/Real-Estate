<?php
// Fetch clients
$stmt = $conn->query("SELECT * FROM clients ORDER BY id DESC");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Show success messages if set
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!empty($_SESSION['success'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}
?>

<div class="container p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Clients</h3>
    <a href="idx.php?page=client_form" class="btn btn-primary">➕ Add Client</a>
  </div>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($clients as $c): ?>
        <tr>
          <td><?= $c['id'] ?></td>
          <td><?= htmlspecialchars($c['name']) ?></td>
          <td><?= htmlspecialchars($c['email']) ?></td>
          <td><?= htmlspecialchars($c['phone']) ?></td>
          <td>
            <a href="index.php?page=client_form&id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="index.php?page=client_delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this client?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
