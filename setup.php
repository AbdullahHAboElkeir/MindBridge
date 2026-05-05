<?php
/**
 * MindBridge — Setup & Debug Helper
 * Access at: http://localhost/MindBridge/MindBridge/setup.php
 *
 * This script:
 *  1. Tests DB connection
 *  2. Resets the admin password to Admin123@
 *  3. Verifies critical tables exist
 *  4. Shows system diagnostics
 *
 * DELETE THIS FILE after setup is complete.
 */

// ── Bootstrap ──────────────────────────────────────────────────
define('BASE_PATH', __DIR__);
define('APP_NAME',   'MindBridge');
define('DB_HOST',    'localhost');
define('DB_NAME',    'mindbridge');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');
define('SESSION_LIFETIME', 3600);
define('BASE_URL',   'http://localhost/MindBridge/MindBridge');

$action = $_GET['action'] ?? 'check';
$results = [];

// ── DB Connect ─────────────────────────────────────────────────
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $results[] = ['ok' => true,  'msg' => '✅ Database connection OK — mindbridge DB reachable'];
} catch (PDOException $e) {
    $results[] = ['ok' => false, 'msg' => '❌ Database connection FAILED: ' . $e->getMessage()];
    $pdo = null;
}

if ($pdo) {
    // ── Reset admin password ──────────────────────────────────
    if ($action === 'reset_admin') {
        $hash = password_hash('Admin123@', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password=?, status='active' WHERE email='admin@mindbridge.com'");
        $stmt->execute([$hash]);
        if ($stmt->rowCount()) {
            $results[] = ['ok' => true, 'msg' => '✅ Admin password reset to: <strong>Admin123@</strong>'];
        } else {
            // Admin doesn't exist yet — insert
            $hash2 = password_hash('Admin123@', PASSWORD_DEFAULT);
            $pdo->prepare(
                "INSERT IGNORE INTO users (email,password,name,first_name,last_name,role,status,email_verified,timezone)
                 VALUES ('admin@mindbridge.com',?,'System Admin','System','Admin','admin','active',1,'UTC')"
            )->execute([$hash2]);
            $results[] = ['ok' => true, 'msg' => '✅ Admin account created with password: <strong>Admin123@</strong>'];
        }
    }

    // ── Verify admin can authenticate ─────────────────────────
    $admin = $pdo->prepare("SELECT id,email,password,role,status FROM users WHERE email='admin@mindbridge.com'");
    $admin->execute();
    $adminRow = $admin->fetch();
    if ($adminRow) {
        $pwOk = password_verify('Admin123@', $adminRow['password']);
        $results[] = [
            'ok'  => $pwOk,
            'msg' => $pwOk
                ? '✅ Admin password "Admin123@" verifies correctly (role: '.$adminRow['role'].', status: '.$adminRow['status'].')'
                : '❌ Admin password does NOT verify. <a href="?action=reset_admin" class="btn btn-warning btn-sm">Click to Reset Password</a>'
        ];
    } else {
        $results[] = [
            'ok'  => false,
            'msg' => '❌ Admin account not found. <a href="?action=reset_admin" class="btn btn-warning btn-sm">Create Admin Account</a>'
        ];
    }

    // ── Check required tables ─────────────────────────────────
    $required = [
        'users','patients','therapists','therapist_availability',
        'appointments','sessions','payments','messages','notifications',
        'forum_posts','forum_comments','reports','crisis_alerts',
        'audit_logs','journals','mood_entries','wellness_goals',
        'wellness_resources','feedback','disputes','intake_forms',
        'consent_forms','waitlists','therapist_matches',
    ];
    foreach ($required as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = (bool)$stmt->fetch();
        $results[] = [
            'ok'  => $exists,
            'msg' => ($exists ? '✅' : '❌') . " Table <code>$table</code>" . ($exists ? ' exists' : ' MISSING — run mindbridge_schema.sql')
        ];
    }

    // ── Verify user counts ────────────────────────────────────
    $counts = $pdo->query("SELECT role, COUNT(*) AS c FROM users GROUP BY role")->fetchAll();
    foreach ($counts as $row) {
        $results[] = ['ok' => true, 'msg' => "📊 {$row['role']} accounts: {$row['c']}"];
    }

    // ── Route alias check (simulates resolveMethod) ───────────
    $testCases = [
        'doLogin'       => 'doLogin',
        'do-login'      => 'doLogin',
        'auditLogs'     => 'auditLogs',
        'audit-logs'    => 'auditLogs',
        'manageUser'    => 'manageUser',
        'manage-user'   => 'manageUser',
        'resolveReport' => 'resolveReport',
    ];
    foreach ($testCases as $input => $expected) {
        $resolved = resolveMethod($input);
        $ok = $resolved === $expected;
        $results[] = [
            'ok'  => $ok,
            'msg' => ($ok ? '✅' : '❌') . " resolveMethod('<code>$input</code>') → <code>$resolved</code>" . ($ok ? '' : " (expected <code>$expected</code>)")
        ];
    }
}

function resolveMethod(string $segment): string {
    if (!str_contains($segment, '-') && !str_contains($segment, '_')) {
        return $segment;
    }
    $segment = str_replace(['-', '_'], ' ', strtolower($segment));
    return lcfirst(str_replace(' ', '', ucwords($segment)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MindBridge Setup</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:800px">
  <div class="d-flex align-items-center gap-3 mb-4">
    <div style="background:#4A90E2;color:#fff;border-radius:12px;padding:12px 18px;font-size:1.5rem;">💜</div>
    <div>
      <h2 class="mb-0 fw-bold">MindBridge Setup &amp; Diagnostics</h2>
      <p class="text-muted mb-0">System health check and admin recovery tool</p>
    </div>
  </div>

  <?php foreach ($results as $r): ?>
    <div class="alert <?= $r['ok'] ? 'alert-success' : 'alert-danger' ?> py-2 mb-2">
      <?= $r['msg'] ?>
    </div>
  <?php endforeach; ?>

  <hr>
  <div class="row g-3 mt-2">
    <div class="col-auto">
      <a href="?action=reset_admin" class="btn btn-warning">
        🔑 Reset Admin Password → Admin123@
      </a>
    </div>
    <div class="col-auto">
      <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary">
        🔐 Go to Login
      </a>
    </div>
    <div class="col-auto">
      <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-primary">
        🏠 Go to Dashboard
      </a>
    </div>
  </div>

  <div class="alert alert-warning mt-4">
    <strong>⚠️ Security Notice:</strong> Delete or rename <code>setup.php</code> after completing setup.
  </div>

  <div class="card mt-4">
    <div class="card-header fw-bold">🔑 Demo Credentials</div>
    <div class="card-body">
      <table class="table table-sm mb-0">
        <thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
        <tbody>
          <tr><td><span class="badge bg-primary">Admin</span></td><td>admin@mindbridge.com</td><td><code>Admin123@</code></td></tr>
          <tr><td><span class="badge bg-success">Therapist</span></td><td>dr.sarah@mindbridge.com</td><td><code>password</code></td></tr>
          <tr><td><span class="badge bg-secondary">Patient</span></td><td>patient1@example.com</td><td><code>password</code></td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card mt-3">
    <div class="card-header fw-bold">📋 Quick Setup Steps</div>
    <div class="card-body">
      <ol class="mb-0">
        <li>Import <code>database/mindbridge_schema.sql</code> in phpMyAdmin</li>
        <li>Import <code>database/mindbridge_seed.sql</code> in phpMyAdmin</li>
        <li>Visit this page and click <strong>Reset Admin Password</strong> if needed</li>
        <li>Log in at <a href="<?= BASE_URL ?>/auth/login"><?= BASE_URL ?>/auth/login</a></li>
        <li>Delete <code>setup.php</code></li>
      </ol>
    </div>
  </div>
</div>
</body>
</html>
