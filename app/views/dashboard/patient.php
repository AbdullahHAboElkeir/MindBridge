<div class="row mb-4">
    <div class="col-lg-12">
        <div class="p-4 rounded-4 bg-white shadow-sm">
            <h1 class="h3 text-primary">Patient Dashboard</h1>
            <p class="text-muted">Welcome, <?php echo htmlspecialchars($user['name']); ?>. Track your wellbeing and scheduled sessions.</p>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4">
            <h5>Mood overview</h5>
            <div class="list-group">
                <?php foreach ($moodEntries as $entry): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($entry['mood_date']); ?></strong>
                            <div class="text-muted small"><?php echo htmlspecialchars($entry['note']); ?></div>
                        </div>
                        <span class="badge bg-info"><?php echo htmlspecialchars($entry['mood_level']); ?>/5</span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($moodEntries)): ?>
                    <div class="list-group-item text-muted">No mood entries yet.</div>
                <?php endif; ?>
            </div>
            <a href="<?php echo $baseUrl; ?>?controller=mood&action=index" class="btn btn-link mt-3">View mood tracker</a>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4">
            <h5>Upcoming appointments</h5>
            <div class="list-group">
                <?php foreach ($appointments as $appointment): ?>
                    <div class="list-group-item">
                        <strong><?php echo htmlspecialchars($appointment['therapist_name'] ?? 'Therapist'); ?></strong>
                        <div class="text-muted small"><?php echo htmlspecialchars($appointment['start_time']); ?> | <?php echo htmlspecialchars($appointment['status']); ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                    <div class="list-group-item text-muted">No appointments scheduled.</div>
                <?php endif; ?>
            </div>
            <a href="<?php echo $baseUrl; ?>?controller=appointment&action=book" class="btn btn-outline-primary mt-3 w-100">Book a session</a>
        </div>
    </div>
</div>
