<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$typeIcons = [
    'new_appointment' => ['calendar-check','primary'],
    'new_message'     => ['chat-dots','success'],
    'new_patient'     => ['person-plus','secondary'],
    'crisis_alert'    => ['heart-pulse','danger'],
    'payment'         => ['credit-card','warning'],
    'system'          => ['bell','muted'],
];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-bell me-2"></i>Notifications</h1>
    <p>All your recent activity and alerts</p>
  </div>

  <div style="max-width:720px;margin:0 auto;">
    <?php if (empty($notifications)): ?>
      <div class="card text-center py-5 fade-in-up">
        <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3 opacity-40"></i>
        <h5>No notifications yet</h5>
        <p class="text-muted">You'll see alerts for appointments, messages, and more here.</p>
      </div>
    <?php else: ?>
      <?php foreach ($notifications as $n):
        [$icon, $color] = $typeIcons[$n['type']] ?? ['bell','muted'];
      ?>
        <div class="d-flex gap-3 p-3 mb-2 rounded fade-in-up"
             style="background:var(--white);border:1px solid var(--border);">
          <div class="avatar flex-shrink-0"
               style="width:42px;height:42px;background:var(--<?= $color === 'danger' ? 'error' : $color ?>-light, var(--bg));">
            <i class="bi bi-<?= $icon ?>"
               style="color:var(--<?= $color === 'muted' ? 'text-muted' : $color ?>);font-size:1.1rem;"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-600"><?= htmlspecialchars($n['title']) ?></div>
            <div class="text-muted small"><?= htmlspecialchars($n['message']) ?></div>
            <div class="text-muted" style="font-size:.75rem;margin-top:2px;">
              <?= date('D, M j Y \a\t g:i A', strtotime($n['created_at'])) ?>
            </div>
          </div>
          <?php if ($n['link']): ?>
            <a href="<?= $baseUrl . $n['link'] ?>" class="btn btn-sm btn-outline-primary align-self-center flex-shrink-0">
              View <i class="bi bi-arrow-right ms-1"></i>
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
