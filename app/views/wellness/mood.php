<?php
$baseUrl   = BASE_URL;
$todayMood = $todayMood ?? null;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$emojiMap = ['😞','😟','😕','😐','😶','🙂','😊','😄','😁','🤩'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-emoji-smile me-2"></i>Mood Tracker</h1>
    <p>Track your daily emotional wellbeing and spot patterns over time</p>
  </div>

  <div class="row g-4">

    <!-- Log Mood Card -->
    <div class="col-lg-5">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-plus-circle text-primary me-2"></i>Log Today's Mood</div>
        <div class="card-body">
          <?php if ($todayMood): ?>
            <div class="alert alert-success mb-3">
              <i class="bi bi-check-circle-fill me-2"></i>
              You already logged today! Feel free to update it.
            </div>
          <?php endif; ?>

          <form id="moodForm" method="POST" action="<?= $baseUrl ?>/wellness/storeMood">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::get('csrf_token','')) ?>">

            <div class="mood-slider-wrap mb-4">
              <div class="mood-emoji-row">
                <?php foreach ($emojiMap as $i => $emoji): ?>
                  <span title="<?= MOOD_LABELS[$i+1] ?? '' ?>"><?= $emoji ?></span>
                <?php endforeach; ?>
              </div>
              <input type="range" id="moodRange" name="mood_level"
                     class="mood-range" min="1" max="10"
                     value="<?= $todayMood['mood_level'] ?? 5 ?>">
              <div id="moodLabel" class="mood-level-label">Average 😶</div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-600">How are you feeling? (optional)</label>
              <textarea name="notes" class="form-control" rows="2" data-crisis-check
                        placeholder="Describe your mood in your own words…"><?= htmlspecialchars($todayMood['notes'] ?? '') ?></textarea>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="form-label fw-600">Triggers</label>
                <input type="text" name="triggers" class="form-control" placeholder="Work, sleep, news…"
                       value="<?= htmlspecialchars($todayMood['triggers'] ?? '') ?>">
              </div>
              <div class="col-6">
                <label class="form-label fw-600">Activities</label>
                <input type="text" name="activities" class="form-control" placeholder="Exercise, reading…"
                       value="<?= htmlspecialchars($todayMood['activities'] ?? '') ?>">
              </div>
            </div>

            <div id="moodStatus"></div>

            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-check-circle me-2"></i>Log My Mood
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Stats & Chart -->
    <div class="col-lg-7">
      <!-- Averages -->
      <div class="row g-3 mb-4">
        <div class="col-6">
          <div class="stat-card primary fade-in-up">
            <div class="stat-icon"><i class="bi bi-calendar-week"></i></div>
            <div class="stat-value"><?= $avg7 ?></div>
            <div class="stat-label">7-Day Average</div>
          </div>
        </div>
        <div class="col-6">
          <div class="stat-card secondary fade-in-up">
            <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
            <div class="stat-value"><?= $avg30 ?></div>
            <div class="stat-label">30-Day Average</div>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-graph-up text-primary me-2"></i>Mood History (Last 30 Days)</div>
        <div class="card-body">
          <?php if (empty($moodValues)): ?>
            <div class="text-center py-4 text-muted">
              <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-40"></i>
              Start logging to see your mood chart
            </div>
          <?php else: ?>
            <canvas id="moodChart" height="200"></canvas>
            <script>
              window.moodData = {
                labels: <?= json_encode($moodLabels) ?>,
                values: <?= json_encode($moodValues) ?>
              };
            </script>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- History Table -->
    <?php if (!empty($last30)): ?>
    <div class="col-12">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-clock-history text-primary me-2"></i>Recent Entries</div>
        <div class="card-body p-0">
          <table class="table-mindbridge table mb-0">
            <thead><tr><th>Date</th><th>Mood</th><th>Level</th><th>Notes</th><th>Triggers</th></tr></thead>
            <tbody>
              <?php foreach (array_reverse($last30) as $entry): ?>
                <tr>
                  <td><?= date('D, M j', strtotime($entry['entry_date'])) ?></td>
                  <td style="font-size:1.4rem;"><?= $emojiMap[max(0,$entry['mood_level']-1)] ?></td>
                  <td>
                    <div class="progress" style="width:80px;">
                      <div class="progress-bar" style="width:<?= $entry['mood_level']*10 ?>%"></div>
                    </div>
                    <small class="text-muted"><?= $entry['mood_level'] ?>/10</small>
                  </td>
                  <td class="text-muted small"><?= htmlspecialchars(substr($entry['notes'] ?? '',0,60)) ?></td>
                  <td class="text-muted small"><?= htmlspecialchars($entry['triggers'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
