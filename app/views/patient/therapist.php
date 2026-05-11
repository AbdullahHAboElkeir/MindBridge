<?php
$baseUrl   = BASE_URL;
$therapist = $therapist ?? [];
$reviews   = $reviews ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-person-badge me-2"></i>Dr. <?= htmlspecialchars($therapist['first_name'] . ' ' . $therapist['last_name']) ?></h1>
    <p>Reviews and ratings from patients who shared their experience publicly.</p>
  </div>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card fade-in-up text-center py-4">
        <?php if (!empty($therapist['avatar']) && file_exists(UPLOAD_PATH . $therapist['avatar'])): ?>
          <img src="<?= $baseUrl ?>/uploads/<?= htmlspecialchars($therapist['avatar']) ?>"
               class="avatar-xl mx-auto border border-3 mb-3" style="border-color:var(--primary)!important;">
        <?php else: ?>
          <div class="avatar avatar-xl mx-auto mb-3" style="font-size:2rem;">
            <?= strtoupper(substr($therapist['first_name'] ?? 'T', 0, 1)) ?>
          </div>
        <?php endif; ?>
        <h5 class="fw-700">Dr. <?= htmlspecialchars($therapist['first_name'] . ' ' . $therapist['last_name']) ?></h5>
        <div class="mb-3 text-muted small">
          <?= htmlspecialchars($therapist['specializations'] ?? '') ?>
        </div>
        <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
          <?php $rounded = round((float)($therapist['rating'] ?? 0)); ?>
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="bi bi-star<?= $i <= $rounded ? '-fill' : '' ?> text-warning"></i>
          <?php endfor; ?>
          <span class="text-muted"><?= number_format((float)($therapist['rating'] ?? 0), 1) ?> / 5</span>
        </div>
        <div class="text-muted small mb-3">
          <?= (int)($therapist['total_reviews'] ?? 0) ?> review(s)
        </div>
        <div class="text-muted small mb-3">
          <i class="bi bi-translate me-1"></i><?= htmlspecialchars($therapist['languages'] ?? 'English') ?>
        </div>
        <div class="text-muted small mb-3">
          <i class="bi bi-currency-dollar me-1"></i>$<?= number_format((float)($therapist['session_rate'] ?? 0), 2) ?> / session
        </div>
        <a href="<?= $baseUrl ?>/appointments/book" class="btn btn-primary w-100">
          <i class="bi bi-calendar-plus me-2"></i>Book a Session
        </a>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card fade-in-up mb-4">
        <div class="card-header"><i class="bi bi-info-circle text-primary me-2"></i>About the Therapist</div>
        <div class="card-body">
          <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($therapist['bio'] ?? 'No bio available.')) ?></p>
        </div>
      </div>

      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-star-fill text-warning me-2"></i>Patient Reviews</div>
        <div class="card-body">
          <?php if (empty($reviews)): ?>
            <div class="text-center py-5 text-muted">
              <i class="bi bi-chat-left-text fs-1 d-block mb-3 opacity-40"></i>
              <h5>No public reviews yet</h5>
              <p class="mb-0">Once patients submit feedback and do not choose anonymous mode, their reviews will appear here.</p>
            </div>
          <?php else: ?>
            <div class="d-flex flex-column gap-3">
              <?php foreach ($reviews as $review): ?>
                <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                      <div class="fw-600"><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></div>
                      <?php if (!empty($review['scheduled_at'])): ?>
                        <div class="text-muted small">Session: <?= date('M j, Y', strtotime($review['scheduled_at'])) ?></div>
                      <?php endif; ?>
                    </div>
                    <div class="text-warning">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= (int)$review['rating'] ? '-fill' : '' ?>"></i>
                      <?php endfor; ?>
                    </div>
                  </div>
                  <?php if (!empty($review['comment'])): ?>
                    <p class="mb-0 text-muted" style="line-height:1.7;">"<?= nl2br(htmlspecialchars($review['comment'])) ?>"</p>
                  <?php else: ?>
                    <p class="mb-0 text-muted"><em>No comment was provided.</em></p>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>