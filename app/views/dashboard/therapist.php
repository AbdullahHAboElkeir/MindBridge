<?php
$title = 'Therapist Dashboard';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-user-md"></i> Welcome back, Dr. <?php echo $user['last_name']; ?>!</h2>
        <p class="text-muted">Manage your sessions and support your patients.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-calendar-check"></i> Today's Sessions</h5>
                <h3><?php echo count($today_sessions); ?></h3>
                <a href="/sessions" class="btn btn-light btn-sm">View Schedule</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Total Patients</h5>
                <h3><?php echo $total_patients; ?></h3>
                <a href="/patients" class="btn btn-light btn-sm">View Patients</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-star"></i> Average Rating</h5>
                <h3>4.8</h3>
                <a href="/feedback" class="btn btn-light btn-sm">View Reviews</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Today's Schedule</h5>
            </div>
            <div class="card-body">
                <?php if (empty($today_sessions)): ?>
                    <p class="text-muted">No sessions scheduled for today.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($today_sessions as $session): ?>
                                    <tr>
                                        <td><?php echo date('g:i A', strtotime($session['scheduled_date'])); ?></td>
                                        <td><?php echo $session['first_name'] . ' ' . $session['last_name']; ?></td>
                                        <td><?php echo ucfirst($session['session_type']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $session['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($session['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/sessions/<?php echo $session['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>