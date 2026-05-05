<?php
$baseUrl  = BASE_URL;
$comments = $comments ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="mb-3">
    <a href="<?= $baseUrl ?>/forum" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-arrow-left me-1"></i>Back to Forum
    </a>
  </div>

  <!-- Post -->
  <div class="card fade-in-up mb-4">
    <div class="card-body">
      <div class="d-flex gap-3 mb-4">
        <div class="avatar avatar-lg flex-shrink-0">
          <?= $post['is_anonymous'] ? '?' : strtoupper(substr($post['first_name'],0,1)) ?>
        </div>
        <div>
          <h2 class="fw-700 mb-1" style="font-size:1.5rem;"><?= htmlspecialchars($post['title']) ?></h2>
          <div class="text-muted small">
            By <strong><?= $post['is_anonymous'] ? ($post['pseudonym'] ?: 'Anonymous') : htmlspecialchars($post['first_name'].' '.$post['last_name']) ?></strong>
            &nbsp;·&nbsp;<?= date('M j, Y \a\t g:i A', strtotime($post['created_at'])) ?>
            &nbsp;·&nbsp;<span class="forum-category-badge"><?= ucfirst($post['category']) ?></span>
          </div>
        </div>
      </div>

      <div style="line-height:1.9;font-size:.95rem;color:var(--text);">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
      </div>

      <div class="d-flex gap-3 mt-4 pt-3 border-top">
        <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= $post['view_count'] ?> views</span>
        <span class="text-muted small"><i class="bi bi-chat me-1"></i><?= count($comments) ?> comments</span>
        <!-- Report -->
        <button type="button" class="btn btn-sm btn-outline-danger ms-auto"
                data-bs-toggle="modal" data-bs-target="#reportModal">
          <i class="bi bi-flag me-1"></i>Report
        </button>
      </div>
    </div>
  </div>

  <!-- Comments -->
  <h5 class="fw-700 mb-3"><?= count($comments) ?> Comments</h5>

  <?php foreach ($comments as $c): ?>
    <div class="card mb-3 fade-in-up">
      <div class="card-body py-3">
        <div class="d-flex gap-3">
          <div class="avatar flex-shrink-0" style="width:36px;height:36px;font-size:.8rem;">
            <?= $c['is_anonymous'] ? '?' : strtoupper(substr($c['first_name'],0,1)) ?>
          </div>
          <div>
            <div class="fw-600 small">
              <?= $c['is_anonymous'] ? ($c['pseudonym'] ?: 'Anonymous') : htmlspecialchars($c['first_name'].' '.$c['last_name']) ?>
              <?php if (!$c['is_anonymous']): ?>
                <span class="badge ms-1" style="background:var(--primary-light);color:var(--primary);font-size:.65rem;">
                  <?= ucfirst($c['role']) ?>
                </span>
              <?php endif; ?>
              <span class="fw-400 text-muted ms-2"><?= date('M j, Y', strtotime($c['created_at'])) ?></span>
            </div>
            <p class="mb-0 mt-1" style="font-size:.9rem;"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Add Comment -->
  <div class="card fade-in-up mt-4">
    <div class="card-header"><i class="bi bi-chat-square-text text-primary me-2"></i>Add a Comment</div>
    <div class="card-body">
      <form method="POST" action="<?= $baseUrl ?>/forum/comment" class="needs-validation" novalidate>
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <div class="mb-3">
          <textarea name="content" class="form-control" rows="4" required
                    placeholder="Share a supportive response…" data-crisis-check></textarea>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonComment" value="1">
          <label class="form-check-label" for="anonComment">Comment anonymously</label>
        </div>
        <div id="pseudoField" style="display:none;" class="mb-3">
          <input type="text" name="pseudonym" class="form-control form-control-sm"
                 placeholder="Your pseudonym…">
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-send me-2"></i>Post Comment
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:var(--radius);">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-700">Report Post</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= $baseUrl ?>/forum/report">
        <input type="hidden" name="type" value="forum_post">
        <input type="hidden" name="target_id" value="<?= $post['id'] ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-600">Reason</label>
            <select name="reason" class="form-select">
              <option value="inappropriate">Inappropriate content</option>
              <option value="harassment">Harassment</option>
              <option value="spam">Spam</option>
              <option value="misinformation">Misinformation</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Details (optional)</label>
            <textarea name="details" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Submit Report</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('anonComment').addEventListener('change', function() {
  document.getElementById('pseudoField').style.display = this.checked ? 'block' : 'none';
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
