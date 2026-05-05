<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-exclamation-circle me-2"></i>Disputes</h1>
    <p>Review and resolve reported disputes between users</p>
  </div>

  <!-- Status tabs -->
  <div class="card mb-4 fade-in-up">
    <div class="card-body py-2 px-3">
      <div class="d-flex gap-2 flex-wrap">
        <?php foreach (['open' => 'Open', 'under_review' => 'Under Review', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $s => $label): ?>
          <a href="<?= $baseUrl ?>/admin/disputes?status=<?= $s ?>"
             class="btn btn-sm <?= $status === $s ? 'btn-primary' : 'btn-outline-primary' ?>">
            <?= $label ?>
            <?php if (isset($counts[$s]) && $counts[$s] > 0): ?>
              <span class="badge bg-white text-primary ms-1"><?= $counts[$s] ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <?php if (empty($disputes)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-check-circle fs-1 text-muted d-block mb-3 opacity-40"></i>
      <h5>No <?= $status ?> disputes</h5>
      <p class="text-muted">All disputes in this category have been handled.</p>
    </div>
  <?php else: ?>
    <div class="card fade-in-up">
      <div class="card-header">
        <i class="bi bi-exclamation-circle text-primary me-2"></i>
        <?= count($disputes) ?> dispute(s) — <?= ucfirst(str_replace('_', ' ', $status)) ?>
      </div>
      <div class="card-body p-0">
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Filed By</th>
              <th>Against</th>
              <th>Reason</th>
              <th>Filed</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($disputes as $d): ?>
              <tr>
                <td class="text-muted small"><?= $d['id'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar" style="width:32px;height:32px;font-size:.8rem;">
                      <?= strtoupper(substr($d['filer_first'], 0, 1)) ?>
                    </div>
                    <span class="fw-600 small"><?= htmlspecialchars($d['filer_first'] . ' ' . $d['filer_last']) ?></span>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar" style="width:32px;height:32px;font-size:.8rem;background:var(--accent);">
                      <?= strtoupper(substr($d['against_first'], 0, 1)) ?>
                    </div>
                    <span class="fw-600 small"><?= htmlspecialchars($d['against_first'] . ' ' . $d['against_last']) ?></span>
                  </div>
                </td>
                <td>
                  <div class="text-muted small" style="max-width:200px;">
                    <?= htmlspecialchars(substr($d['reason'], 0, 100)) ?>
                    <?= strlen($d['reason']) > 100 ? '…' : '' ?>
                  </div>
                </td>
                <td class="text-muted small"><?= date('M j, Y', strtotime($d['created_at'])) ?></td>
                <td>
                  <span class="badge-status <?= match($d['status']) {
                    'open'         => 'pending',
                    'under_review' => 'scheduled',
                    'resolved'     => 'confirmed',
                    default        => 'cancelled'
                  } ?>">
                    <?= ucfirst(str_replace('_', ' ', $d['status'])) ?>
                  </span>
                </td>
                <td>
                  <?php if (in_array($d['status'], ['open', 'under_review'])): ?>
                    <button type="button" class="btn btn-sm btn-primary"
                            data-bs-toggle="modal" data-bs-target="#resolveModal"
                            data-id="<?= $d['id'] ?>"
                            data-reason="<?= htmlspecialchars($d['reason']) ?>">
                      <i class="bi bi-gavel me-1"></i>Resolve
                    </button>
                  <?php else: ?>
                    <span class="text-muted small">
                      <?= $d['resolved_at'] ? date('M j', strtotime($d['resolved_at'])) : '—' ?>
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php if (!empty($d['resolution'])): ?>
                <tr class="table-light">
                  <td colspan="7" class="text-muted small py-2 px-4">
                    <i class="bi bi-chat-left-text me-2 text-success"></i>
                    <strong>Resolution:</strong> <?= htmlspecialchars($d['resolution']) ?>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:var(--radius);">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-700"><i class="bi bi-gavel me-2 text-primary"></i>Resolve Dispute</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= $baseUrl ?>/admin/resolveDispute">
        <input type="hidden" name="dispute_id" id="modalDisputeId">
        <div class="modal-body">
          <div class="alert alert-secondary mb-3 small" id="modalReason" style="border-radius:var(--radius-sm);"></div>
          <div class="mb-3">
            <label class="form-label fw-600">Resolution Action</label>
            <select name="action" class="form-select" required>
              <option value="under_review">Move to Under Review</option>
              <option value="resolved">Mark Resolved</option>
              <option value="closed">Close Dispute</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Resolution Notes</label>
            <textarea name="resolution" class="form-control" rows="3"
                      placeholder="Describe how this dispute was resolved…"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('resolveModal').addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  document.getElementById('modalDisputeId').value = btn.dataset.id;
  document.getElementById('modalReason').textContent = btn.dataset.reason;
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
