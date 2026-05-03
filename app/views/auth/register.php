<?php
$title = 'Register';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> Register for MindBridge</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/register">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo ucfirst($role['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="therapist-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="license_number" name="license_number">
                        </div>
                        <div class="mb-3">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input type="text" class="form-control" id="specialization" name="specialization">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </form>
                <div class="text-center mt-3">
                    <a href="/login">Already have an account? Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role_id').addEventListener('change', function() {
    const therapistFields = document.getElementById('therapist-fields');
    if (this.value === '2') { // therapist role id
        therapistFields.style.display = 'block';
    } else {
        therapistFields.style.display = 'none';
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>