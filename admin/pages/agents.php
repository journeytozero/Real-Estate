<div class="container-fluid p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Agents</h3>
    <a href="idx.php?page=agent_form" class="btn btn-primary">Add Agent</a>
  </div>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $conn->query("SELECT * FROM agents ORDER BY id DESC");
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
          $photo = !empty($row['photo']) ? "uploads/agents/" . htmlspecialchars($row['photo']) : "https://via.placeholder.com/50";
      ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td>
            <img src="<?= $photo ?>" alt="Agent Photo" width="50" height="50" class="rounded-circle">
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>
            <a href="idx.php?page=agent_form&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="pages/agent_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this agent?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

