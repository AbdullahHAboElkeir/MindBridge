<?php
$baseUrl = BASE_URL;
$form = $form ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-clipboard-heart me-2"></i>Intake Form</h1>
    <p>Tell us about yourself so we can find you the best therapist match.</p>
  </div>

  <div class="card fade-in-up" style="max-width:780px;margin:0 auto;">
    <div class="card-header">
      <i class="bi bi-clipboard-heart text-primary me-2"></i>Mental Health Intake Assessment
    </div>
    <div class="card-body">
      <?php if (($form['status'] ?? '') === 'submitted'): ?>
        <div class="alert alert-success">
          <i class="bi bi-check-circle-fill me-2"></i>
          Your intake form has been submitted. You can update it below if needed.
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= $baseUrl ?>/patient/submitIntake" class="needs-validation" novalidate>
        <div class="mb-4">
          <label class="form-label fw-600">Primary Concerns <span class="text-danger">*</span></label>
          <textarea name="primary_concerns" class="form-control" rows="3" required
                    placeholder="What brings you to therapy? (e.g., anxiety, depression, stress, relationship issues)"
                    data-crisis-check><?= htmlspecialchars($form['primary_concerns'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label fw-600">Mental Health History</label>
          <textarea name="mental_health_history" class="form-control" rows="3"
                    placeholder="Any previous mental health diagnoses, hospitalizations, or treatments?"><?= htmlspecialchars($form['mental_health_history'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label fw-600">Current Medications</label>
          <textarea name="current_medications" class="form-control" rows="2"
                    placeholder="List any current medications or supplements (or 'None')"><?= htmlspecialchars($form['current_medications'] ?? '') ?></textarea>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-600">Previous Therapy Experience?</label>
            <select name="previous_therapy" class="form-select">
              <option value="0" <?= !($form['previous_therapy'] ?? 1) ? 'selected' : '' ?>>No, this is my first time</option>
              <option value="1" <?= ($form['previous_therapy'] ?? 0) ? 'selected' : '' ?>>Yes, I've been to therapy before</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Urgency Level</label>
            <select name="urgency_level" class="form-select">
              <?php foreach (['low'=>'Low — I can wait','medium'=>'Medium — Within 2 weeks','high'=>'High — Within a week','crisis'=>'Crisis — Immediate help needed'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($form['urgency_level'] ?? 'medium') === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Preferred Therapist Gender</label>
            <select name="therapist_gender_pref" class="form-select">
              <option value="no_preference" <?= ($form['therapist_gender_pref'] ?? '') === 'no_preference' ? 'selected' : '' ?>>No Preference</option>
              <option value="female" <?= ($form['therapist_gender_pref'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
              <option value="male" <?= ($form['therapist_gender_pref'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Preferred Session Format</label>
            <select name="session_format_pref" class="form-select">
              <option value="no_preference">No Preference</option>
              <?php foreach (['video'=>'Video Call','audio'=>'Audio Call','chat'=>'Text Chat'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($form['session_format_pref'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Preferred Language</label>
            <input type="text" name="preferred_language" class="form-control"
                   value="<?= htmlspecialchars($form['preferred_language'] ?? 'English') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Therapy Type Preference</label>
            <input type="text" name="therapy_type_pref" class="form-control"
                   placeholder="e.g. CBT, DBT, Mindfulness"
                   value="<?= htmlspecialchars($form['therapy_type_pref'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-600">Your Goals</label>
          <textarea name="goals" class="form-control" rows="3"
                    placeholder="What would you like to achieve through therapy?"><?= htmlspecialchars($form['goals'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label fw-600">Availability Notes</label>
          <textarea name="availability_notes" class="form-control" rows="2"
                    placeholder="When are you generally available? (e.g., weekday evenings, weekend mornings)"><?= htmlspecialchars($form['availability_notes'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" name="action" value="save" class="btn btn-outline-primary">
            <i class="bi bi-save me-2"></i>Save Draft
          </button>
          <button type="submit" name="action" value="submit" class="btn btn-primary">
            <i class="bi bi-send me-2"></i>Submit Form
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
