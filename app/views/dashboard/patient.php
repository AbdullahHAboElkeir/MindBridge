<?php
$title = 'Patient Dashboard';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo $user['first_name']; ?>!</h2>
        <p class="text-muted">Your mental health journey starts here.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-calendar-check"></i> Upcoming Sessions</h5>
                <h3><?php echo count($upcoming_sessions); ?></h3>
                <a href="/sessions" class="btn btn-light btn-sm">View All</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-smile"></i> Average Mood</h5>
                <h3><?php echo $average_mood; ?>/10</h3>
                <a href="/wellness" class="btn btn-light btn-sm">Track Mood</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Community Posts</h5>
                <h3>5</h3>
                <a href="/forum" class="btn btn-light btn-sm">Join Discussion</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar"></i> Recent Sessions</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_sessions)): ?>
                    <p class="text-muted">No upcoming sessions.</p>
                    <a href="/sessions/create" class="btn btn-primary">Book a Session</a>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach (array_slice($upcoming_sessions, 0, 3) as $session): ?>
                            <li class="list-group-item">
                                <strong><?php echo $session['first_name'] . ' ' . $session['last_name']; ?></strong>
                                <br><small><?php echo date('M j, Y g:i A', strtotime($session['scheduled_date'])); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-heart"></i> Quick Mood Check</h5>
            </div>
            <div class="card-body">
                <form id="mood-form">
                    <div class="mb-3">
                        <label>How are you feeling today? (1-10)</label>
                        <input type="range" class="form-range" id="mood_level" name="mood_level" min="1" max="10" value="5">
                        <div class="text-center"><span id="mood-value">5</span>/10</div>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="notes" placeholder="Any notes about your mood?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Save Mood</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('mood_level').addEventListener('input', function() {
    document.getElementById('mood-value').textContent = this.value;
});

document.getElementById('mood-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/mood', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Mood saved successfully!');
            location.reload();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>