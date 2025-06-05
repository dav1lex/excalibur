<!-- Hero  -->
<div class="bg-dark text-light text-center py-5">
    <div class="container">
        <h1 class="display-4 fw-bold">Welcome to NanoBid</h1>
        <p class="lead fs-4 mb-4">The simple, secure, and self-hosted solution for online auctions.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="<?= BASE_URL ?>auctions" class="btn btn-primary btn-lg px-4 gap-3">Browse Auctions</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>register" class="btn btn-outline-light btn-lg px-4">Register to Bid</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>user/dashboard" class="btn btn-outline-light btn-lg px-4">My Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- how come -->
<div class="container my-5">
    <h2 class="text-center mb-5 pb-2 border-bottom">A Simple & Transparent Process</h2>
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="mb-3">
                <i class="bi bi-person-plus-fill display-4 text-primary"></i>
            </div>
            <h3 class="h4">1. Register</h3>
            <p class="text-muted">Create your account to get started.</p>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <i class="bi bi-search display-4 text-primary"></i>
            </div>
            <h3 class="h4">2. Browse</h3>
            <p class="text-muted">Explore lots, place pre-bids or proxy-bids to let system bid for you.</p>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <i class="bi bi-trophy-fill display-4 text-primary"></i>
            </div>
            <h3 class="h4">3. Win & Collect</h3>
            <p class="text-muted">If you have the highest bid, you will win the lot.</p>
        </div>
    </div>
</div>

<div class="container-fluid bg-light py-5">
    <div class="container text-center">
        <h2 class="mb-3">Ready?</h2>
        <p class="lead text-muted mb-4">Join now.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>register" class="btn btn-primary btn-lg">Create a Free Account</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>auctions" class="btn btn-primary btn-lg">Explore Live Auctions</a>
        <?php endif; ?>
    </div>
</div>