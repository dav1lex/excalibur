<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Admin Dashboard</h1>
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

<!-- Auction Total Value Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Auction Value Calculator</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="auction_id" class="form-label">Select Auction</label>
                        <select name="auction_id" id="auction_id" class="form-select">
                            <option value="">-- Select an auction --</option>
                            <?php foreach ($auctions as $auction): ?>
                                <option value="<?= $auction['id'] ?>" <?= isset($_GET['auction_id']) && $_GET['auction_id'] == $auction['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($auction['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Calculate Total</button>
                    </div>
                </form>

                <?php if (isset($auctionTotal) && isset($selectedAuction)): ?>
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <p class="mb-1"><strong>Current Value:</strong> <span
                                    class="fs-4 fw-bold"><?= number_format($auctionTotal) ?>€</span></p>
                            <p class="mb-0"><i class="bi bi-info-circle me-2"></i><small class="text-muted">Total of visible
                                    current prices of all lots.</small></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Total Users</h5>
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded p-2">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2"><?= $stats['totalUsers'] ?></div>
                <p class="text-muted mb-0">Registered accounts</p>
                <a href="<?= BASE_URL ?>admin/users" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-primary text-white p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-plus me-2"></i>
                    <span>Manage Users</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Total Auctions</h5>
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded p-2">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2"><?= $stats['totalAuctions'] ?></div>
                <p class="text-muted mb-0">Active & completed</p>
                <a href="<?= BASE_URL ?>admin/auctions" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-success text-white p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-plus me-2"></i>
                    <span>Manage Auctions</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Total Lots</h5>
                    <div class="icon-shape bg-info bg-opacity-10 text-info rounded p-2">
                        <i class="bi bi-collection"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2"><?= $stats['totalLots'] ?></div>
                <p class="text-muted mb-0">Listed items</p>
                <a href="<?= BASE_URL ?>admin/lots" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-info text-white p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-bag-plus me-2"></i>
                    <span>Manage Lots</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-muted mb-0">Total Bids</h5>
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded p-2">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold mb-2"><?= $stats['totalBids'] ?></div>
                <p class="text-muted mb-0">Placed bids</p>
                <a href="<?= BASE_URL ?>admin/bids" class="stretched-link"></a>
            </div>
            <div class="card-footer bg-warning text-dark p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-graph-up me-2"></i>
                    <span>Manage Bids</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>admin/users"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-primary text-white mx-auto mb-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h5>Manage Users</h5>
                                <p class="text-muted mb-0">View, edit, and manage user accounts</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>admin/auctions"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-success text-white mx-auto mb-3">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <h5>Manage Auctions</h5>
                                <p class="text-muted mb-0">View and manage all auctions</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>admin/lots"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-info text-white mx-auto mb-3">
                                    <i class="bi bi-collection"></i>
                                </div>
                                <h5>Manage Lots</h5>
                                <p class="text-muted mb-0">View and manage all auction items</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>admin/bids"
                            class="card bg-light border-0 text-center h-100 p-3 text-decoration-none">
                            <div class="py-3">
                                <div class="icon-circle bg-dark text-white mx-auto mb-3">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <h5>Manage Bids</h5>
                                <p class="text-muted mb-0">View and manage all bids</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Info</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">PHP Version</span>
                        <span class="fw-medium"><?= phpversion() ?></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Current Time</span>
                        <span class="fw-medium"><?= date('Y-m-d H:i:s') ?></span>
                    </li>
                </ul>
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