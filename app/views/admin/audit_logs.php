<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-clock-history me-2"></i>Audit Logs</h1>
    <p>Complete system action history</p>
  </div>

  <div class="card fade-in-up">
    <div class="card-body p-0">
      <table class="table-mindbridge table mb-0">
        <thead>
          <tr><th>User</th><th>Action</th><th>Table</th><th>Details</th><th>IP</th><th>Time</th></tr>
        </thead>
        <tbody>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td class="fw-600 small"><?= htmlspecialchars($log['first_name'].' '.$log['last_name']) ?></td>
              <td><code style="font-size:.75rem;"><?= htmlspecialchars($log['action']) ?></code></td>
              <td class="text-muted small"><?= htmlspecialchars($log['entity'] ?? '') ?></td>
              <td class="text-muted small" style="max-width:220px;">
                <?= htmlspecialchars(substr($log['details']??'',0,80)) ?>
              </td>
              <td class="text-muted small"><?= htmlspecialchars($log['ip_address']??'') ?></td>
              <td class="text-muted small"><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($pages > 1): ?>
    <div class="d-flex justify-content-center gap-1 mt-4">
      <?php for ($p=1;$p<=$pages;$p++): ?>
        <a href="?page=<?= $p ?>"
           class="btn btn-sm <?= $p===$page?'btn-primary':'btn-outline-primary' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
