<?php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../partials/db.php';

// config
$uploadDirFs  = __DIR__ . '/../uploads/blogs/';     
$uploadDirUrl = 'uploads/blogs/';                   
$maxSize      = 5 * 1024 * 1024;                    
$allowedExts  = ['jpg','jpeg','png','webp','gif'];
$allowedMime  = ['image/jpeg','image/png','image/webp','image/gif'];

// Ensure upload dir exists
if (!is_dir($uploadDirFs)) { @mkdir($uploadDirFs, 0775, true); }

// Helpers
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function saveUpload(array $file, string $dirFs, array $allowedExts, array $allowedMime, int $maxSize): ?string {
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > $maxSize) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) return null;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowedMime, true)) return null;

    // Generate safe filename
    $name = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $dest = $dirFs . $name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

    return $name; // return filename only
}

function deletePhotoIfExists(?string $filename, string $dirFs): void {
    if ($filename && is_file($dirFs . $filename)) {
        @unlink($dirFs . $filename);
    }
}

// ---------- CREATE ----------
if (isset($_POST['add_blog'])) {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $photo   = saveUpload($_FILES['photo'] ?? [], $uploadDirFs, $allowedExts, $allowedMime, $maxSize);

    if ($title !== '' && $content !== '') {
        $stmt = $conn->prepare("INSERT INTO blogs (title, content, photo) VALUES (:t,:c,:p)");
        $stmt->execute([':t'=>$title, ':c'=>$content, ':p'=>$photo]);
        header("Location: idx.php?page=blog&msg=added"); exit;
    }
    header("Location: idx.php?page=blog&msg=invalid"); exit;
}

// ---------- DELETE ----------
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT photo FROM blogs WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            deletePhotoIfExists($row['photo'] ?? null, $uploadDirFs);
            $del = $conn->prepare("DELETE FROM blogs WHERE id = :id");
            $del->execute([':id'=>$id]);
            header("Location: idx.php?page=blog&msg=deleted"); exit;
        }
    }
    header("Location: idx.php?page=blog&msg=notfound"); exit;
}

// ---------- UPDATE ----------
if (isset($_POST['update_blog'])) {
    $id      = (int)($_POST['id'] ?? 0);
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $old     = trim($_POST['old_photo'] ?? '');

    if ($id > 0 && $title !== '' && $content !== '') {
        $newPhoto = saveUpload($_FILES['photo'] ?? [], $uploadDirFs, $allowedExts, $allowedMime, $maxSize);
        if ($newPhoto) {
            // delete old photo if replacing
            deletePhotoIfExists($old ?: null, $uploadDirFs);
            $photoToSave = $newPhoto;
        } else {
            $photoToSave = $old; // keep existing
        }

        $stmt = $conn->prepare("UPDATE blogs SET title=:t, content=:c, photo=:p WHERE id=:id");
        $stmt->execute([':t'=>$title, ':c'=>$content, ':p'=>$photoToSave, ':id'=>$id]);
        header("Location: idx.php?page=blog&msg=updated"); exit;
    }
    header("Location: idx.php?page=blog&msg=invalid"); exit;
}

// ---------- READ ----------
$stmt  = $conn->query("SELECT id, title, content, photo, created_at FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Blogs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4">Manage Blogs</h1>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success py-2">
      <?= h(ucfirst($_GET['msg'])) ?> successfully!
    </div>
  <?php endif; ?>

  <!-- Add Blog -->
  <form method="post" enctype="multipart/form-data" class="mb-5 border p-4 rounded shadow-sm bg-light">
    <h4 class="mb-3">Add New Blog</h4>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Content</label>
      <textarea name="content" class="form-control" rows="5" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Photo (max 2MB)</label>
      <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
    </div>
    <button type="submit" name="add_blog" class="btn btn-primary">Add Blog</button>
  </form>

  <!-- List -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th style="width:140px">Photo</th>
          <th>Title</th>
          <th style="width:160px">Created At</th>
          <th style="width:160px">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$blogs): ?>
        <tr><td colspan="4" class="text-center text-muted">No blogs found.</td></tr>
      <?php else: foreach ($blogs as $row): ?>
        <tr>
          <td>
            <?php if (!empty($row['photo'])): ?>
              <img src="<?= h($uploadDirUrl . $row['photo']) ?>" width="120" height="80" style="object-fit:cover" alt="Blog">
            <?php else: ?>
              <span class="text-muted">No Image</span>
            <?php endif; ?>
          </td>
          <td><?= h($row['title']) ?></td>
          <td><?= h($row['created_at']) ?></td>
          <td>
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= (int)$row['id'] ?>">Edit</button>
            <a class="btn btn-sm btn-danger"
               href="idx.php?page=blog&delete=<?= (int)$row['id'] ?>"
               onclick="return confirm('Delete this blog?')">Delete</a>
          </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?= (int)$row['id'] ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form method="post" enctype="multipart/form-data">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Blog</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                  <input type="hidden" name="old_photo" value="<?= h($row['photo']) ?>">

                  <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="<?= h($row['title']) ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control" rows="5" required><?= h($row['content']) ?></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Photo</label><br>
                    <?php if (!empty($row['photo'])): ?>
                      <img src="<?= h($uploadDirUrl . $row['photo']) ?>" width="120" height="80" class="mb-2" style="object-fit:cover" alt="Blog"><br>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="update_blog" class="btn btn-success">Update</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
