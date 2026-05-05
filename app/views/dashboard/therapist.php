<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-person-badge me-2"></i>Therapist Dashboard</h1>
    <p>Welcome back, Dr. <?= htmlspecialchars($therapist['first_name'].' '.$therapist['last_name']) ?> — <?= date('l, F j, Y') ?></p>
  </div>

  <!-- Stats -->
  <div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card primary fade-in-up">
        <div class="stat-icon"><i class="bi bi-people"></i></div>
        <div class="stat-value"><?= $totalPatients ?></div>
        <div class="stat-label">Active Patients</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card secondary fade-in-up">
        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-value"><?= count($todayAppointments) ?></div>
        <div class="stat-label">Sessions Today</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card success fade-in-up">
        <div class="stat-icon"><i class="bi bi-camera-video"></i></div>
        <div class="stat-value"><?= $monthSessions ?></div>
        <div class="stat-label">Sessions This Month</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card accent fade-in-up">
        <div class="stat-icon"><i class="bi bi-star-fill"></i></div>
        <div class="stat-value"><?= $rating ?></div>
        <div class="stat-label">Average Rating</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Today's Schedule -->
    <div class="col-lg-5">
      <div class="card h-100 fade-in-up">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-calendar-day text-primary me-2"></i>Today's Schedule</span>
          <a href="<?= $baseUrl ?>/appointments" class="btn btn-sm btn-outline-primary">Full Calendar</a>
        </div>
        <div class="card-body p-0">
          <?php if (empty($todayAppointments)): ?>
            <div class="p-4 text-center text-muted">
              <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
              No sessions scheduled today.
            </div>
          <?php else: ?>
            <?php foreach ($todayAppointments as $appt): ?>
              <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="avatar">
                  <?= strtoupper(substr($appt['p_first'],0,1)) ?>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-600"><?= htmlspecialchars($appt['p_first'].' '.$appt['p_last']) ?></div>
                  <div class="text-muted small">
                    <i class="bi bi-clock me-1"></i><?= date('g:i A', strtotime($appt['scheduled_at'])) ?>
                    &nbsp;·&nbsp;<?= ucfirst($appt['type']) ?>
                  </div>
                </div>
                <span class="badge-status <?= $appt['status'] ?>"><?= ucfirst($appt['status']) ?></span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Sessions Chart -->
    <div class="col-lg-4">
      <div class="card h-100 fade-in-up">
        <div class="card-header"><i class="bi bi-bar-chart text-primary me-2"></i>Sessions (6 Months)</div>
        <div class="card-body">
          <canvas id="sessionsChart" height="180"></canvas>
          <script>
            window.sessionsData = {
              labels: <?= json_encode($sessLabels) ?>,
              values: <?= json_encode($sessValues) ?>
            };
          </script>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-3">
      <div class="card fade-in-up mb-4">
        <div class="card-header"><i class="bi bi-lightning text-primary me-2"></i>Quick Actions</div>
        <div class="card-body p-2">
          <a href="<?= $baseUrl ?>/therapist/availability" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-clock me-2"></i>Manage Availability
          </a>
          <a href="<?= $baseUrl ?>/therapist/patients" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-people me-2"></i>My Patients
          </a>
          <a href="<?= $baseUrl ?>/messages" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-chat-dots me-2"></i>Messages <?= $unreadMessages > 0 ? "<span class='badge bg-danger ms-1'>$unreadMessages</span>" : '' ?>
          </a>
          <a href="<?= $baseUrl ?>/therapist/profile" class="btn btn-outline-primary w-100 text-start">
            <i class="bi bi-person me-2"></i>My Profile
          </a>
        </div>
      </div>

      <!-- Upcoming -->
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-calendar-week text-primary me-2"></i>Upcoming (7 days)</div>
        <div class="card-body p-0">
          <?php if (empty($upcomingAppointments)): ?>
            <p class="text-muted text-center py-3 small">No upcoming sessions.</p>
          <?php else: ?>
            <?php foreach ($upcomingAppointments as $a): ?>
              <div class="px-3 py-2 border-bottom">
                <div class="fw-600 small"><?= htmlspecialchars($a['p_first'].' '.$a['p_last']) ?></div>
                <div class="text-muted" style="font-size:.78rem;">
                  <?= date('D M j, g:i A', strtotime($a['scheduled_at'])) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
