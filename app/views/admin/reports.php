<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-flag me-2"></i>Content Reports</h1>
    <p>Review and resolve reported content from community members</p>
  </div>

  <div class="d-flex gap-2 mb-4">
    <?php foreach (['pending','resolved','dismissed'] as $s): ?>
      <a href="?status=<?= $s ?>"
         class="btn btn-sm <?= $status===$s ? 'btn-primary':'btn-outline-primary' ?>">
        <?= ucfirst($s) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="card fade-in-up">
    <div class="card-body p-0">
      <?php if (empty($reports)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-check-circle fs-1 d-block mb-3 opacity-40"></i>
          No <?= $status ?> reports.
        </div>
      <?php else: ?>
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr><th>Reporter</th><th>Type</th><th>Reason</th><th>Details</th><th>Reported</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($reports as $r): ?>
              <tr>
                <td class="fw-600 small"><?= htmlspecialchars($r['reporter_first'].' '.$r['reporter_last']) ?></td>
                <td><span class="forum-category-badge"><?= ucfirst(str_replace('_',' ',$r['type'])) ?></span></td>
                <td><?= ucfirst(str_replace('_',' ',$r['reason'])) ?></td>
                <td class="text-muted small" style="max-width:180px;">
                  <?= htmlspecialchars(substr($r['details']??'',0,80)) ?>
                </td>
                <td class="text-muted small"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                <td>
                  <?php if ($r['status'] === 'pending'): ?>
                    <form method="POST" action="<?= $baseUrl ?>/admin/resolveReport" class="d-flex gap-1">
                      <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                      <button name="action" value="action_taken" type="submit" class="btn btn-sm btn-danger">
                        Remove Content
                      </button>
                      <button name="action" value="dismiss" type="submit" class="btn btn-sm btn-outline-secondary">
                        Dismiss
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small"><?= ucfirst($r['status']) ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
