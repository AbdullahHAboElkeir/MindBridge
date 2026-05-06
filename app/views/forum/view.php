<?php
$baseUrl    = BASE_URL;
$comments   = $comments ?? [];
$currentUid = Session::userId();
$isAdmin    = Session::role() === 'admin';
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="mb-3">
    <a href="<?= $baseUrl ?>/forum" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-arrow-left me-1"></i>Back to Forum
    </a>
  </div>

  <!-- Post Card -->
  <div class="card fade-in-up mb-4">
    <div class="card-body">
      <div class="d-flex gap-3 mb-4">
        <div class="avatar avatar-lg flex-shrink-0">
          <?= $post['is_anonymous'] ? '?' : strtoupper(substr($post['first_name'], 0, 1)) ?>
        </div>
        <div class="flex-grow-1">
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

      <!-- Post Actions -->
      <div class="d-flex gap-3 mt-4 pt-3 border-top flex-wrap align-items-center">
        <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= $post['view_count'] ?> views</span>
        <span class="text-muted small"><i class="bi bi-chat me-1"></i><?= count($comments) ?> comments</span>

        <div class="ms-auto d-flex gap-2">
          <!-- Edit — visible to post owner or admin -->
          <?php if ($isAdmin || (int)$post['user_id'] === $currentUid): ?>
            <a href="<?= $baseUrl ?>/forum/edit/<?= $post['id'] ?>" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <form method="POST" action="<?= $baseUrl ?>/forum/delete/<?= $post['id'] ?>"
                  onsubmit="return confirm('Delete this post? This cannot be undone.');">
              <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Delete
              </button>
            </form>
          <?php endif; ?>

          <!-- Report — only for non-owners -->
          <?php if ((int)$post['user_id'] !== $currentUid): ?>
            <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#reportModal">
              <i class="bi bi-flag me-1"></i>Report
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Comments -->
  <h5 class="fw-700 mb-3"><?= count($comments) ?> Comment<?= count($comments) !== 1 ? 's' : '' ?></h5>

  <?php foreach ($comments as $c): ?>
    <div class="card mb-3 fade-in-up" id="comment-<?= $c['id'] ?>">
      <div class="card-body py-3">
        <div class="d-flex gap-3">
          <div class="avatar flex-shrink-0" style="width:36px;height:36px;font-size:.8rem;">
            <?= $c['is_anonymous'] ? '?' : strtoupper(substr($c['first_name'], 0, 1)) ?>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div class="fw-600 small">
                <?= $c['is_anonymous'] ? ($c['pseudonym'] ?: 'Anonymous') : htmlspecialchars($c['first_name'].' '.$c['last_name']) ?>
                <?php if (!$c['is_anonymous']): ?>
                  <span class="badge ms-1" style="background:var(--primary-light);color:var(--primary);font-size:.65rem;">
                    <?= ucfirst($c['role']) ?>
                  </span>
                <?php endif; ?>
                <span class="fw-400 text-muted ms-2"><?= date('M j, Y', strtotime($c['created_at'])) ?></span>
              </div>
              <!-- Delete comment — owner or admin -->
              <?php if ($isAdmin || (int)$c['user_id'] === $currentUid): ?>
                <form method="POST" action="<?= $baseUrl ?>/forum/deleteComment/<?= $c['id'] ?>"
                      onsubmit="return confirm('Remove this comment?');" class="ms-2">
                  <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete comment">
                    <i class="bi bi-trash small"></i>
                  </button>
                </form>
              <?php endif; ?>
            </div>
            <p class="mb-0 mt-1" style="font-size:.9rem;"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (empty($comments)): ?>
    <div class="text-center text-muted py-4">
      <i class="bi bi-chat-square d-block fs-2 mb-2 opacity-40"></i>
      No comments yet. Be the first to respond!
    </div>
  <?php endif; ?>

  <!-- Add Comment -->
  <div class="card fade-in-up mt-4">
    <div class="card-header"><i class="bi bi-chat-square-text text-primary me-2"></i>Add a Comment</div>
    <div class="card-body">
      <form method="POST" action="<?= $baseUrl ?>/forum/comment/add" class="needs-validation" novalidate>
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
                 placeholder="Optional pseudonym (e.g. HopefulHeart)…">
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-send me-2"></i>Post Comment
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Report Post Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:var(--radius);">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-700"><i class="bi bi-flag-fill text-danger me-2"></i>Report Post</h5>
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
              <option value="harassment">Harassment or bullying</option>
              <option value="spam">Spam</option>
              <option value="misinformation">Misinformation</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Additional details <span class="text-muted fw-400">(optional)</span></label>
            <textarea name="details" class="form-control" rows="2"
                      placeholder="Please describe what you're reporting…"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger"><i class="bi bi-flag me-1"></i>Submit Report</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('anonComment')?.addEventListener('change', function () {
  document.getElementById('pseudoField').style.display = this.checked ? 'block' : 'none';
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
