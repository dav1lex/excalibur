<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 mb-4 border-bottom pb-2">My Watchlist</h1>
            
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
        <div class="col">
            <?php if (empty($watchlist)): ?>
                <div class="alert alert-info">
                    <p>Your watchlist is empty. Browse our <a href="<?= BASE_URL ?>auctions" class="alert-link">auctions</a> and add items to your watchlist.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($watchlist as $item): ?>
                        <div class="col">
                            <div class="card h-100">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($item['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light text-center py-5">
                                        <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                                    <p class="card-text">
                                        <strong>Lot #:</strong> <?= htmlspecialchars($item['lot_number']) ?><br>
                                        <strong>Auction:</strong> <?= htmlspecialchars($item['auction_title']) ?><br>
                                        <strong>Current Bid:</strong> $<?= number_format($item['current_price']) ?>
                                    </p>
                                    
                                    <?php 
                                    $badgeClass = 'bg-secondary';
                                    $badgeText = 'Draft';
                                    
                                    if ($item['auction_status'] === 'upcoming') {
                                        $badgeClass = 'bg-info';
                                        $badgeText = 'Upcoming';
                                    } elseif ($item['auction_status'] === 'live') {
                                        $badgeClass = 'bg-success';
                                        $badgeText = 'Live';
                                    } elseif ($item['auction_status'] === 'ended') {
                                        $badgeClass = 'bg-dark';
                                        $badgeText = 'Ended';
                                    }
                                    ?>
                                    
                                    <div class="mb-3">
                                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                        
                                        <?php if ($item['auction_status'] === 'live'): ?>
                                            <div class="small text-danger mt-1">
                                                Ends: <?= date('M j, Y, g:i A', strtotime($item['end_date'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex justify-content-between">
                                        <a href="<?= BASE_URL ?>auctions/<?= $item['auction_id'] ?>/lots/<?= $item['lot_id'] ?>" class="btn btn-sm btn-primary">View Lot</a>
                                        <form action="<?= BASE_URL ?>watchlist/remove/<?= $item['lot_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this item from your watchlist?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 