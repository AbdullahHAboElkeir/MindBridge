<?php
$baseUrl    = BASE_URL;
$categories = $categories ?? ['general','anxiety','depression','stress','relationships','mindfulness','trauma','grief'];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="mb-3">
    <a href="<?= $baseUrl ?>/forum/show/<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-arrow-left me-1"></i>Back to Post
    </a>
  </div>

  <div class="page-header fade-in-up">
    <h1><i class="bi bi-pencil-square me-2"></i>Edit Post</h1>
    <p>Update your post content below.</p>
  </div>

  <div class="card fade-in-up">
    <div class="card-header"><i class="bi bi-pencil text-primary me-2"></i>Edit: <?= htmlspecialchars($post['title']) ?></div>
    <div class="card-body">
      <form method="POST" action="<?= $baseUrl ?>/forum/update/<?= $post['id'] ?>" class="needs-validation" novalidate>

        <div class="mb-4">
          <label class="form-label fw-600" for="editTitle">Post Title <span class="text-danger">*</span></label>
          <input type="text" id="editTitle" name="title" class="form-control form-control-lg"
                 value="<?= htmlspecialchars($post['title']) ?>"
                 placeholder="A clear, descriptive title…" required>
        </div>

        <div class="mb-4">
          <label class="form-label fw-600" for="editCategory">Category</label>
          <select id="editCategory" name="category" class="form-select">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= $post['category'] === $cat ? 'selected' : '' ?>>
                <?= ucfirst($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-4">
          <label class="form-label fw-600" for="editContent">Content <span class="text-danger">*</span></label>
          <textarea id="editContent" name="content" class="form-control" rows="10" required
                    placeholder="Share your thoughts, experiences, or questions…"><?= htmlspecialchars($post['content']) ?></textarea>
          <div class="form-text">
            <i class="bi bi-shield-check text-success me-1"></i>
            This is a safe, moderated space. Please be kind and supportive.
          </div>
        </div>

        <div class="d-flex gap-3">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-circle me-2"></i>Save Changes
          </button>
          <a href="<?= $baseUrl ?>/forum/show/<?= $post['id'] ?>" class="btn btn-outline-secondary">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
