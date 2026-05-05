<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-pencil-square me-2"></i>Session Notes</h1>
    <p>Document the session for <?= htmlspecialchars($appt['p_first'].' '.$appt['p_last']) ?> on <?= date('D, M j Y \a\t g:i A', strtotime($appt['scheduled_at'])) ?></p>
  </div>

  <div style="max-width:720px;margin:0 auto;">
    <div class="card fade-in-up">
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/therapist/saveNotes" class="needs-validation" novalidate>
          <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">

          <div class="mb-3">
            <label class="form-label fw-600">Session Notes <span class="text-danger">*</span></label>
            <textarea name="therapist_notes" class="form-control" rows="5" required
                      placeholder="Document what was discussed in this session…"><?= htmlspecialchars($notes['therapist_notes'] ?? '') ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-600">Techniques Used</label>
            <input type="text" name="techniques_used" class="form-control"
                   value="<?= htmlspecialchars($notes['techniques_used'] ?? '') ?>"
                   placeholder="e.g. CBT, Mindfulness, EMDR">
          </div>

          <div class="mb-3">
            <label class="form-label fw-600">Homework / Assignments</label>
            <textarea name="homework" class="form-control" rows="2"
                      placeholder="Tasks assigned to the patient for next session…"><?= htmlspecialchars($notes['homework'] ?? '') ?></textarea>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-600">Session Outcome</label>
              <select name="outcome" class="form-select">
                <option value="">— Select —</option>
                <?php foreach (['improved','stable','needs_follow_up','crisis_intervention','no_show'] as $o): ?>
                  <option value="<?= $o ?>" <?= ($notes['outcome']??'') === $o ? 'selected':'' ?>>
                    <?= ucfirst(str_replace('_',' ',$o)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Next Session Date (optional)</label>
              <input type="date" name="follow_up_date" class="form-control"
                     value="<?= htmlspecialchars($notes['follow_up_date'] ?? '') ?>">
            </div>
          </div>

          <div class="alert alert-warning mb-4">
            <i class="bi bi-lock-fill me-2"></i>
            <strong>Confidential:</strong> These notes are protected health information and visible only to you.
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-save me-2"></i>Save Notes & Complete Session
            </button>
            <a href="<?= $baseUrl ?>/appointments" class="btn btn-outline-primary btn-lg">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
