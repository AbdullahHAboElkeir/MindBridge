<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$moodLabel = MOOD_LABELS[$todayMood['mood_level'] ?? 0] ?? 'Not logged today';
$onboardingStep = (int)($patient['onboarding_step'] ?? 0);
?>

<div class="main-content">
  <!-- Page Header -->
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-sun me-2"></i>Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?>, <?= htmlspecialchars($patient['first_name']) ?>!</h1>
    <p>Here's your wellness overview for today, <?= date('l, F j, Y') ?>.</p>
  </div>

  <?php if ($onboardingStep < 4): ?>
  <!-- Onboarding Banner -->
  <div class="alert alert-info fade-in-up d-flex align-items-center gap-3 mb-4">
    <i class="bi bi-arrow-right-circle-fill fs-3 flex-shrink-0"></i>
    <div>
      <strong>Complete your onboarding!</strong> You're on step <?= $onboardingStep ?> of 4.
      <a href="<?= $baseUrl ?>/patient/<?= ['intake','intake','consent','matching','matching'][$onboardingStep] ?? 'intake' ?>"
         class="btn btn-sm btn-primary ms-3">Continue Setup</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- Stats Row -->
  <div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card primary fade-in-up">
        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-value"><?= count($appointments) ?></div>
        <div class="stat-label">Upcoming Sessions</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card secondary fade-in-up">
        <div class="stat-icon"><i class="bi bi-camera-video"></i></div>
        <div class="stat-value"><?= $sessionCount ?></div>
        <div class="stat-label">Sessions Completed</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card success fade-in-up">
        <div class="stat-icon"><i class="bi bi-trophy"></i></div>
        <div class="stat-value"><?= $goalCount ?></div>
        <div class="stat-label">Active Goals</div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card accent fade-in-up">
        <div class="stat-icon"><i class="bi bi-chat-dots"></i></div>
        <div class="stat-value"><?= $unreadMessages ?></div>
        <div class="stat-label">Unread Messages</div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    <!-- Mood Card -->
    <div class="col-lg-4">
      <div class="card h-100 fade-in-up">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-emoji-smile text-primary me-2"></i>Today's Mood</span>
          <a href="<?= $baseUrl ?>/wellness/mood" class="btn btn-sm btn-outline-primary">Mood Log</a>
        </div>
        <div class="card-body">
          <?php if ($todayMood): ?>
            <div class="text-center py-2">
              <div style="font-size:3.5rem;">
                <?= ['😞','😟','😕','😐','😶','🙂','😊','😄','😁','🤩'][(int)($todayMood['mood_level'])-1] ?? '😶' ?>
              </div>
              <div class="mood-level-label"><?= htmlspecialchars(MOOD_LABELS[$todayMood['mood_level']] ?? '') ?></div>
              <div class="text-muted small mt-1">Mood level: <?= $todayMood['mood_level'] ?>/10</div>
              <?php if ($todayMood['notes']): ?>
                <p class="text-muted small mt-2">"<?= htmlspecialchars($todayMood['notes']) ?>"</p>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-3">
              <div style="font-size:2.5rem;opacity:.4;">😶</div>
              <p class="text-muted mt-2">No mood logged today.</p>
              <a href="<?= $baseUrl ?>/wellness/mood" class="btn btn-primary btn-sm">Log My Mood</a>
            </div>
          <?php endif; ?>

          <!-- Mini chart -->
          <?php if (!empty($moodValues)): ?>
            <canvas id="moodChart" height="80" class="mt-3"></canvas>
            <script>
              window.moodData = {
                labels: <?= json_encode($moodLabels) ?>,
                values: <?= json_encode($moodValues) ?>
              };
            </script>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="col-lg-5">
      <div class="card h-100 fade-in-up">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-calendar-check text-primary me-2"></i>Upcoming Sessions</span>
          <a href="<?= $baseUrl ?>/appointments" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
          <?php if (empty($appointments)): ?>
            <div class="p-4 text-center text-muted">
              <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
              No upcoming sessions.
              <br><a href="<?= $baseUrl ?>/appointments/book" class="btn btn-sm btn-primary mt-2">Book a Session</a>
            </div>
          <?php else: ?>
            <?php foreach ($appointments as $appt): ?>
              <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="avatar" style="background:linear-gradient(135deg,var(--primary),var(--secondary));">
                  <?= strtoupper(substr($appt['t_first'],0,1)) ?>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-600">Dr. <?= htmlspecialchars($appt['t_first'].' '.$appt['t_last']) ?></div>
                  <div class="text-muted small">
                    <i class="bi bi-calendar me-1"></i><?= date('D, M j', strtotime($appt['scheduled_at'])) ?>
                    &nbsp;·&nbsp;
                    <i class="bi bi-clock me-1"></i><?= date('g:i A', strtotime($appt['scheduled_at'])) ?>
                  </div>
                </div>
                <div>
                  <span class="badge-status <?= $appt['status'] ?>">
                    <?= ucfirst($appt['type']) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
            <div class="p-3 text-center">
              <a href="<?= $baseUrl ?>/appointments/book" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Book New Session
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Quick Links + Journal -->
    <div class="col-lg-3">
      <div class="card mb-4 fade-in-up">
        <div class="card-header"><i class="bi bi-lightning text-primary me-2"></i>Quick Actions</div>
        <div class="card-body p-2">
          <a href="<?= $baseUrl ?>/wellness/journal" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-journal-text me-2"></i>Write in Journal
          </a>
          <a href="<?= $baseUrl ?>/wellness/goals" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-trophy me-2"></i>Update Goals
          </a>
          <a href="<?= $baseUrl ?>/messages" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-chat-dots me-2"></i>Message Therapist
          </a>
          <a href="<?= $baseUrl ?>/wellness/resources" class="btn btn-outline-primary w-100 text-start">
            <i class="bi bi-book-heart me-2"></i>Browse Resources
          </a>
        </div>
      </div>

      <?php if ($recentJournal): ?>
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-journal-text text-primary me-2"></i>Latest Entry</div>
        <div class="card-body">
          <h6 class="fw-600"><?= htmlspecialchars($recentJournal['title']) ?></h6>
          <p class="text-muted small"><?= htmlspecialchars(substr($recentJournal['content'], 0, 120)) ?>…</p>
          <a href="<?= $baseUrl ?>/wellness/journal" class="btn btn-sm btn-outline-primary">View Journal</a>
        </div>
      </div>
      <?php endif; ?>
    </div>

  </div><!-- /row -->
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
