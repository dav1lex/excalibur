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

<div class="row">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User List</h5>
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
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
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
                                        <td><?= date('M j, Y', strtotime($userData['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>admin/edit-user?id=<?= $userData['id'] ?>" class="btn btn-primary">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 