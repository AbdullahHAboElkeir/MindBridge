<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h2 class="mb-3 text-primary">Create an account</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Full name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="roleSelect" class="form-select" onchange="toggleTherapistFields()">
                            <option value="patient">Patient</option>
                            <option value="therapist">Therapist</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div id="therapistFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization" class="form-control" value="General">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">License number</label>
                            <input type="text" name="license_number" class="form-control" value="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Therapist rating (0-5)</label>
                            <input type="number" name="rating" class="form-control" min="0" max="5" step="1" value="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <script>
                function toggleTherapistFields() {
                    const role = document.getElementById('roleSelect').value;
                    const therapistFields = document.getElementById('therapistFields');
                    if (role === 'therapist') {
                        therapistFields.style.display = 'block';
                    } else {
                        therapistFields.style.display = 'none';
                    }
                }
                </script>
            </div>
        </div>
    </div>
</div>
