<div class="row mb-3">
    <div class="col-lg-8">
        <h2 class="h4 text-primary">Wellness Resources</h2>
        <p class="text-muted">Browse guided materials, self-help content, and therapy resources.</p>
    </div>
    <div class="col-lg-4 text-end">
        <?php if ($user['role'] !== 'patient'): ?>
            <a href="<?php echo $baseUrl; ?>?controller=resource&action=add" class="btn btn-primary">Add Resource</a>
        <?php endif; ?>
    </div>
</div>
<div class="row g-4">
    <?php foreach ($resources as $resource): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($resource['title']); ?></h5>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($resource['type']); ?> · <?php echo htmlspecialchars($resource['created_at']); ?></p>
                    <p><?php echo htmlspecialchars($resource['description']); ?></p>
                    <?php if ($resource['file_path']): ?>
                        <a href="<?php echo $baseUrl; ?>uploads/<?php echo htmlspecialchars($resource['file_path']); ?>" class="stretched-link" target="_blank">Open document</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($resources)): ?>
        <div class="col-12">
            <div class="alert alert-info">No resources available yet. Add content to support wellness plans.</div>
        </div>
    <?php endif; ?>
</div>
