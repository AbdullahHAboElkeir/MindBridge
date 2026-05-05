<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-shield-check me-2"></i>Content Moderation</h1>
    <p>Review and approve pending forum posts</p>
  </div>

  <?php if (empty($pending)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-3"></i>
      <h5>No pending posts</h5>
      <p class="text-muted">All forum content is up to date.</p>
    </div>
  <?php else: ?>
    <?php foreach ($pending as $post): ?>
      <div class="card mb-3 fade-in-up">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="flex-grow-1">
              <h6 class="fw-700 mb-1"><?= htmlspecialchars($post['title']) ?></h6>
              <div class="text-muted small mb-2">
                By <?= htmlspecialchars($post['first_name'].' '.$post['last_name']) ?>
                · <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
              </div>
              <p class="text-muted mb-0" style="font-size:.9rem;">
                <?= htmlspecialchars(substr($post['content'],0,300)) ?>…
              </p>
            </div>
            <form method="POST" action="<?= $baseUrl ?>/admin/moderatePost" class="d-flex gap-2 flex-shrink-0">
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
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
