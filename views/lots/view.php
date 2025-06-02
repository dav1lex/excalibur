<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>auctions">Auctions</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>auctions/view?id=<?= $auction['id'] ?>"><?= htmlspecialchars($auction['title']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($lot['title']) ?></li>
                </ol>
            </nav>
            <h1 class="display-5 mb-2"><?= htmlspecialchars($lot['title']) ?></h1>
            <p class="text-muted">Lot #<?= htmlspecialchars($lot['lot_number']) ?></p>
            
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
        <div class="col-md-4 text-end">
            <?php 
            $badgeClass = 'bg-secondary';
            $badgeText = 'Draft';
            
            if ($auction['auction_status'] === 'upcoming') {
                $badgeClass = 'bg-info';
                $badgeText = 'Upcoming';
            } elseif ($auction['auction_status'] === 'live') {
                $badgeClass = 'bg-success';
                $badgeText = 'Live';
            } elseif ($auction['auction_status'] === 'ended') {
                $badgeClass = 'bg-dark';
                $badgeText = 'Ended';
            }
            ?>
            <span class="badge <?= $badgeClass ?> fs-6 mb-2"><?= $badgeText ?></span>
            <div class="d-block">
                <?php if ($auction['auction_status'] === 'upcoming'): ?>
                    <div class="text-muted">
                        Starts: <?= date('M j, Y, g:i A', strtotime($auction['auction_start_date'])) ?>
                    </div>
                <?php elseif ($auction['auction_status'] === 'live'): ?>
                    <div class="text-danger fw-bold">
                        Ends: <?= date('M j, Y, g:i A', strtotime($auction['auction_end_date'])) ?>
                        <div class="auction-timer" data-end="<?= $auction['auction_end_date'] ?>">
                            <span class="days">00</span>d
                            <span class="hours">00</span>h
                            <span class="minutes">00</span>m
                            <span class="seconds">00</span>s
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-muted">
                        Ended: <?= date('M j, Y, g:i A', strtotime($auction['auction_end_date'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <?php if (!empty($lot['image_path'])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($lot['title']) ?>">
            <?php else: ?>
                <div class="bg-light text-center py-5 rounded">
                    <i class="bi bi-image text-muted" style="font-size: 10rem;"></i>
                    <p class="text-muted">No image available</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bidding Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6 fw-bold">Starting Price:</div>
                        <div class="col-6">$<?= number_format($lot['starting_price']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6 fw-bold">Current Bid:</div>
                        <div class="col-6 text-primary fw-bold">$<?= number_format($lot['current_price']) ?></div>
                    </div>
                    
                    <?php if (isset($user)): ?>
                        <?php if ($inWatchlist): ?>
                            <form action="<?= BASE_URL ?>watchlist/remove/<?= $lot['id'] ?>" method="post" class="mb-3">
                                <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-bookmark-dash"></i> Remove from Watchlist
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>watchlist/add/<?= $lot['id'] ?>" method="post" class="mb-3">
                                <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-bookmark-plus"></i> Add to Watchlist
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php else: // User not logged in ?>
                        <a href="<?= BASE_URL ?>login?redirect=<?= urlencode(CURRENT_URL) ?>" class="btn btn-outline-secondary w-100 mb-3">
                            <i class="bi bi-bookmark-plus"></i> Login to Add to Watchlist
                        </a>
                    <?php endif; ?>

                    <?php if ($auction['auction_status'] === 'live'): ?>
                        <hr>
                        <?php if (isset($user)): ?>
                            <form action="<?= BASE_URL ?>bids/place" method="post" class="mt-3">
                                <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                                <div class="mb-3">
                                    <label for="bid_amount" class="form-label">Your Bid Amount ($)</label>
                                    <input type="number" class="form-control" id="bid_amount" name="bid_amount" min="<?= $lot['current_price'] + 1 ?>" value="<?= $lot['current_price'] + 1 ?>" required>
                                    <div class="form-text">Minimum bid: $<?= number_format($lot['current_price'] + 1) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label for="max_amount" class="form-label">Maximum Bid Amount (Optional)</label>
                                    <input type="number" class="form-control" id="max_amount" name="max_amount" min="<?= $lot['current_price'] + 1 ?>">
                                    <div class="form-text">Set a maximum amount for proxy bidding</div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Place Bid</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Please <a href="<?= BASE_URL ?>login" class="alert-link">login</a> to place a bid.
                            </div>
                        <?php endif; ?>
                    <?php elseif ($auction['auction_status'] === 'upcoming'): ?>
                        <div class="alert alert-info">
                            Bidding will open when the auction starts.
                        </div>
                        
                        <?php if (isset($user)): ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-secondary">
                            This auction has ended.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($lot['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($bids)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bid History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Bidder</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bids as $bid): ?>
                                    <tr>
                                        <td><?= date('M j, Y, g:i A', strtotime($bid['placed_at'])) ?></td>
                                        <td>
                                            <?php 
                                            // Show partial name for privacy
                                            $name_parts = explode(' ', $bid['user_name']);
                                            $first_name = $name_parts[0];
                                            $last_initial = !empty($name_parts[1]) ? substr($name_parts[1], 0, 1) . '.' : '';
                                            echo $first_name . ' ' . $last_initial;
                                            ?>
                                        </td>
                                        <td>$<?= number_format($bid['amount']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($relatedLots)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-3">Other Lots in this Auction</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($relatedLots as $relatedLot): ?>
                    <div class="col">
                        <div class="card h-100 card-auction">
                            <?php if (!empty($relatedLot['image_path'])): ?>
                                <img src="<?= BASE_URL . htmlspecialchars($relatedLot['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($relatedLot['title']) ?>" style="height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light text-center py-3">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title small"><?= htmlspecialchars($relatedLot['title']) ?></h5>
                                <p class="card-text small">
                                    <strong>Current Bid:</strong> $<?= number_format($relatedLot['current_price']) ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-top-0 p-2">
                                <a href="<?= BASE_URL ?>lots/view?id=<?= $relatedLot['id'] ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Countdown timer for live auctions
document.addEventListener('DOMContentLoaded', function() {
    const timers = document.querySelectorAll('.auction-timer');
    
    timers.forEach(timer => {
        const endTime = new Date(timer.dataset.end).getTime();
        
        const updateTimer = function() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                timer.innerHTML = 'Auction has ended';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            timer.querySelector('.days').textContent = String(days).padStart(2, '0');
            timer.querySelector('.hours').textContent = String(hours).padStart(2, '0');
            timer.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
            timer.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
        };
        
        updateTimer();
        setInterval(updateTimer, 1000);
    });
});
</script> 