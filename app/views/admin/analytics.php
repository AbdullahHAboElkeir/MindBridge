<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-bar-chart me-2"></i>Analytics</h1>
    <p>Platform-wide statistics and trends</p>
  </div>

  <div class="row g-4">
    <!-- Sessions chart -->
    <div class="col-lg-6">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-graph-up text-primary me-2"></i>Sessions per Month</div>
        <div class="card-body">
          <canvas id="adminSessChart" height="220"></canvas>
        </div>
      </div>
    </div>
    <!-- Registrations chart -->
    <div class="col-lg-6">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-people text-primary me-2"></i>New Registrations per Month</div>
        <div class="card-body">
          <canvas id="adminRegChart" height="220"></canvas>
        </div>
      </div>
    </div>
    <!-- Revenue chart -->
    <div class="col-lg-6">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-currency-dollar text-primary me-2"></i>Revenue per Month ($)</div>
        <div class="card-body">
          <canvas id="adminRevChart" height="220"></canvas>
        </div>
      </div>
    </div>
    <!-- Mood distribution -->
    <div class="col-lg-6">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-emoji-smile text-primary me-2"></i>Mood Distribution</div>
        <div class="card-body">
          <canvas id="adminMoodChart" height="220"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Use unique IDs (prefixed 'admin') to avoid conflicts with footer global charts
const _colors = {
  primary: '#4A90E2',
  secondary: '#2AC0B5',
  accent: '#7B6CF6',
  warning: '#F5A623',
};
function _mkChart(id, type, labels, data, color, label) {
  const ctx = document.getElementById(id);
  if (!ctx || typeof Chart === 'undefined') return;
  new Chart(ctx, {
    type,
    data: {
      labels,
      datasets: [{ label, data, backgroundColor: Array.isArray(color) ? color : color + '33',
                   borderColor: Array.isArray(color) ? color : color, borderWidth: 2,
                   fill: type === 'line', tension: .4 }]
    },
    options: { responsive: true, plugins: { legend: { display: type === 'doughnut' } },
               scales: type !== 'doughnut' ? { y: { beginAtZero: true } } : {} }
  });
}
<?php
$sessLabels = json_encode(array_column($sessMonthly, 'ym'));
$sessData   = json_encode(array_column($sessMonthly, 'cnt'));
$regLabels  = json_encode(array_column($regMonthly,  'ym'));
$regData    = json_encode(array_column($regMonthly,  'cnt'));
$revLabels  = json_encode(array_column($revMonthly,  'ym'));
$revData    = json_encode(array_column($revMonthly,  'total'));
$moodLabels = json_encode(array_column($moodDist,    'band'));
$moodData   = json_encode(array_column($moodDist,    'cnt'));
?>
_mkChart('adminSessChart','line', <?= $sessLabels ?>, <?= $sessData ?>, _colors.primary,  'Sessions');
_mkChart('adminRegChart', 'bar',  <?= $regLabels  ?>, <?= $regData  ?>, _colors.secondary,'Users');
_mkChart('adminRevChart', 'bar',  <?= $revLabels  ?>, <?= $revData  ?>, _colors.accent,   'Revenue ($)');
_mkChart('adminMoodChart','doughnut', <?= $moodLabels ?>, <?= $moodData ?>,
  ['#4A90E2','#F5A623','#2AC0B5'], 'Mood');
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
