<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-person-badge me-2"></i>My Profile</h1>
    <p>Update your professional information and credentials</p>
  </div>

  <form method="POST" action="<?= $baseUrl ?>/therapist/updateProfile" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row g-4">
      <!-- Avatar -->
      <div class="col-lg-4">
        <div class="card fade-in-up text-center py-4">
          <?php $avatar = Session::get('avatar'); ?>
          <?php if ($avatar && file_exists(UPLOAD_PATH . $avatar)): ?>
            <img src="<?= $baseUrl ?>/uploads/<?= htmlspecialchars($avatar) ?>"
                 class="avatar-xl mx-auto border border-3 mb-3" style="border-color:var(--primary)!important;">
          <?php else: ?>
            <div class="avatar avatar-xl mx-auto mb-3" style="font-size:2rem;">
              <?= strtoupper(substr($therapist['first_name']??'T',0,1)) ?>
            </div>
          <?php endif; ?>
          <h5 class="fw-700">Dr. <?= htmlspecialchars(($therapist['first_name']??'').' '.($therapist['last_name']??'')) ?></h5>
          <div class="d-flex justify-content-center gap-2 mb-3">
            <span class="badge-status active">Therapist</span>
            <?php if ($therapist['license_verified']): ?>
              <span class="badge-status confirmed"><i class="bi bi-check-circle-fill me-1"></i>Verified</span>
            <?php endif; ?>
          </div>
          <div class="px-3">
            <label class="form-label">Update Photo</label>
            <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
          </div>
          <div class="mt-3 px-3">
            <div class="text-muted small"><i class="bi bi-star-fill text-warning me-1"></i><?= $therapist['rating'] ?> rating (<?= $therapist['total_reviews'] ?> reviews)</div>
            <div class="text-muted small"><i class="bi bi-people me-1"></i><?= $therapist['current_patients'] ?>/<?= $therapist['max_patients'] ?> patients</div>
          </div>
        </div>
      </div>

      <!-- Info -->
      <div class="col-lg-8">
        <div class="card fade-in-up mb-4">
          <div class="card-header"><i class="bi bi-person text-primary me-2"></i>Personal Information</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-600">First Name</label>
                <input type="text" name="first_name" class="form-control" required
                       value="<?= htmlspecialchars($therapist['first_name']??'') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600">Last Name</label>
                <input type="text" name="last_name" class="form-control" required
                       value="<?= htmlspecialchars($therapist['last_name']??'') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600">Phone</label>
                <input type="tel" name="phone" class="form-control"
                       value="<?= htmlspecialchars($therapist['phone']??'') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600">License Number</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($therapist['license_number']??'') ?>" readonly>
              </div>
            </div>
          </div>
        </div>

        <div class="card fade-in-up mb-4">
          <div class="card-header"><i class="bi bi-briefcase text-primary me-2"></i>Professional Information</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-600">Specializations</label>
                <input type="text" name="specializations" class="form-control"
                       value="<?= htmlspecialchars($therapist['specializations']??'') ?>"
                       placeholder="e.g. Anxiety, Depression, Trauma">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600">Languages</label>
                <input type="text" name="languages" class="form-control"
                       value="<?= htmlspecialchars($therapist['languages']??'English') ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-600">Years Experience</label>
                <input type="number" name="years_experience" class="form-control" min="0"
                       value="<?= $therapist['years_experience']??0 ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-600">Session Rate ($)</label>
                <input type="number" name="session_rate" class="form-control" min="0" step="0.01"
                       value="<?= $therapist['session_rate']??0 ?>">
              </div>
              <div class="col-12">
                <label class="form-label fw-600">Bio</label>
                <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($therapist['bio']??'') ?></textarea>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="accepts_insurance" id="acceptsIns" value="1"
                         <?= $therapist['accepts_insurance'] ? 'checked':'' ?>>
                  <label class="form-check-label" for="acceptsIns">Accept Insurance</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
          <i class="bi bi-check-circle me-2"></i>Save Changes
        </button>
      </div>
    </div>
  </form>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
