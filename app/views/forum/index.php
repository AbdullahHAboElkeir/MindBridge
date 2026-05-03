<?php
$title = 'Community Forum';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-users"></i> Community Support</h2>
            <a href="/forum/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Start Discussion
            </a>
        </div>
        <p class="text-muted">Connect with others on their mental health journey. All posts are anonymous by default.</p>
    </div>
</div>

<div class="row mt-4">
    <?php foreach ($forums as $forum): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> <?php echo htmlspecialchars($forum['title']); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted"><?php echo htmlspecialchars($forum['description']); ?></p>
                    <div class="mb-3">
                        <strong><?php echo count($forum['posts']); ?> discussions</strong>
                    </div>
                    <?php if (!empty($forum['posts'])): ?>
                        <div class="recent-posts">
                            <small class="text-muted">Recent posts:</small>
                            <ul class="list-unstyled">
                                <?php foreach (array_slice($forum['posts'], 0, 2) as $post): ?>
                                    <li>
                                        <a href="/forum/<?php echo $forum['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?>...
                                        </a>
                                        <br><small class="text-muted">by <?php echo $post['is_anonymous'] ? 'Anonymous' : htmlspecialchars($post['username']); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/forum/<?php echo $forum['id']; ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye"></i> View Discussions
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shield-alt"></i> Community Guidelines</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="fas fa-heart text-success"></i> Be Supportive</h6>
                        <p class="small">Offer encouragement and understanding to fellow community members.</p>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-user-secret text-info"></i> Respect Privacy</h6>
                        <p class="small">Anonymity is respected. Never share personal information without consent.</p>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-exclamation-triangle text-warning"></i> Crisis Support</h6>
                        <p class="small">If you or someone else is in crisis, seek immediate professional help.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>