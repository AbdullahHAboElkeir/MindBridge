<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-people me-2"></i>Find Your Therapist</h1>
    <p>We've matched you with the best therapists based on your intake preferences.</p>
  </div>

  <?php if (empty($existingMatches)): ?>
    <div class="card fade-in-up text-center py-5">
      <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
      <h5>No matches yet</h5>
      <p class="text-muted">Please complete your intake form first.</p>
      <a href="<?= $baseUrl ?>/patient/intake" class="btn btn-primary">Complete Intake Form</a>
    </div>
  <?php else: ?>
    <p class="text-muted mb-4">
      <i class="bi bi-info-circle me-1"></i>
      <?= count($existingMatches) ?> therapists matched · Click to select your preferred therapist
    </p>

    <div class="row g-4">
      <?php foreach ($existingMatches as $idx => $match): ?>
        <div class="col-lg-6">
          <div class="therapist-card fade-in-up <?= $match['status'] === 'accepted' ? 'selected' : '' ?>">

            <?php if ($match['status'] === 'accepted'): ?>
              <div class="text-center mb-2">
                <span class="badge-status confirmed"><i class="bi bi-check-circle-fill me-1"></i>Your Current Therapist</span>
              </div>
            <?php endif; ?>

            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="avatar avatar-lg flex-shrink-0">
                <?= strtoupper(substr($match['first_name'],0,1)) ?>
              </div>
              <div class="flex-grow-1">
                <h5 class="fw-700 mb-1">Dr. <?= htmlspecialchars($match['first_name'].' '.$match['last_name']) ?></h5>
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <span class="match-score"><?= $match['match_score'] ?>% Match</span>
                  <?php if ($match['gender']): ?>
                    <span class="badge" style="background:var(--bg);color:var(--text-muted);">
                      <?= ucfirst($match['gender']) ?>
                    </span>
                  <?php endif; ?>
                </div>
                <div class="star-rating mb-1">
                  <?php for ($s=1;$s<=5;$s++): ?>
                    <i class="bi bi-star<?= $s <= round($match['rating']) ? '-fill' : '' ?>" style="font-size:.9rem;"></i>
                  <?php endfor; ?>
                  <span class="text-muted small ms-1">(<?= $match['rating'] ?> · <?= $match['total_reviews'] ?? 0 ?> reviews)</span>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="d-flex flex-wrap gap-1 mb-2">
                <?php foreach (array_slice(explode(',', $match['specializations'] ?? ''), 0, 4) as $spec): ?>
                  <span class="forum-category-badge"><?= htmlspecialchars(trim($spec)) ?></span>
                <?php endforeach; ?>
              </div>
              <p class="text-muted small mb-2"><?= htmlspecialchars(substr($match['bio'] ?? '', 0, 160)) ?>…</p>
              <div class="d-flex gap-3 text-muted small">
                <span><i class="bi bi-clock me-1"></i><?= $match['years_experience'] ?>yr exp</span>
                <span><i class="bi bi-translate me-1"></i><?= htmlspecialchars($match['languages'] ?? '') ?></span>
                <span><i class="bi bi-currency-dollar me-1"></i>$<?= number_format($match['session_rate'], 0) ?>/session</span>
              </div>
            </div>

            <!-- Match reasons -->
            <div class="mb-3">
              <?php
              $reasons = json_decode($match['match_reasons'] ?? '[]', true) ?? [];
              $reasonLabels = [
                'specialization_match' => ['Specialization Match','check-circle-fill','success'],
                'gender_preference'    => ['Gender Match','gender-ambiguous','primary'],
                'language_match'       => ['Language Match','translate','info'],
                'availability_match'   => ['Good Availability','calendar-check','secondary'],
              ];
              foreach ($reasons as $r):
                if (isset($reasonLabels[$r])):
                  [$label,$icon,$color] = $reasonLabels[$r];
              ?>
                <span class="badge me-1 mb-1"
                      style="background:var(--<?= $color === 'secondary' ? 'secondary' : $color ?>-light, var(--bg));
                             color:var(--<?= $color === 'secondary' ? 'secondary' : $color ?>);border:1px solid;">
                  <i class="bi bi-<?= $icon ?> me-1"></i><?= $label ?>
                </span>
              <?php endif; endforeach; ?>
            </div>

            <?php if ($match['status'] !== 'accepted'): ?>
              <form method="POST" action="<?= $baseUrl ?>/patient/selectTherapist">
                <input type="hidden" name="therapist_id" value="<?= $match['therapist_id'] ?>">
                <button type="submit" class="btn btn-primary w-100"
                        onclick="return confirm('Select Dr. <?= htmlspecialchars($match['first_name'].' '.$match['last_name']) ?> as your therapist?')">
                  <i class="bi bi-person-check me-2"></i>Choose This Therapist
                </button>
              </form>
            <?php else: ?>
              <a href="<?= $baseUrl ?>/appointments/book" class="btn btn-secondary w-100">
                <i class="bi bi-calendar-plus me-2"></i>Book First Session
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
