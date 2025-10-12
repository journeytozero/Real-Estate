<?php
// admin/pages/concerns.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../partials/db.php'; // $conn (PDO)

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$uploadDirFs  = __DIR__ . '/../uploads/concerns/'; // FS path
$uploadDirUrl = 'uploads/concerns/';               // URL from /admin/
$maxSize      = 2 * 1024 * 1024;                   // 2MB
$allowedExts  = ['jpg','jpeg','png','webp','gif','svg'];
$allowedMime  = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'];

if (!is_dir($uploadDirFs)) @mkdir($uploadDirFs, 0775, true);

function saveUpload(array $file, string $dirFs, array $allowedExts, array $allowedMime, int $maxSize): ?string {
  if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
  if ($file['error'] !== UPLOAD_ERR_OK) return null;
  if ($file['size'] > $maxSize) return null;
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowedExts, true)) return null;

  // SVG sniff differently (skip finfo for svg to avoid false-negatives on some hosts)
  if ($ext !== 'svg') {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowedMime, true)) return null;
  }

  $name = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
  if (!move_uploaded_file($file['tmp_name'], $dirFs . $name)) return null;
  return $name;
}
function deleteIfExists(?string $filename, string $dirFs): void {
  if ($filename && is_file($dirFs . $filename)) @unlink($dirFs . $filename);
}

$msg = null;

