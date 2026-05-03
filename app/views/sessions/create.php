<?php
$title = 'Book Session';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-calendar-plus"></i> Book a Therapy Session</h2>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/sessions">
                    <div class="mb-3">
                        <label for="therapist_id" class="form-label">Select Therapist</label>
                        <select class="form-control" id="therapist_id" name="therapist_id" required>
                            <option value="">Choose a therapist</option>
                            <?php foreach ($therapists as $therapist): ?>
                                <option value="<?php echo $therapist['user_id']; ?>">
                                    Dr. <?php echo $therapist['first_name'] . ' ' . $therapist['last_name']; ?>
                                    <?php if ($therapist['specialization']): ?>
                                        - <?php echo $therapist['specialization']; ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scheduled_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scheduled_time" class="form-label">Time</label>
                                <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="session_type" class="form-label">Session Type</label>
                        <select class="form-control" id="session_type" name="session_type" required>
                            <option value="individual">Individual Therapy</option>
                            <option value="group">Group Therapy</option>
                            <option value="family">Family Therapy</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="duration_minutes" class="form-label">Duration</label>
                        <select class="form-control" id="duration_minutes" name="duration_minutes" required>
                            <option value="30">30 minutes</option>
                            <option value="60" selected>60 minutes</option>
                            <option value="90">90 minutes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any specific concerns or topics you'd like to discuss..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-calendar-check"></i> Book Session
                    </button>
                    <a href="/sessions" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Booking Information</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-clock text-primary"></i> Sessions are typically 50-60 minutes</li>
                    <li><i class="fas fa-dollar-sign text-success"></i> Pricing varies by therapist</li>
                    <li><i class="fas fa-calendar-alt text-info"></i> Book at least 24 hours in advance</li>
                    <li><i class="fas fa-phone text-warning"></i> Virtual sessions available</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Combine date and time into scheduled_date
document.querySelector('form').addEventListener('submit', function(e) {
    const date = document.getElementById('scheduled_date').value;
    const time = document.getElementById('scheduled_time').value;
    if (date && time) {
        document.getElementById('scheduled_date').value = date + ' ' + time + ':00';
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>