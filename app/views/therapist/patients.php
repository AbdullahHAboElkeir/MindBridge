<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-people me-2"></i>My Patients</h1>
    <p>Patients currently assigned to your care</p>
  </div>

  <?php if (empty($patients)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-people fs-1 text-muted d-block mb-3 opacity-40"></i>
      <h5>No patients assigned yet</h5>
      <p class="text-muted">Patients will appear here once they select you as their therapist.</p>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($patients as $p): ?>
        <div class="col-md-6 col-xl-4">
          <div class="card fade-in-up">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <div class="avatar"><?= strtoupper(substr($p['first_name'],0,1)) ?></div>
                <div>
                  <div class="fw-700"><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></div>
                  <div class="text-muted small"><?= htmlspecialchars($p['email']) ?></div>
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-6 text-center p-2 rounded" style="background:var(--bg);">
                  <div class="fw-700"><?= $p['total_sessions'] ?></div>
                  <div class="text-muted" style="font-size:.75rem;">Sessions</div>
                </div>
                <div class="col-6 text-center p-2 rounded" style="background:var(--bg);">
                  <div class="fw-600 small">
                    <?= $p['last_session'] ? date('M j', strtotime($p['last_session'])) : 'None' ?>
                  </div>
                  <div class="text-muted" style="font-size:.75rem;">Last Session</div>
                </div>
              </div>

              <div class="d-flex gap-2">
                <a href="<?= $baseUrl ?>/messages?with=<?= $p['user_id'] ?>" class="btn btn-sm btn-primary flex-fill">
                  <i class="bi bi-chat me-1"></i>Message
                </a>
                <a href="<?= $baseUrl ?>/appointments?patient=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                  <i class="bi bi-calendar me-1"></i>Sessions
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
