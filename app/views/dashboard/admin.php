<?php
$title = 'Admin Dashboard';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-cog"></i> Admin Dashboard</h2>
        <p class="text-muted">System overview and management.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Total Users</h5>
                <h3><?php echo $total_users; ?></h3>
                <a href="/users" class="btn btn-light btn-sm">Manage Users</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-md"></i> Therapists</h5>
                <h3><?php echo $total_therapists; ?></h3>
                <a href="/users?role=therapist" class="btn btn-light btn-sm">View Therapists</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user"></i> Patients</h5>
                <h3><?php echo $total_patients; ?></h3>
                <a href="/users?role=patient" class="btn btn-light btn-sm">View Patients</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-line"></i> Reports</h5>
                <h3>12</h3>
                <a href="/reports" class="btn btn-light btn-sm">View Reports</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-plus"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/users/create" class="btn btn-primary">Add New User</a>
                    <a href="/reports/generate?type=session" class="btn btn-secondary">Generate Report</a>
                    <a href="/forum/create" class="btn btn-success">Create Forum</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> System Alerts</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> System is running normally.
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 3 pending therapist verifications.
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>