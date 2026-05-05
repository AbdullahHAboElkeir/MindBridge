<?php
$baseUrl    = BASE_URL;
$reviews    = $reviews    ?? [];
$avgRating  = $avgRating  ?? ['avg' => 0, 'cnt' => 0];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-star me-2"></i>My Reviews</h1>
    <p>Patient feedback and ratings for your sessions</p>
  </div>

  <!-- Rating summary -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="stat-card primary fade-in-up text-center">
        <div class="stat-value" style="font-size:3rem;">
          <?= number_format((float)$avgRating['avg'], 1) ?>
        </div>
        <div class="d-flex justify-content-center my-2">
          <?php
          $avg = round((float)$avgRating['avg']);
          for ($i = 1; $i <= 5; $i++):
          ?>
            <i class="bi bi-star<?= $i <= $avg ? '-fill' : '' ?> text-warning me-1" style="font-size:1.2rem;"></i>
          <?php endfor; ?>
        </div>
        <div class="stat-label">Average Rating</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card accent fade-in-up">
        <div class="stat-icon"><i class="bi bi-chat-square-quote"></i></div>
        <div class="stat-value"><?= (int)$avgRating['cnt'] ?></div>
        <div class="stat-label">Total Reviews</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card fade-in-up">
        <div class="stat-icon"><i class="bi bi-trophy"></i></div>
        <div class="stat-value">
          <?php
          $pct = $avgRating['cnt'] > 0
              ? round(($avg / 5) * 100)
              : 0;
          echo $pct . '%';
          ?>
        </div>
        <div class="stat-label">Satisfaction Score</div>
      </div>
    </div>
  </div>

  <?php if (empty($reviews)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-star fs-1 text-muted d-block mb-3 opacity-40"></i>
      <h5>No reviews yet</h5>
      <p class="text-muted">Patients can leave reviews after completed sessions.</p>
    </div>
  <?php else: ?>
    <div class="card fade-in-up">
      <div class="card-header">
        <i class="bi bi-star-fill text-warning me-2"></i><?= count($reviews) ?> review(s)
      </div>
      <div class="card-body">
        <div class="d-flex flex-column gap-4">
          <?php foreach ($reviews as $r): ?>
            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-3">
                  <div class="avatar" style="width:40px;height:40px;">
                    <?= strtoupper(substr($r['first_name'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-600"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></div>
                    <div class="text-muted small">
                      Session: <?= date('M j, Y', strtotime($r['scheduled_at'])) ?>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star<?= $i <= $r['rating'] ? '-fill' : '' ?> text-warning"></i>
                  <?php endfor; ?>
                  <span class="fw-700 ms-2"><?= $r['rating'] ?>/5</span>
                </div>
              </div>
              <?php if (!empty($r['comment'])): ?>
                <p class="mb-0 text-muted" style="font-size:.9rem;line-height:1.7;">
                  "<?= htmlspecialchars($r['comment']) ?>"
                </p>
              <?php endif; ?>
              <div class="text-muted mt-2" style="font-size:.75rem;">
                <i class="bi bi-clock me-1"></i><?= date('M j, Y g:i A', strtotime($r['created_at'])) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
