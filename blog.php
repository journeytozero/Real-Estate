<?php
include __DIR__ . '/config/db.php';
// ---------- settings ----------
$perPage     = 9;                                  // cards per page
$page        = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($page - 1) * $perPage;
$uploadsUrl  = 'admin/uploads/blogs/';             // change to 'uploads/blogs/' if that's your path

// helpers
function hi($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function excerpt($html, $len = 140){
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string)$html)));
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text) > $len ? mb_substr($text, 0, $len - 1) . '…' : $text;
    }
    return strlen($text) > $len ? substr($text, 0, $len - 1) . '…' : $text;
}

// total count
$total = (int)$conn->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// fetch page
$stmt = $conn->prepare("
    SELECT id, title, content, photo, created_at
    FROM blogs
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Real Estate Blog</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php //include __DIR__ . '/includes/navbar.php'; ?> 

<div class="container py-5">
  <h1 class="text-center mb-4">Real Estate Blog</h1>

  <?php if (!$blogs): ?>
    <div class="alert alert-info">No blog posts yet. Please check back soon.</div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($blogs as $b): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <?php if (!empty($b['photo'])): ?>
              <img
                src="<?= hi($uploadsUrl . $b['photo']) ?>"
                class="card-img-top"
                alt="<?= hi($b['title']) ?>"
                loading="lazy">
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1"><?= hi($b['title']) ?></h5>
              <small class="text-muted mb-2">
                <?= date('M j, Y', strtotime($b['created_at'])) ?>
              </small>
              <p class="card-text flex-grow-1">
                <?= hi(excerpt($b['content'], 140)) ?>
              </p>
              <a href="blog_single.php?id=<?= (int)$b['id'] ?>" class="btn btn-primary mt-auto">
                Read More
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav aria-label="Blog pagination">
        <ul class="pagination justify-content-center">
          <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">Previous</a>
          </li>

          <?php
          // simple windowed pager
          $start = max(1, $page - 2);
          $end   = min($totalPages, $page + 2);
          if ($start > 1) {
              echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
              if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
          }
          for ($p = $start; $p <= $end; $p++):
          ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
            </li>
          <?php endfor;
          if ($end < $totalPages) {
              if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
              echo '<li class="page-item"><a class="page-link" href="?page='.$totalPages.'">'.$totalPages.'</a></li>';
          }
          ?>

          <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- <?php include __DIR__ . '/includes/footer.php'; ?> -->
</body>
</html>
