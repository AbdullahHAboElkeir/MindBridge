<?php
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-people me-2"></i>User Management</h1>
    <p>View, search, and manage all registered users</p>
  </div>

  <!-- Filters -->
  <div class="card mb-4 fade-in-up">
    <div class="card-body py-3">
      <form method="GET" class="d-flex flex-wrap gap-3 align-items-end">
        <div class="flex-fill">
          <input type="text" name="search" class="form-control" placeholder="Search by name or email…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <div>
          <select name="role" class="form-select">
            <option value="">All Roles</option>
            <?php foreach (['patient','therapist','admin'] as $r): ?>
              <option value="<?= $r ?>" <?= $role === $r ? 'selected':'' ?>><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?= $baseUrl ?>/admin/users" class="btn btn-outline-primary">Reset</a>
      </form>
    </div>
  </div>

  <div class="card fade-in-up">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-people text-primary me-2"></i><?= $total ?> users found</span>
    </div>
    <div class="card-body p-0">
      <table class="table-mindbridge table mb-0">
        <thead>
          <tr><th>#</th><th>User</th><th>Role</th><th>Status</th><th>Registered</th><th>Last Login</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="text-muted small"><?= $u['id'] ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar" style="width:34px;height:34px;font-size:.8rem;">
                    <?= strtoupper(substr($u['first_name'],0,1)) ?>
                  </div>
                  <div>
                    <div class="fw-600"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($u['email']) ?></div>
                  </div>
                </div>
              </td>
              <td><span class="badge-status <?= $u['role']==='admin' ? 'confirmed':($u['role']==='therapist'?'scheduled':'pending') ?>">
                <?= ucfirst($u['role']) ?>
              </span></td>
              <td><span class="badge-status <?= $u['status'] === 'active' ? 'active':'cancelled' ?>">
                <?= ucfirst($u['status']) ?>
              </span></td>
              <td class="text-muted small"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
              <td class="text-muted small"><?= $u['last_login'] ? date('M j, Y', strtotime($u['last_login'])) : 'Never' ?></td>
              <td>
                <form method="POST" action="<?= $baseUrl ?>/admin/manageUser/<?= $u['id'] ?>" class="d-inline">
                  <?php if ($u['status'] === 'active' && $u['role'] !== 'admin'): ?>
                    <input type="hidden" name="action" value="suspend">
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Suspend this user?')">
                      <i class="bi bi-slash-circle"></i>
                    </button>
                  <?php elseif ($u['status'] === 'suspended'): ?>
                    <input type="hidden" name="action" value="activate">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                      <i class="bi bi-check-circle"></i>
                    </button>
                  <?php endif; ?>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
    <div class="d-flex justify-content-center gap-1 mt-4">
      <?php for ($p=1; $p<=$pages; $p++): ?>
        <a href="?page=<?= $p ?><?= $role?"&role=$role":'' ?>"
           class="btn btn-sm <?= $p===$page?'btn-primary':'btn-outline-primary' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
