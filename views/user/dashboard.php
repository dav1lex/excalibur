<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 mb-4 border-bottom pb-2">My Dashboard</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Active Bids</h5>
                <div class="display-4 my-3"><?= $stats['activeBids'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>user/bids" class="text-white stretched-link">View All Bids</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Won Items</h5>
                <div class="display-4 my-3"><?= $stats['wonItems'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>user/bids?status=won" class="text-white stretched-link">View Won Items</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Watchlist</h5>
                <div class="display-4 my-3"><?= $stats['watchlist'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>user/watchlist" class="text-white stretched-link">View Watchlist</a></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">My Account</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Name:</div>
                    <div class="col-md-8"><?= htmlspecialchars($userData['name']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Email:</div>
                    <div class="col-md-8"><?= htmlspecialchars($userData['email']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Member Since:</div>
                    <div class="col-md-8"><?= date('F j, Y', strtotime($userData['created_at'])) ?></div>
                </div>
                <div class="text-end mt-3">
                    <a href="<?= BASE_URL ?>user/profile" class="btn btn-outline-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="<?= BASE_URL ?>auctions" class="list-group-item list-group-item-action d-flex align-items-center">
                        <span class="me-3 text-primary">🔍</span>Browse Current Auctions
                    </a>
                    <a href="<?= BASE_URL ?>user/bids" class="list-group-item list-group-item-action d-flex align-items-center">
                        <span class="me-3 text-success">💰</span>My Bids
                    </a>
                    <a href="<?= BASE_URL ?>user/watchlist" class="list-group-item list-group-item-action d-flex align-items-center">
                        <span class="me-3 text-warning">⭐</span>My Watchlist
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 