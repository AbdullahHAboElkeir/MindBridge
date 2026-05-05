<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$statColors = ['scheduled'=>'scheduled','confirmed'=>'confirmed','completed'=>'completed','cancelled'=>'cancelled','no_show'=>'cancelled','in_progress'=>'active','rescheduled'=>'pending'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-calendar-check me-2"></i>Appointments</h1>
    <p>Manage your therapy sessions and schedule</p>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
      <?php foreach ([''=>'All','scheduled'=>'Upcoming','completed'=>'Completed','cancelled'=>'Cancelled'] as $v=>$l): ?>
        <a href="?status=<?= $v ?>" class="btn btn-sm <?= ($filter??'') === $v ? 'btn-primary' : 'btn-outline-primary' ?>">
          <?= $l ?>
        </a>
      <?php endforeach; ?>
    </div>
    <?php if ($role === 'patient'): ?>
      <a href="<?= $baseUrl ?>/appointments/book" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Book Session
      </a>
    <?php endif; ?>
  </div>

  <div class="card fade-in-up">
    <div class="card-body p-0">
      <?php if (empty($appointments)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-50"></i>
          <h5>No appointments found</h5>
          <?php if ($role === 'patient'): ?>
            <a href="<?= $baseUrl ?>/appointments/book" class="btn btn-primary mt-2">Book Your First Session</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr>
              <th><?= $role === 'patient' ? 'Therapist' : ($role === 'therapist' ? 'Patient' : 'Participants') ?></th>
              <th>Date & Time</th>
              <th>Type</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($appointments as $a): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar" style="width:34px;height:34px;font-size:.8rem;">
                      <?= $role === 'patient' ? strtoupper(substr($a['t_first'],0,1)) : strtoupper(substr($a['p_first'],0,1)) ?>
                    </div>
                    <div>
                      <div class="fw-600">
                        <?php if ($role === 'patient'): ?>
                          Dr. <?= htmlspecialchars($a['t_first'].' '.$a['t_last']) ?>
                        <?php elseif ($role === 'therapist'): ?>
                          <?= htmlspecialchars($a['p_first'].' '.$a['p_last']) ?>
                        <?php else: ?>
                          <?= htmlspecialchars(($a['p_first']??'').' '.($a['p_last']??'')) ?> → Dr. <?= htmlspecialchars(($a['t_first']??'').' '.($a['t_last']??'')) ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="fw-500"><?= date('D, M j Y', strtotime($a['scheduled_at'])) ?></div>
                  <div class="text-muted small"><?= date('g:i A', strtotime($a['scheduled_at'])) ?> · <?= $a['duration_minutes'] ?> min</div>
                </td>
                <td>
                  <i class="bi bi-<?= $a['type']==='video' ? 'camera-video' : ($a['type']==='audio' ? 'mic' : 'chat') ?> me-1"></i>
                  <?= ucfirst($a['type']) ?>
                </td>
                <td>
                  <span class="badge-status <?= $statColors[$a['status']] ?? 'pending' ?>">
                    <?= ucfirst(str_replace('_',' ',$a['status'])) ?>
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <?php if (in_array($a['status'],['scheduled','confirmed'])): ?>
                      <a href="<?= $baseUrl ?>/appointments/waitingRoom/<?= $a['id'] ?>" class="btn btn-sm btn-primary" title="Join">
                        <i class="bi bi-camera-video"></i>
                      </a>
                      <a href="<?= $baseUrl ?>/appointments/reschedule/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary" title="Reschedule">
                        <i class="bi bi-calendar-event"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-outline-danger"
                              data-bs-toggle="modal" data-bs-target="#cancelModal"
                              data-id="<?= $a['id'] ?>" title="Cancel">
                        <i class="bi bi-x-circle"></i>
                      </button>
                    <?php endif; ?>
                    <?php if ($a['status'] === 'completed' && $role === 'therapist'): ?>
                      <a href="<?= $baseUrl ?>/sessions/addNotes/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Notes
                      </a>
                    <?php endif; ?>
                    <?php if ($a['status'] === 'completed' && $role === 'patient'): ?>
                      <a href="<?= $baseUrl ?>/feedback/create/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-star"></i> Rate
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:var(--radius);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-700">Cancel Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= $baseUrl ?>/appointments/cancel/0" id="cancelForm">
        <div class="modal-body">
          <p class="text-muted">Please provide a reason for cancellation:</p>
          <textarea name="cancel_reason" class="form-control" rows="3" placeholder="Optional reason…"></textarea>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Keep Appointment</button>
          <button type="submit" class="btn btn-danger">Cancel Appointment</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('cancelModal').addEventListener('show.bs.modal', e => {
  const id = e.relatedTarget.dataset.id;
  document.getElementById('cancelForm').action = '<?= $baseUrl ?>/appointments/cancel/' + id;
});
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
