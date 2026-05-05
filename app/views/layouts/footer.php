<?php
/**
 * MindBridge — Master Footer / Layout
 * Closes app-layout div, loads all scripts, initializes charts and AJAX.
 */
$baseUrl   = BASE_URL;
$isLoggedIn= Session::isLoggedIn();
?>
</div><!-- /.app-layout -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<!-- App JS -->
<script src="<?= $baseUrl ?>/assets/js/app.js"></script>

<script>
/* ── Sidebar toggle ── */
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar       = document.getElementById('sidebar');
if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
}

/* ── Flash auto-dismiss ── */
const flash = document.getElementById('flashMsg');
if (flash) setTimeout(() => flash.style.opacity = '0', 4000);

/* ── Public nav scroll effect ── */
const pubNav = document.getElementById('publicNav');
if (pubNav) {
  window.addEventListener('scroll', () => {
    pubNav.classList.toggle('scrolled', window.scrollY > 40);
  });
}

/* ── Notification polling ── */
<?php if ($isLoggedIn): ?>
async function pollNotifications() {
  try {
    const res  = await fetch('<?= $baseUrl ?>/notifications/poll', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    const badge = document.getElementById('notifBadge');
    const list  = document.getElementById('notifList');

    if (badge) {
      badge.textContent = data.count || '';
      badge.style.display = data.count > 0 ? 'flex' : 'none';
    }

    if (list && data.recent) {
      if (data.recent.length === 0) {
        list.innerHTML = '<p class="text-center text-muted py-4 small">No new notifications.</p>';
      } else {
        list.innerHTML = data.recent.map(n => `
          <a href="<?= $baseUrl ?>${n.link || '/notifications'}" class="d-flex gap-2 p-3 border-bottom text-decoration-none" style="color:var(--text);">
            <i class="bi bi-bell text-primary flex-shrink-0 mt-1"></i>
            <div>
              <div class="fw-600 small">${n.title}</div>
              <div class="text-muted" style="font-size:.75rem;">${n.message.substring(0,60)}…</div>
            </div>
          </a>`).join('');
      }
    }
  } catch(e) {}
}
pollNotifications();
setInterval(pollNotifications, 30000);
<?php endif; ?>

/* ── Mood chart (dashboard patient) ── */
if (window.moodData) {
  const ctx = document.getElementById('moodChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: window.moodData.labels,
        datasets: [{
          label: 'Mood Level',
          data: window.moodData.values,
          borderColor: '#4A90E2',
          backgroundColor: 'rgba(74,144,226,.12)',
          borderWidth: 2,
          fill: true,
          tension: .4,
          pointBackgroundColor: '#4A90E2',
          pointRadius: 4,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { min: 1, max: 10, ticks: { stepSize: 2 }, grid: { color: 'rgba(0,0,0,.04)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }
}

/* ── Sessions chart (dashboard therapist/admin) ── */
if (window.sessionsData) {
  const ctx = document.getElementById('sessionsChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: window.sessionsData.labels,
        datasets: [{
          label: 'Sessions',
          data: window.sessionsData.values,
          backgroundColor: 'rgba(42,192,181,.7)',
          borderColor: '#2AC0B5',
          borderWidth: 2,
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } }
      }
    });
  }
}

/* ── Mood range slider label ── */
const moodRange = document.getElementById('moodRange');
const moodLabel = document.getElementById('moodLabel');
const moodLabelMap = <?= json_encode(MOOD_LABELS ?? array_fill(1,10,'')) ?>;
if (moodRange && moodLabel) {
  function updateMoodLabel() {
    const v = moodRange.value;
    const emoji = ['😞','😟','😕','😐','😶','🙂','😊','😄','😁','🤩'][v-1];
    moodLabel.textContent = `${moodLabelMap[v] || ''} ${emoji}`;
    moodRange.style.setProperty('--pct', ((v-1)/9)*100 + '%');
  }
  moodRange.addEventListener('input', updateMoodLabel);
  updateMoodLabel();
}

/* ── Goal progress AJAX ── */
document.querySelectorAll('.goal-progress-input').forEach(input => {
  let timer;
  input.addEventListener('change', function() {
    clearTimeout(timer);
    const goalId   = this.dataset.goalId;
    const progress = this.value;
    const bar      = document.getElementById('goal-bar-' + goalId);
    if (bar) bar.style.width = progress + '%';
    timer = setTimeout(async () => {
      try {
        const fd = new FormData();
        fd.append('goal_id', goalId);
        fd.append('progress', progress);
        await fetch('<?= $baseUrl ?>/wellness/updateGoalProgress', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
      } catch(e) {}
    }, 600);
  });
});

/* ── Bootstrap validation ── */
document.querySelectorAll('.needs-validation').forEach(form => {
  form.addEventListener('submit', e => {
    if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    form.classList.add('was-validated');
  });
});
</script>

<?php if ($isLoggedIn): ?>
<footer class="app-footer">
  <div class="container text-center">
    <span class="text-muted small">
      &copy; <?= date('Y') ?> MindBridge · Built with
      <i class="bi bi-heart-fill" style="color:var(--primary);"></i>
      for better mental health
    </span>
  </div>
</footer>
<?php else: ?>
<footer class="public-footer">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="bi bi-heart-pulse-fill" style="color:var(--primary);font-size:1.4rem;"></i>
          <span class="fw-800" style="font-size:1.1rem;">Mind<span style="color:var(--primary);">Bridge</span></span>
        </div>
        <p class="text-muted small">A holistic mental health & wellness portal connecting people with professional care.</p>
      </div>
      <div class="col-md-4">
        <h6 class="fw-700 mb-3">Platform</h6>
        <ul class="list-unstyled text-muted small">
          <li class="mb-2"><a href="<?= $baseUrl ?>/auth/register" class="text-muted text-decoration-none">Get Started</a></li>
          <li class="mb-2"><a href="<?= $baseUrl ?>/auth/login" class="text-muted text-decoration-none">Sign In</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="fw-700 mb-3">Crisis Support</h6>
        <ul class="list-unstyled text-muted small">
          <li class="mb-2"><a href="tel:988" class="text-muted text-decoration-none"><i class="bi bi-telephone me-1"></i>988 Lifeline</a></li>
          <li class="mb-2"><a href="sms:741741" class="text-muted text-decoration-none"><i class="bi bi-chat me-1"></i>Text HOME to 741741</a></li>
        </ul>
      </div>
    </div>
    <div class="border-top pt-3 text-center text-muted small">
      &copy; <?= date('Y') ?> MindBridge. Academic project — not a substitute for professional care.
    </div>
  </div>
</footer>
<?php endif; ?>

</body>
</html>
