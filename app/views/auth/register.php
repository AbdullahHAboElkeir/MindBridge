<?php
$pageTitle = $pageTitle ?? 'Create Account';
$bodyClass = 'auth-page';
$errors    = $errors ?? [];
$data      = $data ?? [];
$baseUrl   = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="auth-wrapper" style="align-items:flex-start;padding:2rem;">
  <div class="auth-card" style="max-width:560px;margin:auto;">

    <div class="auth-logo">
      <div class="logo-icon"><i class="bi bi-person-plus-fill"></i></div>
      <h2>Create Account</h2>
      <p class="auth-subtitle">Join MindBridge — your mental wellness journey starts here</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/auth/doRegister" method="POST" class="needs-validation" novalidate id="regForm">

      <!-- Role Selection -->
      <div class="mb-4">
        <label class="form-label">I am registering as</label>
        <div class="row g-2">
          <div class="col-6">
            <input type="radio" class="btn-check" name="role" id="rolePatient" value="patient"
                   <?= ($data['role'] ?? 'patient') === 'patient' ? 'checked' : '' ?>>
            <label class="btn btn-outline-primary w-100" for="rolePatient">
              <i class="bi bi-person-fill me-2"></i>Patient
            </label>
          </div>
          <div class="col-6">
            <input type="radio" class="btn-check" name="role" id="roleTherapist" value="therapist"
                   <?= ($data['role'] ?? '') === 'therapist' ? 'checked' : '' ?>>
            <label class="btn btn-outline-secondary w-100" for="roleTherapist">
              <i class="bi bi-person-badge-fill me-2"></i>Therapist
            </label>
          </div>
        </div>
      </div>

      <!-- Basic Info -->
      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label" for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" class="form-control"
                 value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required placeholder="Jane">
        </div>
        <div class="col-6">
          <label class="form-label" for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" class="form-control"
                 value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required placeholder="Smith">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="regEmail">Email Address</label>
        <input type="email" id="regEmail" name="email" class="form-control"
               value="<?= htmlspecialchars($data['email'] ?? '') ?>" required placeholder="you@example.com">
      </div>

      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label" for="regPwd">Password</label>
          <input type="password" id="regPwd" name="password" class="form-control"
                 required minlength="8" placeholder="Min 8 characters">
        </div>
        <div class="col-6">
          <label class="form-label" for="regPwdC">Confirm Password</label>
          <input type="password" id="regPwdC" name="password_confirm" class="form-control"
                 required placeholder="Repeat password">
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label" for="gender">Gender</label>
          <select id="gender" name="gender" class="form-select">
            <option value="">Prefer not to say</option>
            <option value="male"       <?= ($data['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female"     <?= ($data['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
            <option value="non_binary" <?= ($data['gender'] ?? '') === 'non_binary' ? 'selected' : '' ?>>Non-binary</option>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label" for="phone">Phone (optional)</label>
          <input type="tel" id="phone" name="phone" class="form-control"
                 value="<?= htmlspecialchars($data['phone'] ?? '') ?>" placeholder="+1-555-000-0000">
        </div>
      </div>

      <!-- Therapist-only fields -->
      <div id="therapistFields" style="display:none;">
        <hr class="my-3">
        <p class="text-primary fw-600 mb-3"><i class="bi bi-person-badge me-2"></i>Therapist Information</p>
        <div class="mb-3">
          <label class="form-label" for="license_number">License Number <span class="text-danger">*</span></label>
          <input type="text" id="license_number" name="license_number" class="form-control"
                 value="<?= htmlspecialchars($data['license_number'] ?? '') ?>" placeholder="e.g. LIC-2024-NY">
        </div>
        <div class="mb-3">
          <label class="form-label" for="specializations">Specializations</label>
          <input type="text" id="specializations" name="specializations" class="form-control"
                 value="<?= htmlspecialchars($data['specializations'] ?? '') ?>"
                 placeholder="e.g. Anxiety, Depression, Trauma">
          <div class="form-text">Comma-separated list</div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label" for="years_experience">Years Experience</label>
            <input type="number" id="years_experience" name="years_experience" class="form-control"
                   value="<?= htmlspecialchars($data['years_experience'] ?? '0') ?>" min="0" max="60">
          </div>
          <div class="col-6">
            <label class="form-label" for="session_rate">Session Rate (USD)</label>
            <input type="number" id="session_rate" name="session_rate" class="form-control"
                   value="<?= htmlspecialchars($data['session_rate'] ?? '0') ?>" min="0" step="0.01">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="bio">Short Bio</label>
          <textarea id="bio" name="bio" class="form-control" rows="3"
                    placeholder="Describe your approach and experience..."><?= htmlspecialchars($data['bio'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label" for="languages">Languages Spoken</label>
          <input type="text" id="languages" name="languages" class="form-control"
                 value="<?= htmlspecialchars($data['languages'] ?? 'English') ?>"
                 placeholder="e.g. English, Arabic, French">
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 btn-lg mt-3">
        <i class="bi bi-person-check-fill me-2"></i>Create My Account
      </button>
    </form>

    <hr class="my-4">
    <p class="text-center text-muted" style="font-size:.88rem;">
      Already have an account?
      <a href="<?= $baseUrl ?>/auth/login" class="fw-600">Sign in</a>
    </p>

  </div>
</div>

<script>
// Show/hide therapist fields
function toggleTherapistFields() {
  const isTherapist = document.getElementById('roleTherapist').checked;
  document.getElementById('therapistFields').style.display = isTherapist ? 'block' : 'none';
  document.getElementById('license_number').required = isTherapist;
}
document.querySelectorAll('[name="role"]').forEach(r => r.addEventListener('change', toggleTherapistFields));
toggleTherapistFields();

// Password match validation
document.getElementById('regPwdC').addEventListener('input', function() {
  this.setCustomValidity(
    this.value !== document.getElementById('regPwd').value ? 'Passwords do not match.' : ''
  );
});
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
