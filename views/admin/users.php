<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Manage Users</h1>
    </div>
    <div class="col-md-6 text-end align-self-center">
        <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="col-12">
        <div class="container mt-3">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>User List</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="<?= BASE_URL ?>admin/users" class="btn btn-outline-primary <?= !isset($current_role) ? 'active' : '' ?>">All Users</a>
                    <a href="<?= BASE_URL ?>admin/users?role=admin" class="btn btn-outline-danger <?= $current_role === 'admin' ? 'active' : '' ?>">Admins</a>
                    <a href="<?= BASE_URL ?>admin/users?role=user" class="btn btn-outline-success <?= $current_role === 'user' ? 'active' : '' ?>">Regular Users</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $userData): ?>
                                    <tr>
                                        <td><?= $userData['id'] ?></td>
                                        <td><?= htmlspecialchars($userData['name']) ?></td>
                                        <td><?= htmlspecialchars($userData['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $userData['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                <?= ucfirst(htmlspecialchars($userData['role'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($userData['is_confirmed']): ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Unverified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($userData['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>admin/edit-user?id=<?= $userData['id'] ?>" class="btn btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($userData['id'] != $_SESSION['user_id']): ?>
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $userData['id'] ?>" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Delete User Modal -->
                                    <?php if ($userData['id'] != $_SESSION['user_id']): ?>
                                    <div class="modal fade" id="deleteUserModal<?= $userData['id'] ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel<?= $userData['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteUserModalLabel<?= $userData['id'] ?>">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the user "<?= htmlspecialchars($userData['name']) ?>"? This action cannot be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="<?= BASE_URL ?>admin/delete-user?id=<?= $userData['id'] ?>" class="btn btn-danger">Delete User</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 