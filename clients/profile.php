<?php require_once __DIR__ . "/../includes/header.php"; ?>
<?php require_once __DIR__ . "/../includes/sidebar_client.php"; ?>

<?php
$client_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM clients WHERE id=?");
$stmt->execute([$client_id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="card shadow">
  <div class="card-header bg-primary text-white">
    <h4>👤 My Profile</h4>
  </div>
  <div class="card-body">
    <table class="table table-bordered">
      <tr><th>Name</th><td><?php echo htmlspecialchars($client['name']); ?></td></tr>
      <tr><th>Email</th><td><?php echo htmlspecialchars($client['email']); ?></td></tr>
      <tr><th>Phone</th><td><?php echo htmlspecialchars($client['phone']); ?></td></tr>
    </table>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
