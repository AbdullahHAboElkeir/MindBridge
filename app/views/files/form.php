<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 text-primary mb-3">Secure Document Upload</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Select file</label>
                        <input type="file" name="document" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload secure file</button>
                </form>
            </div>
        </div>
    </div>
</div>
