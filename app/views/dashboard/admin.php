<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-shield-check me-2"></i>Admin Dashboard</h1>
    <p>System overview — <?= date('l, F j, Y') ?></p>
  </div>

  <!-- Stats -->
  <div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card primary fade-in-up">
        <div class="stat-icon"><i class="bi bi-people"></i></div>
        <div class="stat-value"><?= $totalUsers ?></div>
        <div class="stat-label">Total Users</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card secondary fade-in-up">
        <div class="stat-icon"><i class="bi bi-camera-video"></i></div>
        <div class="stat-value"><?= $totalSessions ?></div>
        <div class="stat-label">Sessions Completed</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card success fade-in-up">
        <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="stat-value">$<?= number_format($monthRevenue, 0) ?></div>
        <div class="stat-label">Revenue This Month</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card accent fade-in-up">
        <div class="stat-icon"><i class="bi bi-heart-pulse"></i></div>
        <div class="stat-value"><?= $crisisNew ?></div>
        <div class="stat-label">New Crisis Alerts</div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card text-center py-3 fade-in-up">
      <div class="fw-700 fs-4 text-primary"><?= $totalPatients ?></div>
      <div class="text-muted small">Patients</div>
    </div></div>
    <div class="col-md-4"><div class="card text-center py-3 fade-in-up">
      <div class="fw-700 fs-4" style="color:var(--secondary);"><?= $totalTherapists ?></div>
      <div class="text-muted small">Therapists</div>
    </div></div>
    <div class="col-md-4"><div class="card text-center py-3 fade-in-up">
      <div class="fw-700 fs-4 text-danger"><?= $pendingReports ?></div>
      <div class="text-muted small">Pending Reports</div>
    </div></div>
  </div>

  <div class="row g-4">

    <!-- Sessions Chart -->
    <div class="col-lg-5">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-bar-chart text-primary me-2"></i>Sessions (Last 6 Months)</div>
        <div class="card-body">
          <canvas id="sessionsChart" height="200"></canvas>
          <script>
            window.sessionsData = {
              labels: <?= json_encode($sessLabels) ?>,
              values: <?= json_encode($sessValues) ?>
            };
          </script>
        </div>
      </div>
    </div>

    <!-- Crisis Alerts -->
    <div class="col-lg-4">
      <div class="card fade-in-up">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-heart-pulse text-danger me-2"></i>New Crisis Alerts</span>
          <a href="<?= $baseUrl ?>/crisis" class="btn btn-sm btn-outline-danger">View All</a>
        </div>
        <div class="card-body p-0">
          <?php if (empty($recentCrisis)): ?>
            <p class="text-center text-muted py-4"><i class="bi bi-check-circle text-success d-block fs-2 mb-2"></i>No active crisis alerts.</p>
          <?php else: ?>
            <?php foreach ($recentCrisis as $c): ?>
              <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="avatar" style="background:linear-gradient(135deg,#FF6B6B,#ee5a24);">
                  <?= strtoupper(substr($c['first_name'],0,1)) ?>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-600 small"><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?></div>
                  <div class="text-muted" style="font-size:.75rem;">
                    <?= ucfirst($c['severity']) ?> severity · <?= ucfirst($c['source']) ?>
                  </div>
                </div>
                <a href="<?= $baseUrl ?>/crisis/respond/<?= $c['id'] ?>" class="btn btn-sm btn-danger">
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Quick Actions + Recent Users -->
    <div class="col-lg-3">
      <div class="card fade-in-up mb-4">
        <div class="card-header"><i class="bi bi-lightning text-primary me-2"></i>Admin Actions</div>
        <div class="card-body p-2">
          <a href="<?= $baseUrl ?>/admin/users" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-people me-2"></i>Manage Users</a>
          <a href="<?= $baseUrl ?>/admin/reports" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-flag me-2"></i>Review Reports
            <?= $pendingReports > 0 ? "<span class='badge bg-danger ms-1'>$pendingReports</span>" : '' ?>
          </a>
          <a href="<?= $baseUrl ?>/admin/resources" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-book-heart me-2"></i>Manage Resources</a>
          <a href="<?= $baseUrl ?>/admin/analytics" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-bar-chart me-2"></i>Analytics</a>
          <a href="<?= $baseUrl ?>/admin/auditLogs" class="btn btn-outline-primary w-100 text-start">
            <i class="bi bi-clock-history me-2"></i>Audit Logs</a>
        </div>
      </div>
    </div>

    <!-- Recent Users Table -->
    <div class="col-12">
      <div class="card fade-in-up">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-people text-primary me-2"></i>Recently Registered Users</span>
          <a href="<?= $baseUrl ?>/admin/users" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
          <table class="table-mindbridge table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th><th>Name</th><th>Role</th><th>Status</th><th>Registered</th><th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $u): ?>
                <tr>
                  <td><?= $u['id'] ?></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar" style="width:32px;height:32px;font-size:.75rem;">
                        <?= strtoupper(substr($u['first_name'],0,1)) ?>
                      </div>
                      <?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?>
                    </div>
                  </td>
                  <td><span class="badge-status <?= $u['role'] === 'admin' ? 'confirmed' : ($u['role'] === 'therapist' ? 'scheduled' : 'pending') ?>"><?= ucfirst($u['role']) ?></span></td>
                  <td><span class="badge-status <?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
                  <td class="text-muted small"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                  <td>
                    <a href="<?= $baseUrl ?>/admin/manageUser/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
