<?php
require_once BASE_PATH . '/app/views/layouts/header.php';
$pageTitle = '403 — Access Denied';
?>
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem;">
  <div>
    <div style="font-size:6rem;font-weight:800;color:#e74c3c;line-height:1;">403</div>
    <h2 class="fw-700 mb-3">Access Denied</h2>
    <p class="text-muted mb-4">You don't have permission to view this page.</p>
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary me-2">
      <i class="bi bi-house me-2"></i>Go Home
    </a>
    <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-danger">
      <i class="bi bi-box-arrow-right me-2"></i>Switch Account
    </a>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
