<?php
$pageTitle = $pageTitle ?? 'Sign In';
$bodyClass = 'auth-page';
$errors    = $errors ?? [];
$email     = $email ?? '';
$baseUrl   = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="auth-wrapper">
  <div class="auth-card">

    <div class="auth-logo">
      <div class="logo-icon"><i class="bi bi-heart-pulse-fill"></i></div>
      <div>
        <h2>Welcome Back</h2>
        <p class="auth-subtitle">Sign in to your MindBridge account</p>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/auth/doLogin" method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label" for="email">Email Address</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope text-muted"></i></span>
          <input type="email" id="email" name="email" class="form-control border-start-0"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($email) ?>" required autocomplete="email">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
          <input type="password" id="password" name="password" class="form-control border-start-0"
                 placeholder="••••••••" required autocomplete="current-password">
          <button type="button" class="input-group-text bg-white border-start-0" id="togglePwd" tabindex="-1">
            <i class="bi bi-eye text-muted" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 btn-lg mt-2">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
      </button>
    </form>

    <hr class="my-4">

    <p class="text-center text-muted mb-3" style="font-size:.88rem;">Don't have an account?</p>
    <a href="<?= $baseUrl ?>/auth/register" class="btn btn-outline-primary w-100">
      <i class="bi bi-person-plus me-2"></i>Create Account
    </a>

    <!-- Demo credentials info box -->
    <div class="mt-4 p-3 rounded" style="background:var(--bg);font-size:.8rem;">
      <p class="fw-600 mb-1 text-muted"><i class="bi bi-info-circle me-1"></i>Demo Credentials (password: <code>password</code>)</p>
      <div class="row g-1">
        <div class="col-4 text-center">
          <a href="#" class="demo-fill d-block p-1 rounded text-decoration-none"
             style="background:#dbeafe;color:#1d4ed8;"
             data-email="admin@mindbridge.com">
            <i class="bi bi-shield-fill-check d-block mb-1"></i>Admin
          </a>
        </div>
        <div class="col-4 text-center">
          <a href="#" class="demo-fill d-block p-1 rounded text-decoration-none"
             style="background:#d1fae5;color:#065f46;"
             data-email="dr.sarah@mindbridge.com">
            <i class="bi bi-person-badge-fill d-block mb-1"></i>Therapist
          </a>
        </div>
        <div class="col-4 text-center">
          <a href="#" class="demo-fill d-block p-1 rounded text-decoration-none"
             style="background:#ede9fe;color:#4c1d95;"
             data-email="patient1@example.com">
            <i class="bi bi-person-fill d-block mb-1"></i>Patient
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
// Password toggle
document.getElementById('togglePwd').addEventListener('click', function() {
  const pwd = document.getElementById('password');
  const eye = document.getElementById('eyeIcon');
  pwd.type = pwd.type === 'password' ? 'text' : 'password';
  eye.className = pwd.type === 'password' ? 'bi bi-eye text-muted' : 'bi bi-eye-slash text-muted';
});

// Demo fill
document.querySelectorAll('.demo-fill').forEach(el => {
  el.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('email').value = el.dataset.email;
    document.getElementById('password').value = 'password';
  });
});
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
