<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
//$nav = __DIR__ . '/includes/navbar.php';
//if (is_file($nav)) require_once $nav;

/* ---------- Helpers ---------- */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function logo_url(?string $logo, string $uploadsUrl): string {
  if (!$logo) return 'assets/images/concerns/placeholder.png';
  if (preg_match('~^https?://~i', $logo)) return $logo;
  return $uploadsUrl . ltrim($logo, '/');
}

/* ---------- Inputs ---------- */
$uploadsUrl = 'admin/uploads/concerns/';
$q    = trim((string)($_GET['q'] ?? ''));
$sort = (string)($_GET['sort'] ?? 'order');           // order | name | new | old
$per  = (int)($_GET['per']  ?? 12);                   // 8 / 12 / 16 / 24
$page = max(1, (int)($_GET['page'] ?? 1));

$allowedPer = [8,12,16,24];
if (!in_array($per, $allowedPer, true)) $per = 12;
$sortSql = match ($sort) {
  'name' => 'ORDER BY name ASC',
  'new'  => 'ORDER BY created_at DESC',
  'old'  => 'ORDER BY created_at ASC',
  default=> 'ORDER BY sort_order ASC, name ASC'
};

/* ---------- Query ---------- */
$where = 'WHERE is_active = 1';
$params = [];
if ($q !== '') {
  $where .= ' AND (name LIKE :q OR description LIKE :q OR url LIKE :q)';
  $params[':q'] = "%{$q}%";
}

