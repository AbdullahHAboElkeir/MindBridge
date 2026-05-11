<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-person me-2"></i>Patient Details</h1>
    <p>Comprehensive view of <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?>'s progress</p>
  </div>

  <div class="row g-4">
    <!-- Patient Info -->
    <div class="col-lg-4">
      <div class="card fade-in-up">
        <div class="card-header">
          <h5 class="mb-0">Patient Information</h5>
        </div>
        <div class="card-body">
          <div class="d-flex gap-3 mb-3">
            <div class="avatar" style="width:60px;height:60px;font-size:1.5rem;">
              <?= strtoupper(substr($patient['first_name'],0,1)) ?>
            </div>
            <div>
              <h6 class="mb-1"><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></h6>
              <p class="text-muted small mb-1"><?= htmlspecialchars($patient['email']) ?></p>
              <?php if ($patient['phone']): ?>
                <p class="text-muted small mb-0"><?= htmlspecialchars($patient['phone']) ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div class="row g-2">
            <?php if ($patient['date_of_birth']): ?>
              <div class="col-6">
                <small class="text-muted d-block">Age</small>
                <div class="fw-600">
                  <?= date_diff(date_create($patient['date_of_birth']), date_create('today'))->y ?> years
                </div>
              </div>
            <?php endif; ?>
            <div class="col-6">
              <small class="text-muted d-block">Language</small>
              <div class="fw-600"><?= htmlspecialchars($patient['preferred_language']) ?></div>
            </div>
          </div>

          <hr>

          <div class="d-flex gap-2">
            <a href="<?= $baseUrl ?>/messages?with=<?= $patient['user_id'] ?>" class="btn btn-sm btn-primary flex-fill">
              <i class="bi bi-chat me-1"></i>Message
            </a>
            <a href="<?= $baseUrl ?>/therapist/generateReport/<?= $patient['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
              <i class="bi bi-file-earmark-pdf me-1"></i>Report
            </a>
          </div>
        </div>
      </div>

      <!-- Mood Summary -->
      <div class="card fade-in-up">
        <div class="card-header">
          <h5 class="mb-0">Mood Summary</h5>
        </div>
        <div class="card-body">
          <div class="row g-3 text-center">
            <div class="col-6">
              <div class="p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="h4 mb-0"><?= number_format($avgMood7, 1) ?></div>
                <small>7-Day Avg</small>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="h4 mb-0"><?= number_format($avgMood30, 1) ?></div>
                <small>30-Day Avg</small>
              </div>
            </div>
          </div>

          <?php if (!empty($moodData)): ?>
            <div class="mt-3">
              <small class="text-muted">Recent Entries</small>
              <div class="mt-2" style="max-height: 150px; overflow-y: auto;">
                <?php foreach (array_slice($moodData, 0, 5) as $entry): ?>
                  <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <small><?= date('M j', strtotime($entry['entry_date'])) ?></small>
                    <span class="badge bg-<?= $entry['mood_level'] >= 7 ? 'success' : ($entry['mood_level'] >= 4 ? 'warning' : 'danger') ?>">
                      <?= $entry['mood_level'] ?>/10
                    </span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Session History & Goals -->
    <div class="col-lg-8">
      <!-- Recent Sessions -->
      <div class="card fade-in-up">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Recent Sessions</h5>
          <a href="<?= $baseUrl ?>/appointments?patient=<?= $patient['id'] ?>" class="btn btn-sm btn-outline-primary">
            View All
          </a>
        </div>
        <div class="card-body">
          <?php if (empty($patientSessions)): ?>
            <p class="text-muted mb-0">No sessions completed yet.</p>
          <?php else: ?>
            <div class="list-group list-group-flush">
              <?php foreach (array_slice($patientSessions, 0, 5) as $session): ?>
                <div class="list-group-item px-0">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-1">
                        <?= date('M j, Y', strtotime($session['scheduled_at'])) ?> - <?= ucfirst($session['type']) ?>
                      </h6>
                      <?php if ($session['therapist_notes']): ?>
                        <p class="text-muted small mb-1">
                          <?= htmlspecialchars(substr($session['therapist_notes'], 0, 100)) ?>...
                        </p>
                      <?php endif; ?>
                      <?php if ($session['outcome']): ?>
                        <span class="badge bg-<?= $session['outcome'] === 'good' ? 'success' : ($session['outcome'] === 'neutral' ? 'warning' : 'danger') ?>">
                          <?= ucfirst($session['outcome']) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    <a href="<?= $baseUrl ?>/therapist/sessionNotes/<?= $session['appointment_id'] ?>" class="btn btn-sm btn-outline-primary">
                      View Notes
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Goals Progress -->
      <div class="card fade-in-up">
        <div class="card-header">
          <h5 class="mb-0">Wellness Goals</h5>
        </div>
        <div class="card-body">
          <?php if (empty($goals)): ?>
            <p class="text-muted mb-0">No goals set yet.</p>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($goals as $goal): ?>
                <div class="col-md-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <h6 class="card-title"><?= htmlspecialchars($goal['title']) ?></h6>
                      <?php if ($goal['description']): ?>
                        <p class="card-text small text-muted">
                          <?= htmlspecialchars(substr($goal['description'], 0, 80)) ?>...
                        </p>
                      <?php endif; ?>

                      <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                          <small class="text-muted">Progress</small>
                          <small class="fw-600"><?= $goal['progress'] ?>%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                          <div class="progress-bar bg-<?= $goal['status'] === 'completed' ? 'success' : ($goal['status'] === 'active' ? 'primary' : 'warning') ?>"
                               style="width: <?= $goal['progress'] ?>%"></div>
                        </div>
                      </div>

                      <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?= $goal['status'] === 'completed' ? 'success' : ($goal['status'] === 'active' ? 'primary' : 'secondary') ?>">
                          <?= ucfirst($goal['status']) ?>
                        </span>
                        <?php if ($goal['target_date']): ?>
                          <small class="text-muted">
                            Due: <?= date('M j, Y', strtotime($goal['target_date'])) ?>
                          </small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>