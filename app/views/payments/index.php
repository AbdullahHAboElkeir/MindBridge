<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-credit-card me-2"></i>Payments</h1>
    <p>Manage your session payments and billing history</p>
  </div>

  <!-- Summary -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="stat-card primary fade-in-up">
        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-value">$<?= number_format($summary['total'],2) ?></div>
        <div class="stat-label">Total Paid</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-card accent fade-in-up">
        <div class="stat-icon"><i class="bi bi-clock"></i></div>
        <div class="stat-value">$<?= number_format($summary['pending'],2) ?></div>
        <div class="stat-label">Pending</div>
      </div>
    </div>
  </div>

  <div class="card fade-in-up">
    <div class="card-header"><i class="bi bi-receipt text-primary me-2"></i>Payment History</div>
    <div class="card-body p-0">
      <?php if (empty($payments)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-receipt fs-1 d-block mb-3 opacity-40"></i>
          No payments yet.
        </div>
      <?php else: ?>
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr><th>Session</th><th>Therapist</th><th>Amount</th><th>Method</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $p): ?>
              <tr>
                <td>
                  <div class="fw-600"><?= date('M j, Y', strtotime($p['scheduled_at'])) ?></div>
                  <div class="text-muted small"><?= date('g:i A', strtotime($p['scheduled_at'])) ?> · <?= ucfirst($p['type']) ?></div>
                </td>
                <td>Dr. <?= htmlspecialchars($p['t_first'].' '.$p['t_last']) ?></td>
                <td class="fw-700">$<?= number_format($p['amount'],2) ?></td>
                <td><?= ucfirst(str_replace('_',' ',$p['method'])) ?></td>
                <td><span class="badge-status <?= $p['status'] === 'paid' ? 'confirmed' : ($p['status'] === 'pending' ? 'pending' : 'cancelled') ?>">
                  <?= ucfirst($p['status']) ?>
                </span></td>
                <td>
                  <?php if ($p['status'] === 'pending'): ?>
                    <button type="button" class="btn btn-sm btn-primary"
                            data-bs-toggle="modal" data-bs-target="#payModal"
                            data-id="<?= $p['id'] ?>" data-amount="<?= $p['amount'] ?>">
                      Pay Now
                    </button>
                  <?php elseif ($p['status'] === 'paid'): ?>
                    <span class="text-muted small">
                      <i class="bi bi-check-circle-fill text-success me-1"></i>
                      <?= date('M j', strtotime($p['paid_at'])) ?>
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:var(--radius);">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-700"><i class="bi bi-credit-card me-2 text-primary"></i>Process Payment</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= $baseUrl ?>/payments/process">
        <input type="hidden" name="payment_id" id="modalPayId">
        <div class="modal-body">
          <div class="text-center mb-4">
            <div class="fw-700" style="font-size:2rem;color:var(--primary);" id="modalAmount">$0.00</div>
            <div class="text-muted">Session payment</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Payment Method</label>
            <select name="method" class="form-select">
              <option value="credit_card">💳 Credit Card</option>
              <option value="bank_transfer">🏦 Bank Transfer</option>
              <option value="insurance">🏥 Insurance</option>
              <option value="cash">💵 Cash</option>
            </select>
          </div>
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            This is a simulated payment for academic purposes. No real charges will be made.
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-2"></i>Confirm Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('payModal').addEventListener('show.bs.modal', e => {
  document.getElementById('modalPayId').value = e.relatedTarget.dataset.id;
  document.getElementById('modalAmount').textContent = '$' + parseFloat(e.relatedTarget.dataset.amount).toFixed(2);
});
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
