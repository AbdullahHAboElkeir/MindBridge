<div class="row mb-4">
    <div class="col-lg-12">
        <h2 class="h4 text-primary">Community Forum</h2>
        <p class="text-muted">Connect anonymously, share support, and join moderated conversations.</p>
    </div>
</div>
<div class="row g-4">
    <?php foreach ($forums as $forum): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($forum['title']); ?></h5>
                    <p class="small text-muted">Created by <?php echo htmlspecialchars($forum['author']); ?> on <?php echo htmlspecialchars($forum['created_at']); ?></p>
                    <p><?php echo htmlspecialchars($forum['description']); ?></p>
                    <a href="<?php echo $baseUrl; ?>?controller=forum&action=view&id=<?php echo $forum['id']; ?>" class="stretched-link">View discussion</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($forums)): ?>
        <div class="col-12"><div class="alert alert-info">No forum topics published yet.</div></div>
    <?php endif; ?>
</div>
