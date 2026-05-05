<?php
/**
 * Layout: Sidebar
 * Role-aware navigation sidebar.
 */
$baseUrl  = BASE_URL;
$role     = Session::role() ?? '';
$current  = $_GET['url'] ?? '';
$userId   = Session::userId();

// Count unread messages
$unreadMessages = 0;
try {
    $db = Database::getInstance();
    $row = $db->fetchOne(
        "SELECT COUNT(*) AS cnt FROM messages WHERE receiver_id = ? AND is_read = 0",
        [$userId]
    );
    $unreadMessages = (int)($row['cnt'] ?? 0);
} catch (Exception $e) {}

function sidebarLink(string $href, string $icon, string $label, string $current, int $badge = 0): string {
    $active = (str_contains($current, ltrim(parse_url($href, PHP_URL_PATH), '/'))) ? 'active' : '';
    $badgeHtml = $badge > 0 ? "<span class=\"badge-count\">$badge</span>" : '';
    return "<a href=\"$href\" class=\"sidebar-link $active\"><i class=\"bi bi-$icon\"></i>$label$badgeHtml</a>";
}
?>
<div class="sidebar" id="sidebar">
  <!-- User info -->
  <div class="sidebar-brand">
    <h6>Signed in as</h6>
    <p><?= htmlspecialchars(Session::get('first_name','') . ' ' . Session::get('last_name','')) ?></p>
    <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:.7rem;">
      <?= ucfirst($role) ?>
    </span>
  </div>

  <!-- Navigation -->
  <?php if ($role === 'patient'): ?>
    <div class="sidebar-section-label">Overview</div>
    <?= sidebarLink("$baseUrl/dashboard",     'speedometer2',      'Dashboard',       $current) ?>
    <?= sidebarLink("$baseUrl/notifications", 'bell',              'Notifications',   $current) ?>
    <?= sidebarLink("$baseUrl/messages",      'chat-dots',         'Messages',        $current, $unreadMessages) ?>

    <div class="sidebar-section-label">My Care</div>
    <?= sidebarLink("$baseUrl/patient/profile",  'person-circle',  'My Profile',      $current) ?>
    <?= sidebarLink("$baseUrl/patient/intake",   'clipboard-heart', 'Intake Form',    $current) ?>
    <?= sidebarLink("$baseUrl/patient/matching", 'people',         'Find Therapist',  $current) ?>
    <?= sidebarLink("$baseUrl/appointments",     'calendar-check', 'Appointments',    $current) ?>
    <?= sidebarLink("$baseUrl/sessions",         'camera-video',   'My Sessions',     $current) ?>
    <?= sidebarLink("$baseUrl/payments",         'credit-card',    'Payments',        $current) ?>

    <div class="sidebar-section-label">Wellness</div>
    <?= sidebarLink("$baseUrl/wellness/mood",      'emoji-smile',   'Mood Tracker',   $current) ?>
    <?= sidebarLink("$baseUrl/wellness/journal",   'journal-text',  'My Journal',     $current) ?>
    <?= sidebarLink("$baseUrl/wellness/goals",     'trophy',        'Goals',          $current) ?>
    <?= sidebarLink("$baseUrl/wellness/resources", 'book-heart',    'Resources',      $current) ?>

    <div class="sidebar-section-label">Community</div>
    <?= sidebarLink("$baseUrl/forum", 'people-fill', 'Community Forum', $current) ?>

  <?php elseif ($role === 'therapist'): ?>
    <div class="sidebar-section-label">Overview</div>
    <?= sidebarLink("$baseUrl/dashboard",     'speedometer2',   'Dashboard',        $current) ?>
    <?= sidebarLink("$baseUrl/notifications", 'bell',           'Notifications',    $current) ?>
    <?= sidebarLink("$baseUrl/messages",      'chat-dots',      'Messages',         $current, $unreadMessages) ?>

    <div class="sidebar-section-label">Practice</div>
    <?= sidebarLink("$baseUrl/therapist/profile",      'person-badge',  'My Profile',     $current) ?>
    <?= sidebarLink("$baseUrl/therapist/availability", 'clock',         'Availability',   $current) ?>
    <?= sidebarLink("$baseUrl/therapist/patients",     'people',        'My Patients',    $current) ?>
    <?= sidebarLink("$baseUrl/appointments",           'calendar-check','Appointments',   $current) ?>
    <?= sidebarLink("$baseUrl/sessions",               'camera-video',  'Sessions',       $current) ?>
    <?= sidebarLink("$baseUrl/feedback",               'star',          'Reviews',        $current) ?>

    <div class="sidebar-section-label">Community</div>
    <?= sidebarLink("$baseUrl/forum",                  'people-fill',   'Community Forum',$current) ?>
    <?= sidebarLink("$baseUrl/wellness/resources",     'book-heart',    'Resources',      $current) ?>

  <?php elseif ($role === 'admin'): ?>
    <div class="sidebar-section-label">Overview</div>
    <?= sidebarLink("$baseUrl/dashboard",      'speedometer2',    'Dashboard',        $current) ?>
    <?= sidebarLink("$baseUrl/notifications",  'bell',            'Notifications',    $current) ?>

    <div class="sidebar-section-label">Management</div>
    <?= sidebarLink("$baseUrl/admin/users",    'people',          'Users',            $current) ?>
    <?= sidebarLink("$baseUrl/admin/reports",  'flag',            'Reports',          $current) ?>
    <?= sidebarLink("$baseUrl/admin/disputes", 'exclamation-circle','Disputes',       $current) ?>
    <?= sidebarLink("$baseUrl/crisis",         'heart-pulse',     'Crisis Alerts',    $current) ?>

    <div class="sidebar-section-label">Content</div>
    <?= sidebarLink("$baseUrl/admin/resources",   'book-heart',   'Resources',        $current) ?>
    <?= sidebarLink("$baseUrl/admin/moderation",  'shield-check', 'Moderation',       $current) ?>
    <?= sidebarLink("$baseUrl/forum",             'people-fill',  'Community Forum',  $current) ?>

    <div class="sidebar-section-label">Analytics</div>
    <?= sidebarLink("$baseUrl/admin/analytics",   'bar-chart',    'Analytics',        $current) ?>
    <?= sidebarLink("$baseUrl/admin/auditLogs",   'clock-history','Audit Logs',       $current) ?>
  <?php endif; ?>

  <!-- Logout -->
  <div class="mt-auto pt-3 border-top" style="margin-top:2rem!important;">
    <a href="<?= $baseUrl ?>/auth/logout" class="sidebar-link text-danger">
      <i class="bi bi-box-arrow-right" style="color:#e74c3c;"></i>
      Logout
    </a>
  </div>
</div>
