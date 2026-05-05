<?php
$pageTitle = $pageTitle ?? 'Sign In';
$bodyClass = 'auth-page';
$errors    = $errors ?? [];
$email     = $email ?? '';
$baseUrl   = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
?>
<!-- Back to home page -->
<!-- استدعاء مكتبة الأيقونات (لو مش عندك) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    /* حاوية الزرار - هنخليها فوق على الشمال */
    .nav-header-container {
        position: relative;
        padding: 20px;
        display: flex;
        justify-content: flex-start; /* بيخلي الزرار يروح أقصى اليسار */
    }

    .back-btn-premium {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 10px 18px;
        background: rgba(255, 255, 255, 0.8); /* تأثير شفاف */
        backdrop-filter: blur(10px); /* تغبيش الخلفية */
        color: #2d3436;
        text-decoration: none;
        border-radius: 15px;
        font-weight: 700;
        font-size: 0.95rem;
        border: 1px solid rgba(0, 123, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* تصميم الأيقونة الدائرية */
    .icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #007bff, #00d2ff);
        color: white;
        border-radius: 10px;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    /* تأثيرات الحركة */
    .back-btn-premium:hover {
        background: #007bff;
        color: white !important;
        transform: translateX(5px) scale(1.02);
        box-shadow: 0 10px 20px rgba(0, 123, 255, 0.2);
    }

    .back-btn-premium:hover .icon-box {
        background: white;
        color: #007bff;
        transform: rotate(-15deg);
    }

    /* إخفاء الخط تحت اللينك */
    .back-btn-premium:hover {
        text-decoration: none;
    }
</style>

<div class="nav-header-container">
    <a href="<?= $baseUrl ?>" class="back-btn-premium">
        <div class="icon-box">
            <i class="bi bi-chevron-left"></i>
        </div>
        <span>Back to MindBridge</span>
    </a>
</div>

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
      <p class="fw-600 mb-2 text-muted"><i class="bi bi-info-circle me-1"></i>Demo Accounts — click to fill</p>
      <div class="row g-2">
        <div class="col-4 text-center">
          <a href="#" class="demo-fill d-block p-2 rounded text-decoration-none"
             style="background:#dbeafe;color:#1d4ed8;"
             data-email="admin@mindbridge.com"
             data-password="Admin123@">
            <i class="bi bi-shield-fill-check d-block mb-1 fs-5"></i>
            <div class="fw-700">Admin</div>
            <div style="font-size:.68rem;opacity:.8;">Admin123@</div>
          </a>
        </div>
        <div class="col-4 text-center">
          <a href="#" class="demo-fill d-block p-2 rounded text-decoration-none"
             style="background:#d1fae5;color:#065f46;"
             data-email="dr.sarah@mindbridge.com"
             data-password="password">
            <i class="bi bi-person-badge-fill d-block mb-1 fs-5"></i>
            <div class="fw-700">Therapist</div>
            <div style="font-size:.68rem;opacity:.8;">password</div>
          </a>
        </div>
        <div class="col-4 text-center">
          <a href="#" class="demo-fill d-block p-2 rounded text-decoration-none"
             style="background:#ede9fe;color:#4c1d95;"
             data-email="patient1@example.com"
             data-password="password">
            <i class="bi bi-person-fill d-block mb-1 fs-5"></i>
            <div class="fw-700">Patient</div>
            <div style="font-size:.68rem;opacity:.8;">password</div>
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

// Demo fill — each card carries its own data-password
document.querySelectorAll('.demo-fill').forEach(el => {
  el.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('email').value    = el.dataset.email;
    document.getElementById('password').value = el.dataset.password || 'password';
  });
});
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
