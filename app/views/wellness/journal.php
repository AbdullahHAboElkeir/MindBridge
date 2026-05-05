<?php
$baseUrl   = BASE_URL;
$editEntry = $editEntry ?? null;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$moodTags = ['hopeful','grateful','struggling','anxious','calm','sad','angry','content','excited','tired'];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-journal-text me-2"></i>My Journal</h1>
    <p>A private space to reflect on your thoughts and feelings</p>
  </div>

  <div class="row g-4">

    <!-- Write Entry -->
    <div class="col-lg-5">
      <div class="card fade-in-up">
        <div class="card-header">
          <i class="bi bi-pen text-primary me-2"></i>
          <?= $editEntry ? 'Edit Entry' : 'New Entry' ?>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= $baseUrl ?>/wellness/storeJournal" class="needs-validation" novalidate>
            <input type="hidden" name="entry_id" value="<?= $editEntry['id'] ?? 0 ?>">
            <div class="mb-3">
              <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required
                     value="<?= htmlspecialchars($editEntry['title'] ?? '') ?>"
                     placeholder="What's on your mind today?">
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Entry <span class="text-danger">*</span></label>
              <textarea name="content" class="form-control" rows="8" required data-crisis-check
                        placeholder="Write freely… this is your private space."><?= htmlspecialchars($editEntry['content'] ?? '') ?></textarea>
            </div>
            <div class="mb-4">
              <label class="form-label fw-600">Mood Tag</label>
              <select name="mood_tag" class="form-select">
                <option value="">— No tag —</option>
                <?php foreach ($moodTags as $tag): ?>
                  <option value="<?= $tag ?>" <?= ($editEntry['mood_tag'] ?? '') === $tag ? 'selected' : '' ?>>
                    <?= ucfirst($tag) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-save me-2"></i><?= $editEntry ? 'Update Entry' : 'Save Entry' ?>
              </button>
              <?php if ($editEntry): ?>
                <a href="<?= $baseUrl ?>/wellness/journal" class="btn btn-outline-primary">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Entries List -->
    <div class="col-lg-7">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-700 mb-0"><?= $total ?> Journal <?= $total === 1 ? 'Entry' : 'Entries' ?></h5>
      </div>

      <?php if (empty($entries)): ?>
        <div class="card fade-in-up text-center py-5">
          <i class="bi bi-journal fs-1 text-muted d-block mb-3 opacity-40"></i>
          <h5>Your journal is empty</h5>
          <p class="text-muted">Write your first entry to get started.</p>
        </div>
      <?php else: ?>
        <?php foreach ($entries as $entry): ?>
          <div class="card mb-3 fade-in-up">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="fw-700 mb-1"><?= htmlspecialchars($entry['title']) ?></h6>
                  <span class="text-muted small">
                    <i class="bi bi-calendar me-1"></i>
                    <?= date('D, M j Y \a\t g:i A', strtotime($entry['created_at'])) ?>
                    <?php if ($entry['mood_tag']): ?>
                      &nbsp;·&nbsp;
                      <span class="forum-category-badge"><?= ucfirst($entry['mood_tag']) ?></span>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="d-flex gap-1">
                  <a href="?edit=<?= $entry['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="<?= $baseUrl ?>/wellness/deleteJournal/<?= $entry['id'] ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this entry?')" title="Delete">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </div>
              <p class="text-muted mb-0" style="font-size:.9rem;">
                <?= htmlspecialchars(substr($entry['content'], 0, 220)) ?>
                <?= strlen($entry['content']) > 220 ? '…' : '' ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
          <div class="d-flex justify-content-center gap-1 mt-3">
            <?php for ($p = 1; $p <= $pages; $p++): ?>
              <a href="?page=<?= $p ?>" class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= $p ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