/* CREATE */
if (isset($_POST['add_concern'])) {
  $name        = trim($_POST['name'] ?? '');
  $url         = trim($_POST['url'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $sort_order  = (int)($_POST['sort_order'] ?? 0);
  $target_blank= isset($_POST['target_blank']) ? 1 : 0;
  $is_active   = isset($_POST['is_active']) ? 1 : 0;

  if ($name !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
    $logo = saveUpload($_FILES['logo'] ?? [], $uploadDirFs, $allowedExts, $allowedMime, $maxSize);
    $stmt = $conn->prepare("
      INSERT INTO concerns (name, url, logo, description, sort_order, target_blank, is_active)
      VALUES (:name,:url,:logo,:description,:sort_order,:target_blank,:is_active)
    ");
    $stmt->execute([
      ':name'=>$name, ':url'=>$url, ':logo'=>$logo,
      ':description'=>$description ?: null, ':sort_order'=>$sort_order,
      ':target_blank'=>$target_blank, ':is_active'=>$is_active
    ]);
    $msg = 'added';
  } else { $msg = 'invalid'; }
}

/* UPDATE */
if (isset($_POST['update_concern'])) {
  $id          = (int)($_POST['id'] ?? 0);
  $name        = trim($_POST['name'] ?? '');
  $url         = trim($_POST['url'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $sort_order  = (int)($_POST['sort_order'] ?? 0);
  $target_blank= isset($_POST['target_blank']) ? 1 : 0;
  $is_active   = isset($_POST['is_active']) ? 1 : 0;
  $old_logo    = trim($_POST['old_logo'] ?? '');

  if ($id > 0 && $name !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
    $newLogo = saveUpload($_FILES['logo'] ?? [], $uploadDirFs, $allowedExts, $allowedMime, $maxSize);
    if ($newLogo) { deleteIfExists($old_logo, $uploadDirFs); $logoToSave = $newLogo; }
    else { $logoToSave = $old_logo ?: null; }

    $stmt = $conn->prepare("
      UPDATE concerns
         SET name=:name, url=:url, logo=:logo, description=:description,
             sort_order=:sort_order, target_blank=:target_blank, is_active=:is_active
       WHERE id=:id
    ");
    $stmt->execute([
      ':name'=>$name, ':url'=>$url, ':logo'=>$logoToSave, ':description'=>$description ?: null,
      ':sort_order'=>$sort_order, ':target_blank'=>$target_blank, ':is_active'=>$is_active, ':id'=>$id
    ]);
    $msg = 'updated';
  } else { $msg = 'invalid'; }
}

/* DELETE */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id > 0) {
    $q = $conn->prepare("SELECT logo FROM concerns WHERE id=:id");
    $q->execute([':id'=>$id]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if ($row) deleteIfExists($row['logo'] ?? null, $uploadDirFs);

    $conn->prepare("DELETE FROM concerns WHERE id=:id")->execute([':id'=>$id]);
    $msg = 'deleted';
  } else { $msg = 'invalid'; }
}

/* LIST */
$stmt = $conn->query("
  SELECT id, name, url, logo, description, sort_order, target_blank, is_active, created_at
  FROM concerns
  ORDER BY sort_order ASC, name ASC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Concerns (External Links)</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4">Concerns (External Links)</h1>

  <?php if ($msg): ?>
    <div class="alert alert-success py-2 mb-4"><?= h(ucfirst($msg)) ?> successfully!</div>
  <?php endif; ?>

  <!-- Add -->
  <form method="post" enctype="multipart/form-data" class="border p-4 rounded bg-light mb-4">
    <h5 class="mb-3">Add Concern</h5>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required>
      </div>
      <div class="col-md-5">
        <label class="form-label">URL (https://...)</label>
        <input name="url" type="url" class="form-control" placeholder="https://example.com" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="0">
      </div>
      <div class="col-md-6">
        <label class="form-label">Logo (max 2MB)</label>
        <input name="logo" type="file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check me-3">
          <input class="form-check-input" type="checkbox" id="tb_add" name="target_blank" checked>
          <label class="form-check-label" for="tb_add">Open in new tab</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="active_add" name="is_active" checked>
          <label class="form-check-label" for="active_add">Active</label>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="2" class="form-control" placeholder="Optional"></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-primary" type="submit" name="add_concern">Add Concern</button>
      </div>
    </div>
  </form>

  <!-- List -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th style="width:70px">#</th>
          <th style="width:110px">Logo</th>
          <th>Name & URL</th>
          <th>Description</th>
          <th style="width:110px">Sort</th>
          <th style="width:140px">Flags</th>
          <th style="width:170px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$items): ?>
          <tr><td colspan="7" class="text-center text-muted">No concerns yet.</td></tr>
        <?php else: foreach ($items as $it): ?>
          <tr>
            <td><?= (int)$it['id'] ?></td>
            <td>
              <?php if (!empty($it['logo'])): ?>
                <img src="<?= h($uploadDirUrl . $it['logo']) ?>" alt="<?= h($it['name']) ?>" width="90" height="50" style="object-fit:contain;background:#fff;border:1px solid #eee">
              <?php else: ?>
                <span class="text-muted">No logo</span>
              <?php endif; ?>
            </td>
            <td>
              <strong><?= h($it['name']) ?></strong><br>
              <a href="<?= h($it['url']) ?>" target="_blank" rel="noopener"><?= h($it['url']) ?></a>
            </td>
            <td><?= $it['description'] ? h($it['description']) : '—' ?></td>
            <td><?= (int)$it['sort_order'] ?></td>
            <td>
              <?= $it['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
              <?= $it['target_blank'] ? '<span class="badge bg-info ms-1">New Tab</span>' : '' ?>
            </td>
            <td>
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit<?= (int)$it['id'] ?>">Edit</button>
              <a class="btn btn-sm btn-danger" href="idx.php?page=concerns&delete=<?= (int)$it['id'] ?>" onclick="return confirm('Delete this concern?')">Delete</a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="edit<?= (int)$it['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Concern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                    <input type="hidden" name="old_logo" value="<?= h($it['logo']) ?>">

                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="<?= h($it['name']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">URL (https://...)</label>
                        <input name="url" type="url" class="form-control" value="<?= h($it['url']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Logo (replace)</label>
                        <input name="logo" type="file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
                        <?php if (!empty($it['logo'])): ?>
                          <small class="text-muted">Current: <?= h($it['logo']) ?></small>
                        <?php endif; ?>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label">Sort Order</label>
                        <input name="sort_order" type="number" class="form-control" value="<?= (int)$it['sort_order'] ?>">
                      </div>
                      <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check me-3">
                          <input class="form-check-input" type="checkbox" id="tb_<?= (int)$it['id'] ?>" name="target_blank" <?= $it['target_blank']?'checked':'' ?>>
                          <label class="form-check-label" for="tb_<?= (int)$it['id'] ?>">New Tab</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="act_<?= (int)$it['id'] ?>" name="is_active" <?= $it['is_active']?'checked':'' ?>>
                          <label class="form-check-label" for="act_<?= (int)$it['id'] ?>">Active</label>
                        </div>
                      </div>
                      <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" class="form-control"><?= h($it['description']) ?></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update_concern" class="btn btn-success">Update</button>
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
