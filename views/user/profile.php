<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-3 border-bottom pb-2">Edit Profile</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

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

        <div class="col-md-6 text-end d-flex align-items-center justify-content-end">
            <a href="<?= BASE_URL ?>user/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-8 mr-auto">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>My Profile</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= BASE_URL ?>user/update-profile" method="post">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?= htmlspecialchars($userData['name']) ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($userData['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-light bg-light rounded-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bi bi-key me-2"></i>Change Password</h5>
                            </div>
                            <div class="card-body p-3">
                                <p class="text-muted small mb-3">Leave these fields empty if you don't want to change
                                    your password</p>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-muted py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-calendar3 me-1"></i>
                        <small>Member since: <?= date('F j, Y', strtotime($userData['created_at'])) ?></small>
                    </div>
                    <?php if ($userData['is_confirmed']): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Not
                            Verified</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>