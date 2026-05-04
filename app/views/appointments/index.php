<div class="row mb-4">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h4 text-primary">Appointments</h2>
                <p class="text-muted">Review and manage session bookings across the platform.</p>
            </div>
            <?php if ($user['role'] === 'patient'): ?>
                <a href="<?php echo $baseUrl; ?>?controller=appointment&action=book" class="btn btn-primary">Book Appointment</a>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Therapist</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['therapist_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="5" class="text-muted text-center">No appointments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
