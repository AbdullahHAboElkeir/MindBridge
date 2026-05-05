<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-heart-pulse me-2 text-danger"></i>Respond to Crisis</h1>
    <p>Patient: <?= htmlspecialchars($alert['first_name'].' '.$alert['last_name']) ?></p>
  </div>

  <div style="max-width:680px;margin:0 auto;">
    <div class="alert alert-danger fade-in-up">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Crisis Alert</strong> — Severity: <?= ucfirst($alert['severity']) ?> · Source: <?= ucfirst($alert['source']) ?>
    </div>

    <div class="card mb-4 fade-in-up">
      <div class="card-header"><i class="bi bi-chat-quote text-danger me-2"></i>Trigger Content</div>
      <div class="card-body">
        <p style="font-size:.95rem;line-height:1.8;">
          <?= nl2br(htmlspecialchars($alert['trigger_text'] ?? 'No text available.')) ?>
        </p>
        <div class="text-muted small">Detected: <?= date('D, M j Y \a\t g:i A', strtotime($alert['created_at'])) ?></div>
      </div>
    </div>

    <!-- Emergency Resources -->
    <div class="card mb-4 fade-in-up">
      <div class="card-header"><i class="bi bi-telephone text-primary me-2"></i>Emergency Resources</div>
      <div class="card-body">
        <div class="row g-3">
          <?php foreach ([
            ['988 Suicide & Crisis Lifeline','Call or text 988','tel:988','primary'],
            ['Crisis Text Line','Text HOME to 741741','sms:741741','success'],
            ['International Crisis Lines','findahelpline.com','https://findahelpline.com','secondary'],
          ] as [$title,$desc,$link,$color]): ?>
            <div class="col-md-4">
              <a href="<?= $link ?>" class="text-decoration-none" target="_blank">
                <div class="p-3 rounded text-center" style="background:var(--<?= $color ?>-light,var(--bg));border:1px solid var(--border);">
                  <div class="fw-700 small"><?= $title ?></div>
                  <div class="text-muted" style="font-size:.78rem;"><?= $desc ?></div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Resolution form -->
    <div class="card fade-in-up">
      <div class="card-header"><i class="bi bi-check-circle text-success me-2"></i>Document Response</div>
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/crisis/resolve">
          <input type="hidden" name="alert_id" value="<?= $alert['id'] ?>">
          <div class="mb-3">
            <label class="form-label fw-600">Response Notes <span class="text-danger">*</span></label>
            <textarea name="response_note" class="form-control" rows="4" required
                      placeholder="Describe the action taken: contacted patient, referred to emergency services, etc."></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-lg">
              <i class="bi bi-check-circle me-2"></i>Mark Resolved
            </button>
            <a href="<?= $baseUrl ?>/crisis" class="btn btn-outline-primary btn-lg">Back</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