$countStmt = $conn->prepare("SELECT COUNT(*) FROM concerns $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$totalPages = max(1, (int)ceil($total / $per));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $per;

$sql = "
  SELECT id, name, url, logo, description, target_blank, created_at
  FROM concerns
  $where
  $sortSql
  LIMIT :limit OFFSET :offset
";
$listStmt = $conn->prepare($sql);
foreach ($params as $k=>$v) $listStmt->bindValue($k, $v, PDO::PARAM_STR);
$listStmt->bindValue(':limit',  $per,    PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$concerns = $listStmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- URL builders ---------- */
function build_url(array $merge): string {
  $params = array_merge($_GET, $merge);
  return '?' . http_build_query($params);
}
?>
<div class="container py-5">
  <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
    <h2 class="fw-bold m-0 flex-grow-1 gradient-text">Our Concern Group</h2>
    <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2 rounded-pill">
      <?= number_format($total) ?> result<?= $total===1?'':'s' ?>
    </span>
  </div>

  <!-- Controls -->
  <form class="row g-2 g-sm-3 align-items-center mb-4" method="get">
    <div class="col-12 col-sm-6 col-lg-5">
      <div class="input-group">
        <span class="input-group-text bg-body-tertiary border-0">
          <!-- search icon -->
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 21l-3.8-3.8M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        </span>
        <input name="q" value="<?= h($q) ?>" class="form-control border-0" placeholder="Search concerns…">
        <?php if ($q !== ''): ?>
          <a class="btn btn-outline-secondary" href="<?= h(build_url(['q'=>'','page'=>1])) ?>">Clear</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="order" <?= $sort==='order'?'selected':'' ?>>Recommended</option>
        <option value="name"  <?= $sort==='name'?'selected':''  ?>>Name A–Z</option>
        <option value="new"   <?= $sort==='new'?'selected':''   ?>>Newest</option>
        <option value="old"   <?= $sort==='old'?'selected':''   ?>>Oldest</option>
      </select>
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <select name="per" class="form-select" onchange="this.form.submit()">
        <?php foreach ([8,12,16,24] as $n): ?>
          <option value="<?= $n ?>" <?= $per===$n?'selected':'' ?>><?= $n ?>/page</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-lg-3 text-lg-end">
      <button class="btn btn-primary w-100 w-lg-auto" type="submit">Apply</button>
    </div>
  </form>

  <?php if (empty($concerns)): ?>
    <div class="alert alert-info">
      No concerns to show<?= $q !== '' ? ' for “'.h($q).'”' : '' ?>.
      <?php if ($q !== ''): ?>
        <a href="<?= h(build_url(['q'=>'','page'=>1])) ?>" class="alert-link">Reset search</a>.
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($concerns as $c): 
        $logo = logo_url($c['logo'] ?? null, $uploadsUrl);
        $isNew = isset($c['created_at']) && (strtotime($c['created_at']) > time() - 30*24*60*60);
      ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card concern-card h-100 border-0 shadow-sm reveal">
          <div class="thumb-wrapper">
            <img
              src="<?= h($logo) ?>"
              alt="<?= h($c['name']) ?>"
              class="thumb-img"
              loading="lazy"
              onerror="this.src='assets/images/concerns/placeholder.png'">
            <?php if ($isNew): ?>
              <span class="badge new-badge">NEW</span>
            <?php endif; ?>
          </div>
          <div class="card-body d-flex flex-column text-center p-3">
            <h5 class="card-title mb-1 title-link"><?= h($c['name']) ?></h5>
            <?php if (!empty($c['description'])): ?>
              <p class="card-text text-muted small flex-grow-1 mb-3"><?= h($c['description']) ?></p>
            <?php else: ?>
              <div class="flex-grow-1"></div>
            <?php endif; ?>
            <div class="d-grid gap-2">
              <a
                href="<?= h($c['url']) ?>"
                class="btn btn-primary rounded-pill"
                <?= !empty($c['target_blank']) ? 'target="_blank" rel="noopener"' : '' ?>>
                Visit Website
              </a>
              <button
                type="button"
                class="btn btn-outline-secondary rounded-pill copy-link"
                data-url="<?= h($c['url']) ?>">
                Copy Link
              </button>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav class="mt-4" aria-label="Concerns pagination">
        <ul class="pagination justify-content-center">
          <li class="page-item <?= $page<=1?'disabled':'' ?>">
            <a class="page-link" href="<?= h(build_url(['page'=>$page-1])) ?>">Previous</a>
          </li>
          <?php
            $start = max(1, $page-2);
            $end   = min($totalPages, $page+2);
            if ($start > 1) {
              echo '<li class="page-item"><a class="page-link" href="'.h(build_url(['page'=>1])).'">1</a></li>';
              if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
            for ($p = $start; $p <= $end; $p++):
          ?>
            <li class="page-item <?= $p===$page?'active':'' ?>">
              <a class="page-link" href="<?= h(build_url(['page'=>$p])) ?>"><?= $p ?></a>
            </li>
          <?php endfor;
            if ($end < $totalPages) {
              if ($end < $totalPages-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
              echo '<li class="page-item"><a class="page-link" href="'.h(build_url(['page'=>$totalPages])).'">'.$totalPages.'</a></li>';
            }
          ?>
          <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
            <a class="page-link" href="<?= h(build_url(['page'=>$page+1])) ?>">Next</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>

<style>
  :root{
    --brand:#0d6efd;
    --card-radius:1rem;
  }
  .gradient-text{
    background: linear-gradient(90deg, var(--brand), #00c6ff);
    -webkit-background-clip:text;background-clip:text;color:transparent;
  }
  .concern-card{
    border-radius: var(--card-radius);
    overflow: hidden;
    transition: transform .25s ease, box-shadow .25s ease;
    background: linear-gradient(180deg, #fff, #fafbff);
  }
  .concern-card:hover{ transform: translateY(-6px); box-shadow: 0 16px 32px rgba(13,110,253,.12); }
  .thumb-wrapper{
    position:relative;
    display:flex; align-items:center; justify-content:center;
    background: radial-gradient(120% 120% at 50% 0%, rgba(13,110,253,.08), transparent), #fff;
    height: 160px; border-bottom:1px solid rgba(13,110,253,.08);
  }
  .thumb-img{ max-width:88%; max-height:88%; object-fit:contain; transition: transform .35s ease; }
  .concern-card:hover .thumb-img{ transform: scale(1.03); }
  .new-badge{
    position:absolute; top:10px; left:10px;
    background:#22c55e; color:#fff; font-weight:600; font-size:.7rem;
    border-radius:999px; padding:.25rem .55rem; box-shadow:0 2px 6px rgba(34,197,94,.35);
  }
  .title-link{ color: var(--brand); }
  .title-link:hover{ text-decoration: underline; }
  /* simple reveal on scroll */
  .reveal{ opacity:0; transform: translateY(10px); }
  .reveal.revealed{ opacity:1; transform:none; transition: all .4s ease; }
</style>

<script>
// Copy link buttons
document.querySelectorAll('.copy-link').forEach(btn=>{
  btn.addEventListener('click', async ()=>{
    const url = btn.dataset.url || '';
    try {
      await navigator.clipboard.writeText(url);
      const old = btn.textContent;
      btn.textContent = 'Copied!';
      btn.classList.remove('btn-outline-secondary');
      btn.classList.add('btn-success');
      setTimeout(()=>{ btn.textContent = old; btn.classList.add('btn-outline-secondary'); btn.classList.remove('btn-success'); }, 1200);
    } catch(e){
      prompt('Copy this link:', url);
    }
  });
});

// Reveal on scroll
const obs = new IntersectionObserver((entries)=>{
  entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('revealed'); obs.unobserve(e.target); }});
},{ threshold: 0.12 });
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));
</script>

<?php //require_once __DIR__ . '/includes/footer.php'; ?>
