<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$scheduledTs  = strtotime($appt['scheduled_at']);
$minutesUntil = (int)(($scheduledTs - time()) / 60);
$isTime       = $minutesUntil <= 10;
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-camera-video me-2"></i>Virtual Waiting Room</h1>
    <p>Your session starts at <?= date('g:i A', $scheduledTs) ?> on <?= date('D, M j Y', $scheduledTs) ?></p>
  </div>

  <div style="max-width:680px;margin:0 auto;">
    <div class="waiting-room fade-in-up mb-4">
      <div class="waiting-pulse">
        <i class="bi bi-camera-video-fill"></i>
      </div>

      <?php if (!$isTime): ?>
        <h3 class="fw-700 mb-2">Session starts in</h3>
        <div id="countdown" class="fw-800 mb-3" style="font-size:2.5rem;color:var(--primary);font-family:var(--font-heading);">
          --:--:--
        </div>
        <p class="text-muted mb-4">Please wait here. The session link will activate 5 minutes before start time.</p>
      <?php else: ?>
        <h3 class="fw-700 mb-2 text-success">It's time for your session!</h3>
        <p class="text-muted mb-4">Your therapist is ready. Click below to begin.</p>
      <?php endif; ?>

      <!-- Session info -->
      <div class="row g-3 mb-4">
        <div class="col-4">
          <div class="p-3 rounded" style="background:rgba(255,255,255,.7);">
            <div class="fw-700" style="font-size:1.2rem;">
              <?= Session::role() === 'patient' ? 'Dr. '.$appt['t_first'].' '.$appt['t_last'] : $appt['p_first'].' '.$appt['p_last'] ?>
            </div>
            <div class="text-muted small"><?= Session::role() === 'patient' ? 'Your Therapist' : 'Patient' ?></div>
          </div>
        </div>
        <div class="col-4">
          <div class="p-3 rounded" style="background:rgba(255,255,255,.7);">
            <div class="fw-700 fs-5"><?= date('g:i A', $scheduledTs) ?></div>
            <div class="text-muted small">Session Time</div>
          </div>
        </div>
        <div class="col-4">
          <div class="p-3 rounded" style="background:rgba(255,255,255,.7);">
            <div class="fw-700 fs-5"><?= $appt['duration_minutes'] ?> min</div>
            <div class="text-muted small">Duration</div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-3 justify-content-center">
        <?php if ($isTime): ?>
          <a href="#" class="btn btn-primary btn-lg">
            <i class="bi bi-camera-video-fill me-2"></i>Join Session Now
          </a>
        <?php else: ?>
          <button class="btn btn-primary btn-lg" disabled id="joinBtn">
            <i class="bi bi-camera-video me-2"></i>Waiting for session…
          </button>
        <?php endif; ?>
        <a href="<?= $baseUrl ?>/appointments" class="btn btn-outline-primary btn-lg">
          <i class="bi bi-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <!-- Tips while waiting -->
    <div class="card fade-in-up">
      <div class="card-header"><i class="bi bi-lightbulb text-primary me-2"></i>While You Wait</div>
      <div class="card-body">
        <div class="row g-3">
          <?php foreach ([
            ['camera-video','Check your camera & mic','Test your audio and video before the session starts.'],
            ['wifi','Check your connection','Ensure you have a stable internet connection for a smooth session.'],
            ['journal-text','Prepare your thoughts','Think about what you want to discuss today.'],
            ['shield-lock','Private space','Find a quiet, private space where you feel comfortable talking.'],
          ] as [$icon,$title,$desc]): ?>
            <div class="col-md-6">
              <div class="d-flex gap-3 align-items-start">
                <div class="avatar flex-shrink-0" style="width:40px;height:40px;background:var(--primary-light);">
                  <i class="bi bi-<?= $icon ?>" style="color:var(--primary);font-size:1.1rem;"></i>
                </div>
                <div>
                  <div class="fw-600 small"><?= $title ?></div>
                  <div class="text-muted small"><?= $desc ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const sessionTime = <?= $scheduledTs * 1000 ?>;
const joinBtn = document.getElementById('joinBtn');

function updateCountdown() {
  const now  = Date.now();
  const diff = Math.max(0, Math.floor((sessionTime - now) / 1000));
  const h    = Math.floor(diff / 3600);
  const m    = Math.floor((diff % 3600) / 60);
  const s    = diff % 60;
  const el   = document.getElementById('countdown');
  if (el) el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

  // Enable join button 5 min before
  if (diff <= 300 && joinBtn) {
    joinBtn.disabled = false;
    joinBtn.innerHTML = '<i class="bi bi-camera-video-fill me-2"></i>Join Session Now';
  }
}
setInterval(updateCountdown, 1000);
updateCountdown();
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
