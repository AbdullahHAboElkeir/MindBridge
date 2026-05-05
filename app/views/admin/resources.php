<?php
$baseUrl = BASE_URL;
$resources = $resources ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up d-flex justify-content-between align-items-start">
    <div>
      <h1><i class="bi bi-book-heart me-2"></i>Wellness Resources</h1>
      <p>Manage articles, exercises, and media for patients</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resourceModal"
            onclick="resetResourceForm()">
      <i class="bi bi-plus-circle me-2"></i>Add Resource
    </button>
  </div>

  <?php if (empty($resources)): ?>
    <div class="card text-center py-5 fade-in-up">
      <i class="bi bi-book fs-1 text-muted d-block mb-3 opacity-40"></i>
      <h5>No resources yet</h5>
      <p class="text-muted">Add wellness resources for patients and therapists.</p>
    </div>
  <?php else: ?>
    <div class="card fade-in-up">
      <div class="card-header">
        <i class="bi bi-book-heart text-primary me-2"></i><?= count($resources) ?> resource(s)
      </div>
      <div class="card-body p-0">
        <table class="table-mindbridge table mb-0">
          <thead>
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Type</th>
              <th>Views</th>
              <th>Featured</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resources as $r): ?>
              <tr>
                <td>
                  <div class="fw-600 small"><?= htmlspecialchars($r['title']) ?></div>
                  <?php if (!empty($r['description'])): ?>
                    <div class="text-muted" style="font-size:.75rem;">
                      <?= htmlspecialchars(substr($r['description'], 0, 60)) ?>…
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:.7rem;">
                    <?= ucfirst($r['category']) ?>
                  </span>
                </td>
                <td class="text-muted small"><?= ucfirst($r['type']) ?></td>
                <td class="text-muted small"><?= number_format($r['view_count']) ?></td>
                <td>
                  <?php if ($r['is_featured']): ?>
                    <span class="badge" style="background:#fff3cd;color:#856404;font-size:.7rem;">
                      <i class="bi bi-star-fill me-1"></i>Featured
                    </span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge-status <?= $r['is_active'] ? 'confirmed' : 'cancelled' ?>">
                    <?= $r['is_active'] ? 'Active' : 'Hidden' ?>
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal" data-bs-target="#resourceModal"
                            onclick="editResource(<?= htmlspecialchars(json_encode($r)) ?>)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" action="<?= $baseUrl ?>/admin/deleteResource/<?= $r['id'] ?>"
                          onsubmit="return confirm('Delete this resource?')">
                      <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Add / Edit Resource Modal -->
<div class="modal fade" id="resourceModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:var(--radius);">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-700" id="resourceModalTitle">
          <i class="bi bi-book-heart me-2 text-primary"></i>Add Resource
        </h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= $baseUrl ?>/admin/storeResource" id="resourceForm">
        <input type="hidden" name="resource_id" id="resourceId" value="0">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
              <input type="text" name="title" id="resTitle" class="form-control" required
                     placeholder="e.g. Understanding Anxiety: A Guide">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Type</label>
              <select name="type" id="resType" class="form-select">
                <option value="article">Article</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="exercise">Exercise</option>
                <option value="worksheet">Worksheet</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Category</label>
              <select name="category" id="resCategory" class="form-select">
                <?php foreach (['anxiety','depression','stress','sleep','mindfulness','relationships','grief','trauma','general'] as $cat): ?>
                  <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Short Description</label>
              <textarea name="description" id="resDescription" class="form-control" rows="2"
                        placeholder="Brief summary of this resource…"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Content</label>
              <textarea name="content" id="resContent" class="form-control" rows="6"
                        placeholder="Full article content, instructions, or exercise details…"></textarea>
            </div>
            <div class="col-12">
              <div class="d-flex gap-4 flex-wrap">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_featured" id="resFeatured" value="1">
                  <label class="form-check-label fw-600" for="resFeatured">
                    <i class="bi bi-star-fill text-warning me-1"></i>Feature this resource
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_active" id="resActive" value="1" checked>
                  <label class="form-check-label fw-600" for="resActive">
                    <i class="bi bi-eye text-success me-1"></i>Active (visible to users)
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-2"></i><span id="resourceSubmitLabel">Add Resource</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function resetResourceForm() {
  document.getElementById('resourceId').value      = '0';
  document.getElementById('resTitle').value        = '';
  document.getElementById('resType').value         = 'article';
  document.getElementById('resCategory').value     = 'general';
  document.getElementById('resDescription').value  = '';
  document.getElementById('resContent').value      = '';
  document.getElementById('resFeatured').checked   = false;
  document.getElementById('resActive').checked     = true;
  document.getElementById('resourceModalTitle').innerHTML =
    '<i class="bi bi-book-heart me-2 text-primary"></i>Add Resource';
  document.getElementById('resourceSubmitLabel').textContent = 'Add Resource';
}

function editResource(r) {
  document.getElementById('resourceId').value      = r.id;
  document.getElementById('resTitle').value        = r.title        || '';
  document.getElementById('resType').value         = r.type         || 'article';
  document.getElementById('resCategory').value     = r.category     || 'general';
  document.getElementById('resDescription').value  = r.description  || '';
  document.getElementById('resContent').value      = r.content      || '';
  document.getElementById('resFeatured').checked   = parseInt(r.is_featured) === 1;
  document.getElementById('resActive').checked     = parseInt(r.is_active)   === 1;
  document.getElementById('resourceModalTitle').innerHTML =
    '<i class="bi bi-pencil me-2 text-primary"></i>Edit Resource';
  document.getElementById('resourceSubmitLabel').textContent = 'Save Changes';
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
