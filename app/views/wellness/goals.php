<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$categories = ['mental','physical','social','spiritual','work','other'];
$catColors  = ['mental'=>'primary','physical'=>'success','social'=>'info','spiritual'=>'secondary','work'=>'warning','other'=>'accent'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-trophy me-2"></i>Wellness Goals</h1>
    <p>Set, track, and achieve your personal wellness goals</p>
  </div>

  <div class="row g-4">
    <!-- Add Goal Form -->
    <div class="col-lg-4">
      <div class="card fade-in-up">
        <div class="card-header"><i class="bi bi-plus-circle text-primary me-2"></i>Add New Goal</div>
        <div class="card-body">
          <form method="POST" action="<?= $baseUrl ?>/wellness/storeGoal" class="needs-validation" novalidate>
            <input type="hidden" name="goal_id" value="0">
            <div class="mb-3">
              <label class="form-label fw-600">Goal Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required placeholder="e.g. Meditate daily">
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Description</label>
              <textarea name="description" class="form-control" rows="2" placeholder="More details…"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Category</label>
              <select name="category" class="form-select">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-4">
              <label class="form-label fw-600">Target Date</label>
              <input type="date" name="target_date" class="form-control"
                     min="<?= date('Y-m-d') ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-plus-circle me-2"></i>Create Goal
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Goals List -->
    <div class="col-lg-8">
      <?php if (empty($goals)): ?>
        <div class="card fade-in-up text-center py-5">
          <i class="bi bi-trophy fs-1 text-muted d-block mb-3 opacity-40"></i>
          <h5>No goals yet</h5>
          <p class="text-muted">Create your first wellness goal to get started!</p>
        </div>
      <?php else: ?>
        <?php
        $active    = array_filter($goals, fn($g) => $g['status'] === 'active');
        $completed = array_filter($goals, fn($g) => $g['status'] === 'completed');
        ?>

        <?php if (!empty($active)): ?>
          <h6 class="text-muted fw-600 mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
            Active Goals (<?= count($active) ?>)
          </h6>
          <?php foreach ($active as $goal): ?>
            <div class="card mb-3 fade-in-up">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div>
                    <h6 class="fw-700 mb-1"><?= htmlspecialchars($goal['title']) ?></h6>
                    <div class="d-flex gap-2 align-items-center">
                      <span class="badge-status <?= $catColors[$goal['category']] ?? 'pending' ?>">
                        <?= ucfirst($goal['category']) ?>
                      </span>
                      <?php if ($goal['target_date']): ?>
                        <span class="text-muted small">
                          <i class="bi bi-calendar me-1"></i>Target: <?= date('M j, Y', strtotime($goal['target_date'])) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="d-flex gap-1">
                    <a href="<?= $baseUrl ?>/wellness/deleteGoal/<?= $goal['id'] ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this goal?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </div>

                <?php if ($goal['description']): ?>
                  <p class="text-muted small mb-3"><?= htmlspecialchars($goal['description']) ?></p>
                <?php endif; ?>

                <!-- Progress -->
                <div class="d-flex align-items-center gap-3">
                  <div class="flex-grow-1">
                    <div class="d-flex justify-content-between mb-1">
                      <small class="text-muted">Progress</small>
                      <small class="fw-600 text-primary" id="pct-<?= $goal['id'] ?>"><?= $goal['progress'] ?>%</small>
                    </div>
                    <div class="progress">
                      <div class="progress-bar" id="goal-bar-<?= $goal['id'] ?>"
                           style="width:<?= $goal['progress'] ?>%"></div>
                    </div>
                  </div>
                  <input type="range" class="goal-progress-input" min="0" max="100" step="5"
                         value="<?= $goal['progress'] ?>" data-goal-id="<?= $goal['id'] ?>"
                         style="width:120px;" title="Drag to update progress">
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($completed)): ?>
          <h6 class="text-muted fw-600 mb-3 mt-2 text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
            Completed (<?= count($completed) ?>)
          </h6>
          <?php foreach ($completed as $goal): ?>
            <div class="card mb-3 fade-in-up" style="opacity:.7;">
              <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center gap-3">
                  <i class="bi bi-check-circle-fill text-success fs-5"></i>
                  <div>
                    <div class="fw-600"><?= htmlspecialchars($goal['title']) ?></div>
                    <div class="text-muted small"><?= ucfirst($goal['category']) ?> · Completed</div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Update pct label live as range moves
document.querySelectorAll('.goal-progress-input').forEach(input => {
  input.addEventListener('input', function() {
    const pct = document.getElementById('pct-' + this.dataset.goalId);
    if (pct) pct.textContent = this.value + '%';
  });
});
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
