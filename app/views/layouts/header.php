<?php
/**
 * MindBridge — Master Header / Layout
 * Loads CSS, sets <body> class, renders navbar with notifications.
 */
if (!defined('BASE_PATH')) die('Direct access not permitted.');

$baseUrl   = BASE_URL;
$isLoggedIn= Session::isLoggedIn();
$role      = Session::role() ?? '';
$firstName = Session::get('first_name','');
$pageTitle = $pageTitle ?? 'MindBridge';
$bodyClass = $bodyClass ?? ($isLoggedIn ? 'app-page' : 'public-page');

// Unread notification count
$notifCount = 0;
if ($isLoggedIn) {
    try {
        $db = Database::getInstance();
        $row = $db->fetchOne("SELECT COUNT(*) AS c FROM notifications WHERE user_id=? AND is_read=0", [Session::userId()]);
        $notifCount = (int)($row['c'] ?? 0);
    } catch (Exception $e) {}
}

// Flash messages — read all three before consuming any
$_flashSuccess = Session::getFlash('success');
$_flashError   = Session::getFlash('error');
$_flashInfo    = Session::getFlash('info');
if ($_flashSuccess) {
    $flash = $_flashSuccess;
    $flashType = 'success';
} elseif ($_flashError) {
    $flash = $_flashError;
    $flashType = 'danger';
} elseif ($_flashInfo) {
    $flash = $_flashInfo;
    $flashType = 'info';
} else {
    $flash = null;
    $flashType = 'info';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="MindBridge — A holistic mental health and wellness portal connecting patients with licensed therapists.">
  <meta name="theme-color" content="#4A90E2">
  <title><?= htmlspecialchars($pageTitle) ?> — MindBridge</title>

  <!-- Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- MindBridge CSS -->
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= $baseUrl ?>/assets/favicon.svg">
</head>
<body class="<?= $bodyClass ?>">

<!-- Flash -->
<?php if ($flash): ?>
  <div class="flash-message flash-<?= $flashType ?>" id="flashMsg">
    <i class="bi bi-<?= $flashType==='success'?'check-circle-fill':($flashType==='danger'?'exclamation-triangle-fill':'info-circle-fill') ?> me-2"></i>
    <?= htmlspecialchars($flash) ?>
    <button class="flash-close" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
  </div>
<?php endif; ?>

<?php if ($isLoggedIn): ?>
<!-- ─── Authenticated Navbar ─── -->
<nav class="app-navbar">
  <!-- Hamburger (mobile) -->
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
  </button>

  <!-- Brand -->
  <a href="<?= $baseUrl ?>/dashboard" class="navbar-brand-link">
    <i class="bi bi-heart-pulse-fill" style="color:var(--primary);"></i>
    <span class="navbar-brand-text">Mind<strong>Bridge</strong></span>
  </a>

  <!-- Navigation Links -->
  <div class="d-none d-lg-flex ms-4 gap-3">
    <a href="<?= $baseUrl ?>/dashboard" class="nav-link text-white">Dashboard</a>
    <a href="<?= $baseUrl ?>/forum" class="nav-link text-white">Community</a>
    <a href="<?= $baseUrl ?>/appointments" class="nav-link text-white">Appointments</a>
    <a href="<?= $baseUrl ?>/sessions" class="nav-link text-white">Sessions</a>
    <a href="<?= $baseUrl ?>/messages" class="nav-link text-white">Messages</a>
    <?php if ($role === 'admin'): ?>
      <a href="<?= $baseUrl ?>/admin/dashboard" class="nav-link text-white fw-bold">Admin</a>
    <?php endif; ?>
  </div>

  <div class="ms-auto d-flex align-items-center gap-3">
    <!-- Search (placeholder) -->
    <div class="d-none d-md-block">
      <div class="input-group input-group-sm" style="width:220px;">
        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
          <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" class="form-control" style="background:var(--bg);border-color:var(--border);"
               placeholder="Search…" aria-label="Search">
      </div>
    </div>

    <!-- Notifications bell -->
    <div class="dropdown">
      <button class="btn position-relative p-1" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false"
              style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);width:38px;height:38px;">
        <i class="bi bi-bell" style="font-size:1.1rem;color:var(--text-muted);"></i>
        <span class="notification-badge" id="notifBadge" <?= $notifCount === 0 ? 'style="display:none"' : '' ?>>
          <?= $notifCount ?>
        </span>
      </button>
      <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" style="width:320px;border-radius:var(--radius);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <span class="fw-700">Notifications</span>
          <a href="<?= $baseUrl ?>/notifications" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div id="notifList" style="max-height:300px;overflow-y:auto;">
          <p class="text-center text-muted py-4 small">Loading…</p>
        </div>
      </div>
    </div>

    <!-- User avatar -->
    <div class="dropdown">
      <button class="btn p-0 border-0" data-bs-toggle="dropdown">
        <div class="avatar" style="width:38px;height:38px;font-size:.9rem;">
          <?= strtoupper(substr($firstName,0,1)) ?>
        </div>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" style="border-radius:var(--radius);">
        <li class="px-3 py-2 border-bottom">
          <div class="fw-600 small"><?= htmlspecialchars($firstName) ?></div>
          <div class="text-muted" style="font-size:.75rem;"><?= ucfirst($role) ?></div>
        </li>
        <li><a class="dropdown-item" href="<?= $baseUrl ?>/<?= $role ?>/profile"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><a class="dropdown-item" href="<?= $baseUrl ?>/notifications"><i class="bi bi-bell me-2"></i>Notifications</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= $baseUrl ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php else: ?>
<!-- ─── Public Navbar ─── -->
<nav class="public-nav" id="publicNav">
  <div class="container d-flex align-items-center justify-content-between py-3">
    <a href="<?= $baseUrl ?>/" class="navbar-brand-link">
      <i class="bi bi-heart-pulse-fill" style="color:var(--primary);font-size:1.5rem;"></i>
      <span class="navbar-brand-text">Mind<strong>Bridge</strong></span>
    </a>
    <div class="d-flex gap-3 align-items-center">
      <a href="<?= $baseUrl ?>/forum" class="btn btn-outline-light btn-sm">Community</a>
      <a href="<?= $baseUrl ?>/auth/login" class="btn btn-outline-primary btn-sm">Sign In</a>
      <a href="<?= $baseUrl ?>/auth/register" class="btn btn-primary btn-sm">Get Started</a>
    </div>
  </div>
</nav>
<?php endif; ?>

<!-- Crisis Banner (shown by JS when keywords detected) -->
<div id="crisisBanner" style="display:none;" class="crisis-banner">
  <div class="d-flex align-items-center gap-3">
    <i class="bi bi-heart-pulse-fill" style="font-size:1.5rem;"></i>
    <div>
      <strong>You're not alone.</strong> If you're in crisis, please reach out immediately.
      <a href="tel:988" class="text-white fw-700 ms-2">Call/Text 988</a>
      <span class="mx-2">·</span>
      <a href="sms:741741" class="text-white fw-700">Text HOME to 741741</a>
    </div>
    <button class="btn btn-sm ms-auto" style="color:#fff;" onclick="document.getElementById('crisisBanner').style.display='none'">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
</div>

<div class="<?= $isLoggedIn ? 'app-layout' : '' ?>">
