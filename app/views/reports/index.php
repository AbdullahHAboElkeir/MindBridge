<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h4 text-primary">Reports</h2>
            <p class="text-muted">View generated analytics and PDF-ready summaries.</p>
        </div>
        <a href="<?php echo $baseUrl; ?>?controller=report&action=generate" class="btn btn-primary">Generate Report</a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Title</th><th>Category</th><th>Author</th><th>Created</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['title']); ?></td>
                            <td><?php echo htmlspecialchars($report['category']); ?></td>
                            <td><?php echo htmlspecialchars($report['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($report['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($reports)): ?>
                        <tr><td colspan="4" class="text-muted">No reports created yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
