<?php
$baseUrl    = BASE_URL;
$category   = $category ?? '';
$categories = $categories ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-people-fill me-2"></i>Community Forum</h1>
    <p>A safe, supportive space to share and connect with others on their wellness journey</p>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <!-- Category filters -->
    <div class="d-flex flex-wrap gap-2">
      <a href="<?= $baseUrl ?>/forum" class="btn btn-sm <?= $category==='' ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="?category=<?= $cat ?>" class="btn btn-sm <?= $category===$cat ? 'btn-primary' : 'btn-outline-primary' ?>">
          <?= ucfirst($cat) ?>
        </a>
      <?php endforeach; ?>
    </div>
    <a href="<?= $baseUrl ?>/forum/create" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>New Post
    </a>
  </div>

  <?php if (empty($posts)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-chat-square-text fs-1 text-muted d-block mb-3 opacity-40"></i>
      <h5>No posts yet</h5>
      <p class="text-muted">Be the first to start a conversation!</p>
      <a href="<?= $baseUrl ?>/forum/create" class="btn btn-primary">Create Post</a>
    </div>
  <?php else: ?>
    <?php foreach ($posts as $post): ?>
      <div class="forum-post-card fade-in-up <?= $post['is_pinned'] ? 'border-primary border' : '' ?>">
        <div class="d-flex gap-3">
          <div class="flex-shrink-0">
            <div class="avatar">
              <?= $post['is_anonymous'] ? '?' : strtoupper(substr($post['first_name'],0,1)) ?>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
              <div>
                <?php if ($post['is_pinned']): ?>
                  <span class="badge bg-warning text-dark me-2" style="font-size:.72rem;">
                    <i class="bi bi-pin-fill me-1"></i>Pinned
                  </span>
                <?php endif; ?>
                <a href="<?= $baseUrl ?>/forum/view/<?= $post['id'] ?>" class="fw-700 text-decoration-none" style="color:var(--text);font-size:1.05rem;">
                  <?= htmlspecialchars($post['title']) ?>
                </a>
              </div>
              <span class="forum-category-badge"><?= ucfirst($post['category']) ?></span>
            </div>

            <div class="text-muted small mb-2">
              By
              <strong><?= $post['is_anonymous'] ? ($post['pseudonym'] ?: 'Anonymous') : htmlspecialchars($post['first_name'].' '.$post['last_name']) ?></strong>
              <?php if (!$post['is_anonymous']): ?>
                <span class="badge ms-1" style="background:var(--primary-light);color:var(--primary);font-size:.65rem;">
                  <?= ucfirst($post['role']) ?>
                </span>
              <?php endif; ?>
              &nbsp;·&nbsp;<?= date('M j, Y', strtotime($post['created_at'])) ?>
            </div>

            <p class="text-muted mb-3" style="font-size:.9rem;">
              <?= htmlspecialchars(substr($post['content'], 0, 200)) ?><?= strlen($post['content']) > 200 ? '…' : '' ?>
            </p>

            <div class="d-flex align-items-center gap-4">
              <a href="<?= $baseUrl ?>/forum/view/<?= $post['id'] ?>" class="text-muted small text-decoration-none">
                <i class="bi bi-chat me-1"></i><?= $post['comment_count'] ?> comments
              </a>
              <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= $post['view_count'] ?> views</span>
              <a href="<?= $baseUrl ?>/forum/view/<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary ms-auto">
                Read More <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
      <div class="d-flex justify-content-center gap-1 mt-4">
        <?php for ($p=1; $p<=$pages; $p++): ?>
          <a href="?<?= $category ? "category=$category&" : '' ?>page=<?= $p ?>"
             class="btn btn-sm <?= $p===$page ? 'btn-primary' : 'btn-outline-primary' ?>"><?= $p ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
