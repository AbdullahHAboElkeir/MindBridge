<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-star me-2"></i>Rate Your Session</h1>
    <p>Your feedback helps improve care quality</p>
  </div>

  <div style="max-width:540px;margin:0 auto;">
    <div class="card fade-in-up">
      <div class="card-body">
        <div class="d-flex gap-3 mb-4 p-3 rounded" style="background:var(--bg);">
          <div class="avatar">
            <?= strtoupper(substr($appt['t_first'],0,1)) ?>
          </div>
          <div>
            <div class="fw-700">Dr. <?= htmlspecialchars($appt['t_first'].' '.$appt['t_last']) ?></div>
            <div class="text-muted small">
              <?= date('D, M j Y \a\t g:i A', strtotime($appt['scheduled_at'])) ?> · <?= ucfirst($appt['type']) ?>
            </div>
          </div>
        </div>

        <form method="POST" action="<?= $baseUrl ?>/feedback/store">
          <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
          <input type="hidden" name="therapist_id" value="<?= $appt['therapist_db_id'] ?>">

          <!-- Star Rating -->
          <div class="mb-4 text-center">
            <label class="form-label fw-600 d-block mb-3">How would you rate this session?</label>
            <div class="star-picker" id="starPicker">
              <?php for ($s=1; $s<=5; $s++): ?>
                <i class="bi bi-star-fill" data-val="<?= $s ?>"
                   style="font-size:2.5rem;cursor:pointer;color:#ddd;transition:color .15s;" id="star-<?= $s ?>"></i>
              <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="5">
            <div id="ratingLabel" class="text-muted mt-2" style="font-size:.9rem;">Excellent</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-600">Written Review (optional)</label>
            <textarea name="review" class="form-control" rows="4"
                      placeholder="Share what was helpful about this session…"></textarea>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonFeedback" value="1">
            <label class="form-check-label" for="anonFeedback">Submit anonymously</label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-send me-2"></i>Submit Feedback
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
const labels = ['','Terrible','Poor','Average','Good','Excellent'];
let selected = 5;
function setStars(val) {
  for (let i=1;i<=5;i++) {
    const s = document.getElementById('star-'+i);
    s.style.color = i <= val ? '#F5A623' : '#ddd';
  }
  document.getElementById('ratingInput').value = val;
  document.getElementById('ratingLabel').textContent = labels[val];
  selected = val;
}
setStars(5);
document.querySelectorAll('#starPicker .bi').forEach(star => {
  star.addEventListener('click', () => setStars(+star.dataset.val));
  star.addEventListener('mouseenter', () => setStars(+star.dataset.val));
  star.addEventListener('mouseleave', () => setStars(selected));
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
