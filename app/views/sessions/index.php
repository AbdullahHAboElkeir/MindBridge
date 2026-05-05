<?php
$baseUrl = BASE_URL;
$sessions = $sessions ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-camera-video me-2"></i>
      <?= $role === 'therapist' ? 'Session Records' : 'My Sessions' ?>
    </h1>
    <p><?= $role === 'therapist' ? 'All completed therapy session notes' : 'Your therapy session history' ?></p>
  </div>

  <?php if (empty($sessions)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-camera-video fs-1 text-muted d-block mb-3 opacity-40"></i>
      <h5>No sessions yet</h5>
      <p class="text-muted">Completed sessions with notes will appear here.</p>
      <?php if ($role === 'patient'): ?>
        <a href="<?= $baseUrl ?>/appointments/book" class="btn btn-primary mt-2">
          <i class="bi bi-calendar-plus me-2"></i>Book a Session
        </a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="card fade-in-up">
      <div class="card-header"><i class="bi bi-camera-video text-primary me-2"></i>Session History</div>
      <div class="card-body p-0">
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr>
              <th>Date</th>
              <?php if ($role === 'patient'): ?>
                <th>Therapist</th>
              <?php elseif ($role === 'therapist'): ?>
                <th>Patient</th>
              <?php else: ?>
                <th>Patient</th><th>Therapist</th>
              <?php endif; ?>
              <th>Type</th>
              <th>Outcome</th>
              <th>Follow-up</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sessions as $s): ?>
              <tr>
                <td>
                  <div class="fw-600"><?= date('M j, Y', strtotime($s['scheduled_at'])) ?></div>
                  <div class="text-muted small"><?= date('g:i A', strtotime($s['scheduled_at'])) ?></div>
                </td>
                <?php if ($role === 'patient'): ?>
                  <td>Dr. <?= htmlspecialchars($s['t_first'] . ' ' . $s['t_last']) ?></td>
                <?php elseif ($role === 'therapist'): ?>
                  <td><?= htmlspecialchars($s['p_first'] . ' ' . $s['p_last']) ?></td>
                <?php else: ?>
                  <td><?= htmlspecialchars($s['p_first'] . ' ' . $s['p_last']) ?></td>
                  <td>Dr. <?= htmlspecialchars($s['t_first'] . ' ' . $s['t_last']) ?></td>
                <?php endif; ?>
                <td><span class="badge bg-primary"><?= ucfirst($s['type'] ?? 'video') ?></span></td>
                <td>
                  <?php if ($s['outcome']): ?>
                    <span class="badge-status <?= $s['outcome'] === 'good' ? 'confirmed' : ($s['outcome'] === 'poor' || $s['outcome'] === 'crisis' ? 'cancelled' : 'pending') ?>">
                      <?= ucfirst($s['outcome']) ?>
                    </span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?= $s['follow_up_date'] ? date('M j, Y', strtotime($s['follow_up_date'])) : '<span class="text-muted small">—</span>' ?>
                </td>
                <td>
                  <?php if ($role === 'therapist'): ?>
                    <a href="<?= $baseUrl ?>/therapist/sessionNotes/<?= $s['appointment_id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil me-1"></i>Notes
                    </a>
                  <?php elseif ($s['therapist_notes']): ?>
                    <span class="text-muted small" title="<?= htmlspecialchars(substr($s['therapist_notes'], 0, 100)) ?>">
                      <i class="bi bi-file-text me-1"></i>Available
                    </span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
