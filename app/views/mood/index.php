<div class="row mb-4">
    <div class="col-lg-8">
        <h2 class="h4 text-primary">Mood Tracker</h2>
        <p class="text-muted">Record today&rsquo;s wellness and review recent entries.</p>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4">
            <form id="moodForm">
                <div class="mb-3">
                    <label class="form-label">Mood level</label>
                    <select name="mood_level" class="form-select" required>
                        <option value="5">Excellent</option>
                        <option value="4">Good</option>
                        <option value="3">Neutral</option>
                        <option value="2">Low</option>
                        <option value="1">Needs support</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="note" class="form-control" rows="3"></textarea>
                </div>
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn btn-primary w-100">Save mood</button>
                <div id="moodAlert" class="mt-3 d-none alert"></div>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4">
            <h5>Recent entries</h5>
            <div id="moodList" class="list-group">
                <?php foreach ($entries as $entry): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($entry['mood_date']); ?></strong>
                            <div class="text-muted small"><?php echo htmlspecialchars($entry['note']); ?></div>
                        </div>
                        <span class="badge bg-info"><?php echo htmlspecialchars($entry['mood_level']); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($entries)): ?>
                    <div class="list-group-item text-muted">No mood entries yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('moodForm').addEventListener('submit', async function (evt) {
        evt.preventDefault();
        const form = evt.target;
        const data = new FormData(form);
        const resp = await fetch('<?php echo $baseUrl; ?>?controller=mood&action=add', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data,
        });
        const result = await resp.json();
        const alertBox = document.getElementById('moodAlert');
        if (result.status === 'success') {
            alertBox.className = 'alert alert-success mt-3';
            alertBox.textContent = 'Mood entry saved successfully.';
            alertBox.classList.remove('d-none');
            form.reset();
            setTimeout(() => alertBox.classList.add('d-none'), 3000);
        } else {
            alertBox.className = 'alert alert-danger mt-3';
            alertBox.textContent = result.message || 'Unable to save mood entry.';
            alertBox.classList.remove('d-none');
        }
    });
</script>
