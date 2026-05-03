<?php
$title = 'Wellness & Self-Help';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-heart"></i> Your Wellness Journey</h2>
        <p class="text-muted">Track your mood, maintain journals, and access wellness resources.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-smile fa-3x mb-3"></i>
                <h5>Mood Tracker</h5>
                <p>Track your daily mood</p>
                <h3><?php echo $average_mood; ?>/10</h3>
                <small>Average this week</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x mb-3"></i>
                <h5>Journal</h5>
                <p>Personal reflections</p>
                <h3><?php echo count($journals); ?></h3>
                <small>Entries written</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-lightbulb fa-3x mb-3"></i>
                <h5>Resources</h5>
                <p>Wellness articles</p>
                <h3>15</h3>
                <small>Available now</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Mood History</h5>
            </div>
            <div class="card-body">
                <?php if (empty($moods)): ?>
                    <p class="text-muted">No mood entries yet. Start tracking your mood below!</p>
                <?php else: ?>
                    <canvas id="moodChart" width="400" height="200"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Quick Journal Entry</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/journals">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="title" placeholder="Entry title..." required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="4" placeholder="What's on your mind today?" required></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_private" name="is_private" checked>
                        <label class="form-check-label" for="is_private">Keep private</label>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Save Entry</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-book-open"></i> Recent Journal Entries</h5>
            </div>
            <div class="card-body">
                <?php if (empty($journals)): ?>
                    <p class="text-muted">No journal entries yet.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach (array_slice($journals, 0, 3) as $journal): ?>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6><?php echo htmlspecialchars($journal['title']); ?></h6>
                                        <p class="text-muted small"><?php echo date('M j, Y', strtotime($journal['created_at'])); ?></p>
                                        <p><?php echo substr(htmlspecialchars($journal['content']), 0, 100); ?>...</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center">
                        <a href="/journals" class="btn btn-outline-primary">View All Entries</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($moods)): ?>
const moodData = <?php echo json_encode(array_reverse(array_slice($moods, -7))); ?>;
const ctx = document.getElementById('moodChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: moodData.map(m => new Date(m.recorded_at).toLocaleDateString()),
        datasets: [{
            label: 'Mood Level',
            data: moodData.map(m => m.mood_level),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 10
            }
        }
    }
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>