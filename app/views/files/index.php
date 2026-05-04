<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h4 text-primary">My Documents</h2>
            <p class="text-muted">Upload secure intake forms, reports, and licenses.</p>
        </div>
        <a href="<?php echo $baseUrl; ?>?controller=file&action=upload" class="btn btn-primary">Upload File</a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Filename</th><th>Type</th><th>Uploaded</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($uploads as $upload): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($upload['original_name']); ?></td>
                            <td><?php echo htmlspecialchars($upload['file_type']); ?></td>
                            <td><?php echo htmlspecialchars($upload['created_at']); ?></td>
                            <td><a href="<?php echo $baseUrl; ?>uploads/<?php echo htmlspecialchars($upload['filename']); ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Download</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($uploads)): ?>
                        <tr><td colspan="4" class="text-muted">No documents uploaded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
