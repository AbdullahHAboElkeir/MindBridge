<div class="row mb-4">
    <div class="col-lg-12">
        <div class="p-4 rounded-4 bg-white shadow-sm">
            <h1 class="h3 text-primary">Therapist Dashboard</h1>
            <p class="text-muted">Hello <?php echo htmlspecialchars($user['name']); ?>. Manage your sessions and community support.</p>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h5>Upcoming Sessions</h5>
            <div class="list-group">
                <?php foreach ($appointments as $appointment): ?>
                    <div class="list-group-item">
                        <strong><?php echo htmlspecialchars($appointment['patient_name'] ?? 'Patient'); ?></strong>
                        <span class="text-muted d-block"><?php echo htmlspecialchars($appointment['start_time']); ?> - <?php echo htmlspecialchars($appointment['status']); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                    <div class="list-group-item text-muted">No sessions scheduled.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4">
            <h5>Quick Actions</h5>
            <a href="<?php echo $baseUrl; ?>?controller=report&action=generate" class="btn btn-outline-primary w-100 mb-2">Generate Report</a>
            <a href="<?php echo $baseUrl; ?>?controller=resource&action=add" class="btn btn-outline-secondary w-100">Add Resource</a>
        </div>
    </div>
</div>
