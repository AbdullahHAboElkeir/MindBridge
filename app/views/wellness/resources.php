<?php
$baseUrl  = BASE_URL;
$category = $category ?? '';
$type     = $type ?? '';
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';

$categories = ['anxiety','depression','stress','sleep','mindfulness','relationships','grief','trauma','general'];
$types      = ['article','video','audio','exercise','worksheet'];
$typeIcons  = ['article'=>'file-text','video'=>'play-circle','audio'=>'music-note','exercise'=>'activity','worksheet'=>'file-earmark-check'];
$catColors  = ['anxiety'=>'primary','depression'=>'accent','stress'=>'warning','sleep'=>'secondary','mindfulness'=>'success','relationships'=>'info','grief'=>'muted','trauma'=>'danger','general'=>'primary'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-book-heart me-2"></i>Wellness Resources</h1>
    <p>Evidence-based articles, exercises, and tools for your wellbeing</p>
  </div>

  <!-- Featured -->
  <?php if (!empty($featured)): ?>
  <div class="row g-4 mb-4">
    <?php foreach ($featured as $res): ?>
      <div class="col-md-4">
        <a href="<?= $baseUrl ?>/wellness/resource/<?= $res['id'] ?>" class="text-decoration-none">
          <div class="feature-card">
            <div class="feature-icon mx-auto"
                 style="background:var(--primary-light);">
              <i class="bi bi-<?= $typeIcons[$res['type']] ?? 'file-text' ?>" style="color:var(--primary);font-size:1.5rem;"></i>
            </div>
            <h6 class="fw-700"><?= htmlspecialchars($res['title']) ?></h6>
            <p class="text-muted small"><?= htmlspecialchars(substr($res['description'] ?? '', 0, 100)) ?></p>
            <span class="forum-category-badge"><?= ucfirst($res['type']) ?></span>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Filters -->
  <div class="card mb-4 fade-in-up">
    <div class="card-body py-3">
      <form method="GET" action="<?= $baseUrl ?>/wellness/resources" class="d-flex flex-wrap gap-3 align-items-end">
        <div>
          <label class="form-label small fw-600 mb-1">Category</label>
          <select name="category" class="form-select form-select-sm">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label small fw-600 mb-1">Type</label>
          <select name="type" class="form-select form-select-sm">
            <option value="">All Types</option>
            <?php foreach ($types as $t): ?>
              <option value="<?= $t ?>" <?= $type === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="<?= $baseUrl ?>/wellness/resources" class="btn btn-outline-primary btn-sm">Reset</a>
      </form>
    </div>
  </div>

  <!-- Resources Grid -->
  <div class="row g-4">
    <?php if (empty($resources)): ?>
      <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-search fs-1 d-block mb-3 opacity-40"></i>
        No resources found for the selected filters.
      </div>
    <?php else: ?>
      <?php foreach ($resources as $res): ?>
        <div class="col-md-6 col-xl-4">
          <div class="card h-100 fade-in-up" style="border:1px solid var(--border);">
            <div class="card-body">
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="avatar" style="width:42px;height:42px;background:var(--primary-light);">
                  <i class="bi bi-<?= $typeIcons[$res['type']] ?? 'file-text' ?>"
                     style="color:var(--primary);font-size:1.1rem;"></i>
                </div>
                <div>
                  <span class="forum-category-badge"><?= ucfirst($res['type']) ?></span>
                  &nbsp;
                  <span class="forum-category-badge" style="background:var(--bg);color:var(--text-muted);">
                    <?= ucfirst($res['category']) ?>
                  </span>
                </div>
              </div>
              <h6 class="fw-700 mb-2"><?= htmlspecialchars($res['title']) ?></h6>
              <p class="text-muted small mb-3"><?= htmlspecialchars(substr($res['description'] ?? '', 0, 110)) ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="bi bi-eye me-1"></i><?= $res['view_count'] ?> views</small>
                <a href="<?= $baseUrl ?>/wellness/resource/<?= $res['id'] ?>" class="btn btn-sm btn-primary">
                  Read More <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
