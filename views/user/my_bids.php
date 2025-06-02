<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 mb-4 border-bottom pb-2">My Bids</h1>
            
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

    <?php if (!empty($winningBids)): ?>
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Won Items</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($winningBids as $bid): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <?php if (!empty($bid['image_path'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($bid['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($bid['lot_title']) ?>" style="height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light text-center py-3">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($bid['lot_title']) ?></h5>
                                        <p class="card-text">
                                            <strong>Auction:</strong> <?= htmlspecialchars($bid['auction_title']) ?><br>
                                            <strong>Your Winning Bid:</strong> $<?= number_format($bid['amount']) ?><br>
                                            <strong>Ended:</strong> <?= date('M j, Y', strtotime($bid['auction_end_date'])) ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-top-0">
                                        <a href="<?= BASE_URL ?>lots/view?id=<?= $bid['lot_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bid History</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($bids)): ?>
                        <div class="alert alert-info mb-0">
                            You haven't placed any bids yet. Browse our <a href="<?= BASE_URL ?>auctions" class="alert-link">auctions</a> to start bidding.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Lot</th>
                                        <th>Auction</th>
                                        <th>Your Bid</th>
                                        <th>Current Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bids as $bid): ?>
                                        <tr>
                                            <td><?= date('M j, Y, g:i A', strtotime($bid['placed_at'])) ?></td>
                                            <td><?= htmlspecialchars($bid['lot_title']) ?></td>
                                            <td><?= htmlspecialchars($bid['auction_title']) ?></td>
                                            <td>$<?= number_format($bid['amount']) ?></td>
                                            <td>$<?= number_format($bid['current_price']) ?></td>
                                            <td>
                                                <?php 
                                                $statusClass = 'bg-secondary';
                                                $statusText = 'Outbid';
                                                
                                                if ($bid['amount'] == $bid['current_price']) {
                                                    if ($bid['auction_status'] == 'ended') {
                                                        $statusClass = 'bg-success';
                                                        $statusText = 'Won';
                                                    } else {
                                                        $statusClass = 'bg-primary';
                                                        $statusText = 'Highest Bid';
                                                    }
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>lots/view?id=<?= $bid['lot_id'] ?>" class="btn btn-sm btn-outline-primary">View Lot</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 