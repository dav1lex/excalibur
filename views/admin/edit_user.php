<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h1 class="display-5 mb-0">Edit User</h1>
            <a href="<?= BASE_URL ?>admin/users" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>

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
    <div class="row">
        <div class="col">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>admin/update-user" method="post">
                        <input type="hidden" name="id" value="<?= $userData['id'] ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= htmlspecialchars($userData['name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($userData['email']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text text-muted">Leave blank to keep current password</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">User ID</span>
                            <span class="fw-medium">#<?= $userData['id'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Role</span>
                            <span
                                class="badge bg-<?= $userData['role'] === 'admin' ? 'danger' : 'primary' ?>"><?= ucfirst($userData['role']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Status</span>
                            <?php if ($userData['is_confirmed']): ?>
                                <span class="badge bg-success">Verified</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Unverified</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Registered</span>
                            <span><?= date('M j, Y', strtotime($userData['created_at'])) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Last Updated</span>
                            <span><?= date('M j, Y', strtotime($userData['updated_at'])) ?></span>
                        </li>
                    </ul>

                    <?php if (isset($_SESSION['user_id']) && $userData['id'] !== $_SESSION['user_id']): ?>
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteUserModal<?= $userData['id'] ?>">
                                <i class="bi bi-trash me-1"></i> Delete User Account
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id']) && $userData['id'] !== $_SESSION['user_id']): ?>
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteUserModal<?= $userData['id'] ?>" tabindex="-1"
            aria-labelledby="deleteUserModalLabel<?= $userData['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel<?= $userData['id'] ?>">Confirm Delete User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to permanently delete the user
                            "<strong><?= htmlspecialchars($userData['name']) ?>
                                (<?= htmlspecialchars($userData['email']) ?>)</strong>"?</p>
                        <p class="text-danger">This action cannot be undone. All associated data for this user (like bids)
                            will also be deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="<?= BASE_URL ?>admin/delete-user?id=<?= $userData['id'] ?>" class="btn btn-danger">Delete
                            User Permanently</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>