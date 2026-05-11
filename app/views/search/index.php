<?php
/**
 * Search Results Page
 * Displays therapists, forum posts, and resources matching the query.
 */
if (!defined('BASE_PATH')) die('Direct access not permitted.');
$baseUrl = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-search me-2"></i>Search Results</h1>
    <p>Find therapists, forum posts, and wellness resources</p>
  </div>

  <div class="row g-4">
    <div class="col-12">
      <div class="card fade-in-up">
        <div class="card-body">
          <?php if (empty($query)): ?>
            <div class="text-center py-5">
              <i class="bi bi-search display-1 text-muted mb-3"></i>
              <h4 class="text-muted">Search MindBridge</h4>
              <p class="text-muted">Enter a search term to find therapists, forum posts, and wellness resources.</p>
            </div>
          <?php elseif (empty($results['therapists']) && empty($results['forum_posts']) && empty($results['resources'])): ?>
            <div class="text-center py-5">
              <i class="bi bi-search display-1 text-muted mb-3"></i>
              <h4 class="text-muted">No results found</h4>
              <p class="text-muted">No matches for "<strong><?= htmlspecialchars($query) ?></strong>". Try different keywords or check your spelling.</p>
              <a href="<?= $baseUrl ?>/dashboard" class="btn btn-primary">Back to Dashboard</a>
            </div>
          <?php else: ?>

            <!-- Therapists -->
            <?php if (!empty($results['therapists'])): ?>
              <h5 class="mb-3">
                <i class="bi bi-person-badge me-2"></i>
                Therapists (<?= count($results['therapists']) ?>)
              </h5>
              <div class="row g-3 mb-4">
                <?php foreach ($results['therapists'] as $therapist): ?>
                  <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <h6 class="card-title">
                          <?= htmlspecialchars($therapist['first_name'] . ' ' . $therapist['last_name']) ?>
                        </h6>
                        <p class="card-text small text-muted">
                          <?= htmlspecialchars(substr($therapist['bio'] ?? '', 0, 100)) ?>...
                        </p>
                        <div class="mb-2">
                          <small class="text-muted">
                            Specializations: <?= htmlspecialchars($therapist['specializations'] ?? 'N/A') ?>
                          </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                          <small class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <?= number_format($therapist['rating'], 1) ?> (<?= $therapist['total_reviews'] ?> reviews)
                          </small>
                          <a href="<?= $baseUrl ?>/patient/therapist/<?= $therapist['id'] ?>" class="btn btn-sm btn-outline-primary">
                            View Profile
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <!-- Forum Posts -->
            <?php if (!empty($results['forum_posts'])): ?>
              <h5 class="mb-3">
                <i class="bi bi-chat-square-text me-2"></i>
                Forum Posts (<?= count($results['forum_posts']) ?>)
              </h5>
              <div class="list-group mb-4">
                <?php foreach ($results['forum_posts'] as $post): ?>
                  <a href="<?= $baseUrl ?>/forum/view/<?= $post['id'] ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                      <h6 class="mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                      <small class="text-muted">
                        <?= date('M j, Y', strtotime($post['created_at'])) ?>
                      </small>
                    </div>
                    <p class="mb-1 small text-muted">
                      <?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...
                    </p>
                    <small class="text-muted">
                      By <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?>
                    </small>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <!-- Resources -->
            <?php if (!empty($results['resources'])): ?>
              <h5 class="mb-3">
                <i class="bi bi-book me-2"></i>
                Wellness Resources (<?= count($results['resources']) ?>)
              </h5>
              <div class="row g-3">
                <?php foreach ($results['resources'] as $resource): ?>
                  <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <h6 class="card-title">
                          <i class="bi bi-book me-2"></i>
                          <?= htmlspecialchars($resource['title']) ?>
                        </h6>
                        <p class="card-text small">
                          <?= htmlspecialchars(substr($resource['description'] ?? '', 0, 100)) ?>...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                          <small class="badge bg-secondary">
                            <?= htmlspecialchars($resource['category'] ?? 'General') ?>
                          </small>
                          <a href="<?= $baseUrl ?>/wellness/resource/<?= $resource['id'] ?>" class="btn btn-sm btn-outline-primary">
                            View Resource
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>