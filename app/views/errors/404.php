<?php
require_once BASE_PATH . '/app/views/layouts/header.php';
$pageTitle = '404 — Page Not Found';
?>
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem;">
  <div>
    <div style="font-size:6rem;font-weight:800;color:var(--primary);line-height:1;">404</div>
    <h2 class="fw-700 mb-3">Page Not Found</h2>
    <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.</p>
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary me-2">
      <i class="bi bi-house me-2"></i>Go Home
    </a>
    <a href="javascript:history.back()" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left me-2"></i>Go Back
    </a>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
