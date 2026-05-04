<?php
// app/views/dashboard/admin.php


?>
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="p-4 rounded-4 bg-white shadow-sm">
            <h1 class="h3 text-primary">Admin Dashboard</h1>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($user['name']); ?>.</p>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h5>Active users</h5>
            <p class="display-6 text-info"><?php echo $metrics['active_users']; ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h5>Appointments</h5>
            <p class="display-6 text-success"><?php echo $metrics['appointments']; ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h5>Resources</h5>
            <p class="display-6 text-warning"><?php echo $metrics['resources']; ?></p>
        </div>
    </div>
</div>
