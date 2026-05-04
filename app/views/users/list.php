<div class="row mb-3">
    <div class="col-md-8">
        <h2 class="h4 text-primary">User Management</h2>
        <p class="text-muted">Search, edit and remove users from the system.</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?php echo $baseUrl; ?>?controller=user&action=add" class="btn btn-primary">Add User</a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="get" class="mb-3 row g-2 align-items-center">
            <input type="hidden" name="controller" value="user">
            <input type="hidden" name="action" value="index">
            <div class="col">
                <input type="text" name="q" class="form-control" placeholder="Search users" value="<?php echo htmlspecialchars($query); ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-secondary">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['status']); ?></td>
                            <td>
                                <a href="<?php echo $baseUrl; ?>?controller=user&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="<?php echo $baseUrl; ?>?controller=user&action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
