<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$step = (int)($patient['onboarding_step'] ?? 0);
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-person-circle me-2"></i>My Profile</h1>
    <p>Manage your personal information and account settings</p>
  </div>

  <!-- Onboarding Steps -->
  <div class="card mb-4 fade-in-up">
    <div class="card-body">
      <div class="steps-bar">
        <?php
        $steps = ['Registered','Intake Form','Consent','Matched','Complete'];
        for ($i = 0; $i <= 4; $i++):
          $cls = $i < $step ? 'done' : ($i === $step ? 'active' : '');
        ?>
        <div class="step-item <?= $cls ?>">
          <div class="step-circle"><?= $i < $step ? '<i class="bi bi-check-lg"></i>' : ($i+1) ?></div>
          <div class="step-label"><?= $steps[$i] ?></div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <form method="POST" action="<?= $baseUrl ?>/patient/updateProfile" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row g-4">

      <!-- Avatar & Basic -->
      <div class="col-lg-4">
        <div class="card fade-in-up">
          <div class="card-body text-center py-4">
            <?php
            $avatar = Session::get('avatar');
            if ($avatar && file_exists(UPLOAD_PATH . $avatar)):
            ?>
              <img src="<?= $baseUrl ?>/uploads/<?= htmlspecialchars($avatar) ?>"
                   class="avatar-xl mb-3 border border-3" style="border-color:var(--primary)!important;" alt="Avatar">
            <?php else: ?>
              <div class="avatar avatar-xl mx-auto mb-3" style="font-size:2rem;">
                <?= strtoupper(substr($patient['first_name'] ?? 'P', 0, 1)) ?>
              </div>
            <?php endif; ?>
            <h5 class="fw-700"><?= htmlspecialchars($patient['first_name'].' '.$patient['last_name']) ?></h5>
            <span class="badge-status active">Patient</span>
            <div class="mt-3">
              <label class="form-label d-block">Update Photo</label>
              <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
            </div>
          </div>
        </div>
      </div>

      <!-- Personal Info -->
      <div class="col-lg-8">
        <div class="card fade-in-up mb-4">
          <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Personal Information</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control" required
                       value="<?= htmlspecialchars($patient['first_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control" required
                       value="<?= htmlspecialchars($patient['last_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-control"
                       value="<?= htmlspecialchars($patient['phone'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control"
                       value="<?= htmlspecialchars($patient['date_of_birth'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                  <option value="">Prefer not to say</option>
                  <?php foreach (['male'=>'Male','female'=>'Female','non_binary'=>'Non-binary'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= ($patient['gender'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Preferred Language</label>
                <input type="text" name="preferred_language" class="form-control"
                       value="<?= htmlspecialchars($patient['preferred_language'] ?? 'English') ?>">
              </div>
            </div>
          </div>
        </div>

        <!-- Insurance & Emergency -->
        <div class="card fade-in-up mb-4">
          <div class="card-header"><i class="bi bi-shield-check me-2 text-primary"></i>Insurance & Emergency</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Insurance Provider</label>
                <input type="text" name="insurance_provider" class="form-control"
                       value="<?= htmlspecialchars($patient['insurance_provider'] ?? '') ?>" placeholder="e.g. BlueCross">
              </div>
              <div class="col-md-6">
                <label class="form-label">Insurance Number</label>
                <input type="text" name="insurance_number" class="form-control"
                       value="<?= htmlspecialchars($patient['insurance_number'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Emergency Contact Name</label>
                <input type="text" name="emergency_contact" class="form-control"
                       value="<?= htmlspecialchars($patient['emergency_contact'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Emergency Contact Phone</label>
                <input type="tel" name="emergency_phone" class="form-control"
                       value="<?= htmlspecialchars($patient['emergency_phone'] ?? '') ?>">
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
          <i class="bi bi-check-circle me-2"></i>Save Changes
        </button>
        <a href="<?= $baseUrl ?>/dashboard" class="btn btn-outline-primary btn-lg ms-2">Cancel</a>
      </div>
    </div>
  </form>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
