<?php
$baseUrl   = BASE_URL;
$dayNames  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
// Group existing availability by day
$byDay = [];
foreach ($availability as $slot) {
    $byDay[(int)$slot['day_of_week']][] = $slot;
}
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-clock me-2"></i>Manage Availability</h1>
    <p>Set the days and times you are available for patient sessions</p>
  </div>

  <div style="max-width:700px;margin:0 auto;">
    <div class="card fade-in-up">
      <div class="card-header"><i class="bi bi-calendar-week text-primary me-2"></i>Weekly Schedule</div>
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/therapist/updateAvailability">
          <?php for ($day = 0; $day <= 6; $day++): ?>
            <div class="mb-4">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-700 mb-0"><?= $dayNames[$day] ?></h6>
                <button type="button" class="btn btn-sm btn-outline-primary add-slot" data-day="<?= $day ?>">
                  <i class="bi bi-plus"></i> Add Slot
                </button>
              </div>
              <div id="slots-day-<?= $day ?>">
                <?php foreach (($byDay[$day] ?? []) as $i => $slot): ?>
                  <div class="d-flex gap-2 mb-2 align-items-center slot-row">
                    <input type="hidden" name="slots[<?= $day ?>_<?= $i ?>][day]" value="<?= $day ?>">
                    <input type="time" name="slots[<?= $day ?>_<?= $i ?>][start_time]"
                           class="form-control" value="<?= $slot['start_time'] ?>">
                    <span class="text-muted">to</span>
                    <input type="time" name="slots[<?= $day ?>_<?= $i ?>][end_time]"
                           class="form-control" value="<?= $slot['end_time'] ?>">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-slot">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endfor; ?>

          <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-check-circle me-2"></i>Save Availability
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let slotCount = 100;
document.querySelectorAll('.add-slot').forEach(btn => {
  btn.addEventListener('click', function() {
    const day = this.dataset.day;
    const container = document.getElementById('slots-day-' + day);
    const key = day + '_' + (slotCount++);
    container.insertAdjacentHTML('beforeend', `
      <div class="d-flex gap-2 mb-2 align-items-center slot-row">
        <input type="hidden" name="slots[${key}][day]" value="${day}">
        <input type="time" name="slots[${key}][start_time]" class="form-control">
        <span class="text-muted">to</span>
        <input type="time" name="slots[${key}][end_time]" class="form-control">
        <button type="button" class="btn btn-sm btn-outline-danger remove-slot">
          <i class="bi bi-trash"></i>
        </button>
      </div>`);
    bindRemove();
  });
});

function bindRemove() {
  document.querySelectorAll('.remove-slot').forEach(btn => {
    btn.onclick = () => btn.closest('.slot-row').remove();
  });
}
bindRemove();
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
