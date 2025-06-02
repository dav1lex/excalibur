<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 mb-4 border-bottom pb-2">Admin Dashboard</h1>
        
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
    <div class="col-md-6 col-lg-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Users</h5>
                <div class="display-4 my-3"><?= $stats['totalUsers'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>admin/users" class="text-white stretched-link">Manage Users</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Auctions</h5>
                <div class="display-4 my-3"><?= $stats['totalAuctions'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>admin/auctions" class="text-white stretched-link">Manage Auctions</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Lots</h5>
                <div class="display-4 my-3"><?= $stats['totalLots'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>admin/lots" class="text-white stretched-link">Manage Lots</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Bids</h5>
                <div class="display-4 my-3"><?= $stats['totalBids'] ?></div>
                <p class="card-text mt-auto"><a href="<?= BASE_URL ?>admin/bids" class="text-dark stretched-link">Manage Bids</a></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="<?= BASE_URL ?>admin/users" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Manage Users
                        <span class="badge bg-primary rounded-pill"><?= $stats['totalUsers'] ?></span>
                    </a>
                    <a href="<?= BASE_URL ?>admin/auctions" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Manage Auctions
                        <span class="badge bg-success rounded-pill"><?= $stats['totalAuctions'] ?></span>
                    </a>
                    <a href="<?= BASE_URL ?>admin/lots" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Manage Lots
                        <span class="badge bg-info rounded-pill"><?= $stats['totalLots'] ?></span>
                    </a>
                    <a href="<?= BASE_URL ?>auctions/create" class="list-group-item list-group-item-action">
                        Create New Auction
                    </a>
                    <a href="<?= BASE_URL ?>lots/create" class="list-group-item list-group-item-action">
                        Add New Lot
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 