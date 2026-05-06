<?php
$baseUrl = BASE_URL;
$flagged = $flagged ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-shield-check me-2"></i>Content Moderation</h1>
    <p>Review pending and flagged forum posts</p>
  </div>

  <!-- Pending Posts -->
  <div class="card mb-4 fade-in-up">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-clock text-warning me-2"></i>Pending Approval <span class="badge bg-warning text-dark ms-2"><?= count($pending) ?></span></span>
    </div>
    <div class="card-body p-0">
      <?php if (empty($pending)): ?>
        <div class="text-center py-4 text-muted">
          <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
          No posts pending approval.
        </div>
      <?php else: ?>
        <?php foreach ($pending as $post): ?>
          <div class="border-bottom p-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
              <div class="flex-grow-1">
                <h6 class="fw-700 mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                <div class="text-muted small mb-2">
                  By <?= htmlspecialchars($post['first_name'].' '.$post['last_name']) ?>
                  · <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
                  · <span class="forum-category-badge"><?= ucfirst($post['category']) ?></span>
                </div>
                <p class="text-muted mb-0" style="font-size:.9rem;">
                  <?= htmlspecialchars(substr($post['content'],0,300)) ?>…
                </p>
              </div>
              <div class="d-flex gap-2 flex-shrink-0">
                <a href="<?= $baseUrl ?>/forum/show/<?= $post['id'] ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                  <i class="bi bi-eye me-1"></i>View
                </a>
                <form method="POST" action="<?= $baseUrl ?>/admin/moderatePost" class="d-flex gap-2">
                  <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                  <button name="action" value="approve" type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-check-circle me-1"></i>Approve
                  </button>
                  <button name="action" value="remove" type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>Remove
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Flagged/Reported Posts -->
  <?php if (!empty($flagged)): ?>
  <div class="card fade-in-up">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-flag text-danger me-2"></i>Flagged / Reported Posts <span class="badge bg-danger ms-2"><?= count($flagged) ?></span></span>
      <a href="<?= $baseUrl ?>/admin/reports" class="btn btn-sm btn-outline-danger">View Reports</a>
    </div>
    <div class="card-body p-0">
      <?php foreach ($flagged as $post): ?>
        <div class="border-bottom p-3">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="flex-grow-1">
              <h6 class="fw-700 mb-1">
                <?= htmlspecialchars($post['title']) ?>
                <?php if (!empty($post['report_count']) && $post['report_count'] > 0): ?>
                  <span class="badge bg-danger ms-2"><?= $post['report_count'] ?> report<?= $post['report_count'] > 1 ? 's' : '' ?></span>
                <?php endif; ?>
              </h6>
              <div class="text-muted small mb-2">
                By <?= htmlspecialchars($post['first_name'].' '.$post['last_name']) ?>
                · <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
                · Status: <span class="badge-status <?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span>
              </div>
              <p class="text-muted mb-0" style="font-size:.9rem;">
                <?= htmlspecialchars(substr($post['content'],0,250)) ?>…
              </p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
              <a href="<?= $baseUrl ?>/forum/show/<?= $post['id'] ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                <i class="bi bi-eye me-1"></i>View
              </a>
              <form method="POST" action="<?= $baseUrl ?>/admin/moderatePost" class="d-flex gap-2">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button name="action" value="approve" type="submit" class="btn btn-success btn-sm">
                  <i class="bi bi-check-circle me-1"></i>Restore
                </button>
                <button name="action" value="remove" type="submit" class="btn btn-danger btn-sm">
                  <i class="bi bi-trash me-1"></i>Remove
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
