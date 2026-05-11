<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-file-earmark-pdf me-2"></i>Generate Patient Report</h1>
    <p>Create a comprehensive PDF report for patient progress tracking</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card fade-in-up">
        <div class="card-header">
          <h5 class="mb-0">Report Configuration</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= $baseUrl ?>/therapist/createReport">
            <input type="hidden" name="patient_id" value="<?= $patientId ?>">

            <!-- Report Period -->
            <div class="mb-4">
              <label class="form-label fw-600">Report Period</label>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small">Start Date</label>
                  <input type="date" name="start_date" class="form-control"
                         value="<?= date('Y-m-d', strtotime('-6 months')) ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small">End Date</label>
                  <input type="date" name="end_date" class="form-control"
                         value="<?= date('Y-m-d') ?>" required>
                </div>
              </div>
            </div>

            <!-- Include Sections -->
            <div class="mb-4">
              <label class="form-label fw-600">Include in Report</label>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_sessions" id="include_sessions" checked>
                    <label class="form-check-label" for="include_sessions">
                      Session History
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_mood" id="include_mood" checked>
                    <label class="form-check-label" for="include_mood">
                      Mood Tracking
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_goals" id="include_goals" checked>
                    <label class="form-check-label" for="include_goals">
                      Goals Progress
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Therapist Summary -->
            <div class="mb-4">
              <label class="form-label fw-600">Therapist Summary <small class="text-muted">(optional)</small></label>
              <textarea name="summary" class="form-control" rows="4"
                        placeholder="Add your professional assessment, observations, or recommendations for this patient's progress..."></textarea>
              <div class="form-text">
                This summary will be included at the end of the report for documentation purposes.
              </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF Report
              </button>
              <a href="<?= $baseUrl ?>/therapist/patient/<?= $patientId ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Patient
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Report Preview Info -->
      <div class="card fade-in-up">
        <div class="card-header">
          <h6 class="mb-0">Report Contents</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <h6><i class="bi bi-person-lines-fill text-primary me-2"></i>Patient Information</h6>
              <ul class="small text-muted mb-0">
                <li>Basic demographics</li>
                <li>Contact information</li>
                <li>Initial assessment data</li>
              </ul>
            </div>
            <div class="col-md-6">
              <h6><i class="bi bi-graph-up text-success me-2"></i>Progress Tracking</h6>
              <ul class="small text-muted mb-0">
                <li>Session history & notes</li>
                <li>Mood tracking trends</li>
                <li>Goals progress</li>
                <li>Therapist observations</li>
              </ul>
            </div>
          </div>

          <hr>

          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Confidential:</strong> This report contains sensitive patient information and should only be used for therapeutic purposes.
            The PDF will be downloaded directly to your device.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>