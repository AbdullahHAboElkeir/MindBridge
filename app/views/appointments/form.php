<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 text-primary mb-3">Book a Therapy Session</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Therapist</label>
                        <select name="therapist_id" class="form-select" required>
                            <option value="">Select therapist</option>
                            <?php foreach ($therapists as $therapist): ?>
                                <?php if ($therapist['role'] === 'therapist'): ?>
                                    <option value="<?php echo $therapist['id']; ?>"><?php echo htmlspecialchars($therapist['name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start time</label>
                                <input type="datetime-local" name="start_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End time</label>
                                <input type="datetime-local" name="end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time zone</label>
                        <input type="text" name="timezone" class="form-control" value="UTC">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Session notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Request session</button>
                </form>
            </div>
        </div>
    </div>
</div>
