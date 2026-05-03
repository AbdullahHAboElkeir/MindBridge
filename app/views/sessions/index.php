<?php
$title = 'My Sessions';
ob_start();
$user = $_SESSION['user'];
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-calendar-check"></i> My Sessions</h2>
            <?php if ($user['role_name'] === 'patient'): ?>
                <a href="/sessions/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Book Session
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (empty($sessions)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No sessions found</h4>
                        <p class="text-muted">You don't have any scheduled sessions yet.</p>
                        <?php if ($user['role_name'] === 'patient'): ?>
                            <a href="/sessions/create" class="btn btn-primary">Book Your First Session</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th><?php echo $user['role_name'] === 'patient' ? 'Therapist' : 'Patient'; ?></th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo date('M j, Y', strtotime($session['scheduled_date'])); ?></strong><br>
                                            <small class="text-muted"><?php echo date('g:i A', strtotime($session['scheduled_date'])); ?> (<?php echo $session['duration_minutes']; ?> min)</small>
                                        </td>
                                        <td><?php echo $session['first_name'] . ' ' . $session['last_name']; ?></td>
                                        <td><?php echo ucfirst($session['session_type']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $session['status'] === 'completed' ? 'success' :
                                                     ($session['status'] === 'confirmed' ? 'primary' :
                                                     ($session['status'] === 'scheduled' ? 'warning' : 'secondary'));
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $session['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/sessions/<?php echo $session['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
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