<?php require_once __DIR__ . "/../includes/header.php"; ?>
<?php require_once __DIR__ . "/../includes/sidebar_client.php"; ?>

<?php
$sql = "SELECT p.*, (SELECT photo FROM property_photos WHERE property_id=p.id LIMIT 1) AS photo
        FROM properties p WHERE p.status='available'";
$props = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">🔍 Available Properties</h2>
<div class="row">
  <?php foreach ($props as $p): ?>
    <div class="col-md-4">
      <div class="card mb-4 shadow-sm">
        <?php if ($p['photo']): ?>
          <img src="../uploads/<?php echo $p['photo']; ?>" class="card-img-top" style="height:200px;object-fit:cover;">
        <?php endif; ?>
        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
          <p class="card-text">
            📍 <?php echo htmlspecialchars($p['location']); ?><br>
            💵 Price: $<?php echo $p['price']; ?>
          </p>
          <a href="contact_agent.php?property_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Contact Agent</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
