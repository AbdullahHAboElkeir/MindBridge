<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm p-4">
            <h2 class="h4 text-primary"><?php echo htmlspecialchars($forum['title']); ?></h2>
            <p class="text-muted small">Published by <?php echo htmlspecialchars($forum['author']); ?> on <?php echo htmlspecialchars($forum['created_at']); ?></p>
            <p><?php echo htmlspecialchars($forum['description']); ?></p>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="mb-3">Posts</h5>
            <?php foreach ($posts as $post): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-1"><?php echo $post['is_anonymous'] ? 'Anonymous' : htmlspecialchars($post['author_name']); ?></h6>
                        <small class="text-muted"><?php echo htmlspecialchars($post['created_at']); ?></small>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <div class="text-muted">Be the first to share support in this discussion.</div>
            <?php endif; ?>
        </div>
        <div class="card border-0 shadow-sm p-4">
            <h5 class="mb-3">Add a post</h5>
            <form method="post" action="<?php echo $baseUrl; ?>?controller=forum&action=post">
                <input type="hidden" name="forum_id" value="<?php echo $forum['id']; ?>">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="anonymous" id="anonymousPost">
                    <label class="form-check-label" for="anonymousPost">Post anonymously</label>
                </div>
                <button type="submit" class="btn btn-primary">Share</button>
            </form>
        </div>
    </div>
</div>
