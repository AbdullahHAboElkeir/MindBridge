<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-heart-pulse me-2 text-danger"></i>Crisis Alerts</h1>
    <p>Monitor and respond to patient crisis events</p>
  </div>

  <!-- Status tabs -->
  <div class="d-flex gap-2 mb-4 flex-wrap">
    <?php foreach (['new','acknowledged','in_progress','resolved'] as $s): ?>
      <a href="?status=<?= $s ?>"
         class="btn btn-sm <?= $status === $s ? 'btn-danger' : 'btn-outline-secondary' ?>">
        <?= ucfirst(str_replace('_',' ',$s)) ?>
        <span class="badge ms-1" style="background:rgba(255,255,255,.3);">
          <?= $counts[$s] ?? 0 ?>
        </span>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="card fade-in-up">
    <div class="card-body p-0">
      <?php if (empty($alerts)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-3"></i>
          <h5>No <?= $status ?> alerts</h5>
        </div>
      <?php else: ?>
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr><th>Patient</th><th>Source</th><th>Severity</th><th>Trigger</th><th>Time</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($alerts as $a): ?>
              <tr>
                <td>
                  <div class="fw-600"><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></div>
                  <div class="text-muted small"><?= htmlspecialchars($a['email']) ?></div>
                </td>
                <td><span class="forum-category-badge"><?= ucfirst($a['source']) ?></span></td>
                <td>
                  <span class="badge-status <?= $a['severity']==='high'?'cancelled':($a['severity']==='medium'?'pending':'active') ?>">
                    <?= ucfirst($a['severity']) ?>
                  </span>
                </td>
                <td class="text-muted small" style="max-width:200px;">
                  <?= htmlspecialchars(substr($a['trigger_text']??'',0,80)) ?>…
                </td>
                <td class="text-muted small"><?= date('M j, Y g:i A', strtotime($a['created_at'])) ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <?php if ($a['status'] !== 'resolved'): ?>
                      <a href="<?= $baseUrl ?>/crisis/respond/<?= $a['id'] ?>" class="btn btn-sm btn-danger">
                        <i class="bi bi-arrow-right me-1"></i>Respond
                      </a>
                    <?php else: ?>
                      <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Resolved</span>
                    <?php endif; ?>
                  </div>
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
