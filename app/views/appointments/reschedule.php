<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-calendar-event me-2"></i>Reschedule Appointment</h1>
    <p>Choose a new date and time for your session</p>
  </div>
  <div style="max-width:560px;margin:0 auto;">
    <div class="card fade-in-up">
      <div class="card-body">
        <div class="p-3 rounded mb-4" style="background:var(--bg);">
          <div class="fw-600">Current Appointment</div>
          <div class="text-muted"><?= date('D, M j Y \a\t g:i A', strtotime($appt['scheduled_at'])) ?></div>
        </div>
        <form method="POST" action="<?= $baseUrl ?>/appointments/doReschedule" class="needs-validation" novalidate>
          <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
          <div class="mb-3">
            <label class="form-label fw-600">New Date <span class="text-danger">*</span></label>
            <input type="date" name="date" id="reschedDate" class="form-control" required
                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
          </div>
          <div class="mb-4">
            <label class="form-label fw-600">New Time <span class="text-danger">*</span></label>
            <select name="time" id="reschedTime" class="form-select" required>
              <option value="">— Select date first —</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-calendar-check me-2"></i>Confirm Reschedule
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('reschedDate').addEventListener('change', async function() {
  const date = this.value;
  const tid  = <?= $appt['therapist_id'] ?>;
  const sel  = document.getElementById('reschedTime');
  sel.innerHTML = '<option>Loading…</option>';
  const res  = await fetch(`<?= $baseUrl ?>/appointments/slots?therapist_id=${tid}&date=${date}`);
  const data = await res.json();
  sel.innerHTML = (data.slots||[]).length
    ? '<option value="">— Select —</option>' + data.slots.map(s => `<option value="${s}">${s}</option>`).join('')
    : '<option value="">No slots available</option>';
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
