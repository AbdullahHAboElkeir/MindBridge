<?php
$baseUrl  = BASE_URL;
$resource = $resource ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="mb-3">
    <a href="<?= $baseUrl ?>/wellness/resources" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-arrow-left me-1"></i>Back to Resources
    </a>
  </div>

  <div class="card fade-in-up">
    <div class="card-body">
      <!-- Category badge & type -->
      <div class="d-flex gap-2 mb-3 flex-wrap">
        <span class="badge" style="background:var(--primary-light);color:var(--primary);">
          <?= ucfirst($resource['category'] ?? 'general') ?>
        </span>
        <span class="badge bg-secondary">
          <?= ucfirst($resource['type'] ?? 'article') ?>
        </span>
        <?php if (!empty($resource['is_featured'])): ?>
          <span class="badge" style="background:#fff3cd;color:#856404;">
            <i class="bi bi-star-fill me-1"></i>Featured
          </span>
        <?php endif; ?>
      </div>

      <h1 class="fw-700 mb-3" style="font-size:1.8rem;">
        <?= htmlspecialchars($resource['title']) ?>
      </h1>

      <?php if (!empty($resource['description'])): ?>
        <p class="text-muted lead mb-4"><?= htmlspecialchars($resource['description']) ?></p>
      <?php endif; ?>

      <hr class="my-4">

      <?php if (!empty($resource['content'])): ?>
        <div class="resource-content" style="line-height:1.9;font-size:.97rem;">
          <?= nl2br(htmlspecialchars($resource['content'])) ?>
        </div>
      <?php elseif (!empty($resource['url'])): ?>
        <div class="text-center py-4">
          <p class="text-muted mb-4">This resource is available at an external link.</p>
          <a href="<?= htmlspecialchars($resource['url']) ?>" target="_blank" rel="noopener noreferrer"
             class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-up-right me-2"></i>Open Resource
          </a>
        </div>
      <?php else: ?>
        <p class="text-muted">No content available for this resource.</p>
      <?php endif; ?>

      <!-- View count -->
      <div class="mt-4 pt-3 border-top text-muted small">
        <i class="bi bi-eye me-1"></i><?= number_format($resource['view_count'] ?? 0) ?> views
        <?php if (!empty($resource['created_at'])): ?>
          &nbsp;·&nbsp; Added <?= date('M j, Y', strtotime($resource['created_at'])) ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
