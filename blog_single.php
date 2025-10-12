<?php
// public/blog_single.php

include __DIR__ . '/config/db.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$uploadsUrl = 'admin/uploads/blogs/'; // change to 'uploads/blogs/' if that’s your path

$id = max(0, (int)($_GET['id'] ?? 0));
if ($id <= 0) {
  http_response_code(404);
  $blog = null;
} else {
  $stmt = $conn->prepare("SELECT id, title, content, photo, created_at FROM blogs WHERE id = :id");
  $stmt->execute([':id' => $id]);
  $blog = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$blog) http_response_code(404);
}

// Prev / Next (by id)
$prev = $next = null;
if ($blog) {
  $p = $conn->prepare("SELECT id, title FROM blogs WHERE id < :id ORDER BY id DESC LIMIT 1");
  $p->execute([':id' => $blog['id']]);
  $prev = $p->fetch(PDO::FETCH_ASSOC);

  $n = $conn->prepare("SELECT id, title FROM blogs WHERE id > :id ORDER BY id ASC LIMIT 1");
  $n->execute([':id' => $blog['id']]);
  $next = $n->fetch(PDO::FETCH_ASSOC);
}

// Related (latest 3 excluding current)
$related = [];
if ($blog) {
  $r = $conn->prepare("
    SELECT id, title, photo FROM blogs
    WHERE id <> :id
    ORDER BY created_at DESC
    LIMIT 3
  ");
  $r->execute([':id' => $blog['id']]);
  $related = $r->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $blog ? h($blog['title']) . ' — Real Estate Blog' : 'Post not found — Real Estate Blog' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if ($blog): ?>
    <meta name="description" content="<?= h(mb_substr(trim(strip_tags($blog['content'])), 0, 150)) ?>">
    <meta property="og:title" content="<?= h($blog['title']) ?>">
    <meta property="og:type" content="article">
    <?php if (!empty($blog['photo'])): ?>
      <meta property="og:image" content="<?= h($uploadsUrl . $blog['photo']) ?>">
    <?php endif; ?>
  <?php endif; ?>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>


<div class="container py-5">

  <?php if (!$blog): ?>
    <div class="alert alert-warning">Sorry, this post was not found.</div>
    <a href="blog.php" class="btn btn-secondary">Back to Blog</a>

  <?php else: ?>
    <nav class="mb-3">
      <a href="blog.php" class="text-decoration-none">&larr; All posts</a>
    </nav>

    <article class="mb-4">
      <h1 class="mb-2"><?= h($blog['title']) ?></h1>
      <small class="text-muted d-block mb-3"><?= date('M j, Y', strtotime($blog['created_at'])) ?></small>

      <?php if (!empty($blog['photo'])): ?>
        <img src="<?= h($uploadsUrl . $blog['photo']) ?>" alt="<?= h($blog['title']) ?>"
             class="img-fluid mb-4 rounded" loading="lazy">
      <?php endif; ?>

      <!-- Content from admin; assumed trusted -->
      <div class="blog-content">
        <?= $blog['content'] ?>
      </div>
    </article>

    <div class="d-flex justify-content-between mb-5">
      <div>
        <?php if ($prev): ?>
          <a class="btn btn-outline-secondary" href="blog_single.php?id=<?= (int)$prev['id'] ?>">
            &larr; <?= h($prev['title']) ?>
          </a>
        <?php endif; ?>
      </div>
      <div>
        <?php if ($next): ?>
          <a class="btn btn-outline-secondary" href="blog_single.php?id=<?= (int)$next['id'] ?>">
            <?= h($next['title']) ?> &rarr;
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($related): ?>
      <h3 class="h5 mb-3">Related posts</h3>
      <div class="row">
        <?php foreach ($related as $rel): ?>
          <div class="col-md-4 mb-4">
            <a class="text-decoration-none" href="blog_single.php?id=<?= (int)$rel['id'] ?>">
              <div class="card h-100">
                <?php if (!empty($rel['photo'])): ?>
                  <img src="<?= h($uploadsUrl . $rel['photo']) ?>" class="card-img-top" alt="<?= h($rel['title']) ?>" loading="lazy">
                <?php endif; ?>
                <div class="card-body">
                  <h6 class="card-title mb-0"><?= h($rel['title']) ?></h6>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
