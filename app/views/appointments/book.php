<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-calendar-plus me-2"></i>Book a Session</h1>
    <p>Schedule your next therapy session</p>
  </div>

  <div class="row g-4" style="max-width:900px;">
    <div class="col-12">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-calendar-plus text-primary me-2"></i>Session Details</div>
        <div class="card-body">
          <form method="POST" action="<?= $baseUrl ?>/appointments/store" class="needs-validation" novalidate id="bookForm">

            <!-- Therapist -->
            <div class="mb-4">
              <label class="form-label fw-600">Select Therapist <span class="text-danger">*</span></label>
              <select name="therapist_id" id="therapistSelect" class="form-select" required>
                <option value="">— Choose a therapist —</option>
                <?php foreach ($therapists as $th): ?>
                  <option value="<?= $th['id'] ?>"
                    <?= $selectedTherapistId === (int)$th['id'] ? 'selected' : '' ?>>
                    Dr. <?= htmlspecialchars($th['first_name'].' '.$th['last_name']) ?>
                    (<?= htmlspecialchars($th['specializations'] ?? 'General') ?>) · $<?= number_format($th['session_rate'],0) ?>/session
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row g-3 mb-4">
              <!-- Date -->
              <div class="col-md-6">
                <label class="form-label fw-600">Date <span class="text-danger">*</span></label>
                <input type="date" id="apptDate" name="date" class="form-control" required
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
              </div>

              <!-- Time slot -->
              <div class="col-md-6">
                <label class="form-label fw-600">Available Time Slots <span class="text-danger">*</span></label>
                <select name="time" id="timeSelect" class="form-select" required>
                  <option value="">— Select date &amp; therapist first —</option>
                </select>
                <div id="slotLoader" class="form-text text-muted d-none">
                  <span class="spinner-border spinner-border-sm me-1"></span>Loading slots…
                </div>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-600">Session Type</label>
                <div class="d-flex gap-2">
                  <?php foreach (['video'=>['camera-video','Video Call'],'audio'=>['mic','Audio Call'],'chat'=>['chat','Chat']] as $v=>[$icon,$lbl]): ?>
                    <div class="flex-fill">
                      <input type="radio" class="btn-check" name="type" id="type_<?= $v ?>" value="<?= $v ?>" <?= $v==='video'?'checked':'' ?>>
                      <label class="btn btn-outline-primary w-100" for="type_<?= $v ?>">
                        <i class="bi bi-<?= $icon ?> me-1"></i><?= $lbl ?>
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600">Duration</label>
                <input type="text" class="form-control" value="50 minutes (standard)" readonly style="background:var(--bg);">
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-600">Notes for Therapist (optional)</label>
              <textarea name="notes" class="form-control" rows="2"
                        placeholder="Anything you'd like your therapist to know before the session?"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-calendar-check me-2"></i>Confirm Booking
            </button>
            <a href="<?= $baseUrl ?>/appointments" class="btn btn-outline-primary btn-lg ms-2">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const therapistEl = document.getElementById('therapistSelect');
const dateEl      = document.getElementById('apptDate');
const timeEl      = document.getElementById('timeSelect');
const loader      = document.getElementById('slotLoader');
const baseUrl     = '<?= $baseUrl ?>';

async function loadSlots() {
  const tid  = therapistEl.value;
  const date = dateEl.value;
  if (!tid || !date) return;

  loader.classList.remove('d-none');
  timeEl.innerHTML = '<option value="">Loading…</option>';

  try {
    const res  = await fetch(`${baseUrl}/appointments/slots?therapist_id=${tid}&date=${date}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    loader.classList.add('d-none');

    if (!data.slots || data.slots.length === 0) {
      timeEl.innerHTML = '<option value="">No slots available for this date</option>';
    } else {
      timeEl.innerHTML = '<option value="">— Select a time —</option>' +
        data.slots.map(s => {
          const [h,m]  = s.split(':');
          const hour   = parseInt(h);
          const label  = `${hour > 12 ? hour-12 : hour}:${m} ${hour >= 12 ? 'PM' : 'AM'}`;
          return `<option value="${s}">${label}</option>`;
        }).join('');
    }
  } catch(e) {
    loader.classList.add('d-none');
    timeEl.innerHTML = '<option value="">Error loading slots</option>';
  }
}

therapistEl.addEventListener('change', loadSlots);
dateEl.addEventListener('change', loadSlots);

// Auto-load if therapist pre-selected
if (therapistEl.value) loadSlots();
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
