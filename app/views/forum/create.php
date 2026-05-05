<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$categories = ['general','anxiety','depression','stress','relationships','mindfulness','trauma','grief'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-pencil-square me-2"></i>Create New Post</h1>
    <p>Share your thoughts, ask questions, or offer support to the community</p>
  </div>
  <div style="max-width:720px;margin:0 auto;">
    <div class="card fade-in-up">
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/forum/store" class="needs-validation" novalidate>
          <div class="mb-3">
            <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" required placeholder="What's on your mind?">
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Category</label>
            <select name="category" class="form-select">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Content <span class="text-danger">*</span></label>
            <textarea name="content" class="form-control" rows="7" required data-crisis-check
                      placeholder="Share your thoughts, experiences, or questions…"></textarea>
          </div>
          <div class="card mb-4" style="background:var(--bg);border:1px solid var(--border);">
            <div class="card-body py-3">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonCheck" value="1">
                <label class="form-check-label fw-600" for="anonCheck">
                  <i class="bi bi-incognito me-2"></i>Post Anonymously
                </label>
              </div>
              <div id="pseudonymField" style="display:none;" class="mt-2">
                <label class="form-label small fw-600">Display Name (pseudonym)</label>
                <input type="text" name="pseudonym" class="form-control form-control-sm"
                       placeholder="e.g. WorriedHeart, HopefulSoul">
                <div class="form-text">This name will be shown instead of your real name.</div>
              </div>
            </div>
          </div>
          <div class="alert alert-info mb-4">
            <i class="bi bi-shield-check me-2"></i>
            <strong>Community Guidelines:</strong> Be respectful, supportive, and kind. No medical advice.
            If you're in crisis, please call 988.
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-send me-2"></i>Publish Post
            </button>
            <a href="<?= $baseUrl ?>/forum" class="btn btn-outline-primary btn-lg">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('anonCheck').addEventListener('change', function() {
  document.getElementById('pseudonymField').style.display = this.checked ? 'block' : 'none';
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
