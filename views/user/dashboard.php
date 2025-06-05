<div class="row mb-4">
    <div class="col">
        <h1 class="display-5 mb-3 border-bottom pb-2">Dashboard</h1>


        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Active Bids</h5>
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded p-2">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2"><?= $stats['activeBids'] ?></div>
                <p class="text-muted mb-0">Unique lots you're bidding on</p>
                <a href="<?= BASE_URL ?>user/bids" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-primary text-white p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list-check me-2"></i>
                    <span>View All Bids</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Won Items</h5>
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded p-2">
                        <i class="bi bi-trophy"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2">
                    <?php if (isset($stats['wonItemsValue']) && $stats['wonItemsValue'] > 0): ?>
                        <?= number_format($stats['wonItemsValue']) ?> €
                    <?php else: ?>
                        <?= $stats['wonItems'] ?>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-0">
                    <?php if (isset($stats['wonItemsValue']) && $stats['wonItemsValue'] > 0): ?>
                        Total value of won items
                    <?php else: ?>
                        Items you've won
                    <?php endif; ?>
                </p>
                <a href="<?= BASE_URL ?>user/bids?status=won" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-success text-white p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-emoji-smile me-2"></i>
                    <span>View Won Items</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Watchlist</h5>
                    <div class="icon-shape bg-info bg-opacity-10 text-info rounded p-2">
                        <i class="bi bi-bookmark-heart"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2"><?= $stats['watchlist'] ?></div>
                <p class="text-muted mb-0">Saved items</p>
                <a href="<?= BASE_URL ?>user/watchlist" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-info text-white p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-eye me-2"></i>
                    <span>View Watchlist</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>My Account</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light rounded-circle p-3 me-3">
                        <i class="bi bi-person fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-1"><?= htmlspecialchars($userData['name']) ?></h5>
                        <p class="text-muted mb-0"><?= htmlspecialchars($userData['email']) ?></p>
                    </div>
                </div>

                <div class="list-group list-group-flush mb-3">
                    <div class="list-group-item bg-light px-0">
                        <div class="row">
                            <div class="col-md-4 fw-medium text-muted">Member Since</div>
                            <div class="col-md-8"><?= date('F j, Y', strtotime($userData['created_at'])) ?></div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="<?= BASE_URL ?>user/profile" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?= BASE_URL ?>auctions"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-primary text-white mx-auto mb-3">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h5>Browse Auctions</h5>
                                <p class="text-muted mb-0">Find items to bid on</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= BASE_URL ?>user/bids"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-success text-white mx-auto mb-3">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <h5>My Bids</h5>
                                <p class="text-muted mb-0">Track your bidding activity</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-12">
                        <a href="<?= BASE_URL ?>user/watchlist"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-warning text-dark mx-auto mb-3">
                                    <i class="bi bi-bookmark-heart"></i>
                                </div>
                                <h5>My Watchlist</h5>
                                <p class="text-muted mb-0">View saved items you're interested in</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-circle i {
        font-size: 1.5rem;
    }

    .icon-shape {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-shape i {
        font-size: 1.25rem;
    }
</style>