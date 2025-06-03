<div class="bg-light py-5 mb-5 rounded-3 shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold"><?= SITE_NAME ?></h1>
                <p class="lead fs-3 mb-4">Discover unique treasures at competitive prices.</p>
                <p class="mb-4 fs-5 text-muted">Browse our latest auctions or register to start bidding on exclusive items.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>register" class="btn btn-primary btn-lg px-4 me-md-2">Register Now</a>
                        <a href="<?= BASE_URL ?>login" class="btn btn-outline-secondary btn-lg px-4">Login</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>auctions" class="btn btn-primary btn-lg px-4 me-md-2">Browse Auctions</a>
                        <a href="<?= BASE_URL ?>user/dashboard" class="btn btn-outline-primary btn-lg px-4">My Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4 d-none d-md-block text-center">
                <img src="<?= BASE_URL ?>public/img/auction-gavel.jpg" alt="Auction Gavel" class="img-fluid  shadow" style="max-width: 100%;">
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <h2 class="text-center mb-4 pb-2 border-bottom">Why Choose Our Platform</h2>
    
    <div class="row g-4 py-3">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-light">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="display-5 text-primary">⚡</span>
                    </div>
                    <h3 class="card-title h4">Easy Bidding</h3>
                    <p class="card-text">Place bids with ease on items you're interested in. Set maximum bids and let our system bid for you automatically.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-light">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="display-5 text-success">🔒</span>
                    </div>
                    <h3 class="card-title h4">Secure Transactions</h3>
                    <p class="card-text">Our platform ensures all bids are processed securely and accurately with real-time updates to all participants.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-light">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="display-5 text-warning">🔔</span>
                    </div>
                    <h3 class="card-title h4">Auction Notifications</h3>
                    <p class="card-text">Get notified when you're outbid or when an auction is about to end so you never miss out on an opportunity.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="mb-3">Ready to start bidding?</h2>
                <p class="lead mb-4">Join thousands of users who find unique items on our platform every day.</p>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>register" class="btn btn-primary btn-lg">Create an Account</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>auctions" class="btn btn-primary btn-lg">Explore Current Auctions</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 