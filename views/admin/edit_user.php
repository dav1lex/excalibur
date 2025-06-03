<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="display-5 mb-4 border-bottom pb-2">Edit User</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASE_URL ?>admin/users" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Edit User #<?= $userData['id'] ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>admin/update-user" method="post">
                        <input type="hidden" name="id" value="<?= $userData['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($userData['name']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Leave blank to keep current password</div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Registered</label>
                                <p class="form-control-plaintext"><?= date('F j, Y, g:i a', strtotime($userData['created_at'])) ?></p>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Last Updated</label>
                                <p class="form-control-plaintext"><?= date('F j, Y, g:i a', strtotime($userData['updated_at'])) ?></p>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                    <?php if (isset($_SESSION['user_id']) && $userData['id'] !== $_SESSION['user_id']): ?>
                    <hr class="my-4">
                    <div class="text-center">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $userData['id'] ?>">
                            <i class="bi bi-trash"></i> Delete User Account
                        </button>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['user_id']) && $userData['id'] !== $_SESSION['user_id']): ?>
<!-- Delete  Modal -->
<div class="modal fade" id="deleteUserModal<?= $userData['id'] ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel<?= $userData['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel<?= $userData['id'] ?>">Confirm Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete the user "<strong><?= htmlspecialchars($userData['name']) ?> (<?= htmlspecialchars($userData['email']) ?>)</strong>"?</p>
                <p class="text-danger">This action cannot be undone. All associated data for this user (like bids) will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="<?= BASE_URL ?>admin/delete-user?id=<?= $userData['id'] ?>" class="btn btn-danger">Delete User Permanently</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?> 