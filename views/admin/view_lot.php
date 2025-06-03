<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-3 border-bottom pb-2">
            <i class="bi bi-box me-2"></i>Lot Details
        </h1>
    </div>
    <div class="col-md-6 text-end align-self-center">
        <a href="<?= BASE_URL ?>admin/lots<?= isset($_GET['auction_id']) ? '?auction_id=' . $_GET['auction_id'] : '' ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Lots
        </a>
        <a href="<?= BASE_URL ?>lots/edit?id=<?= $lot['id'] ?>" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit Lot
        </a>
    </div>
</div>

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

<div class="row g-4">
    <!-- Left column with lot details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Lot Information</h5>
                    <?php 
                    $badgeClass = 'bg-secondary';
                    $badgeText = 'Draft';
                    
                    if ($auction['status'] === 'upcoming') {
                        $badgeClass = 'bg-info';
                        $badgeText = 'Upcoming';
                    } elseif ($auction['status'] === 'live') {
                        $badgeClass = 'bg-success';
                        $badgeText = 'Live';
                    } elseif ($auction['status'] === 'ended') {
                        $badgeClass = 'bg-dark';
                        $badgeText = 'Ended';
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-5">
                        <?php if (!empty($lot['image_path'])): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($lot['title']) ?>">
                        <?php else: ?>
                            <div class="bg-light text-center py-5 rounded">
                                <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                                <p class="text-muted">No image available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-7">
                        <h2 class="h4 mb-3"><?= htmlspecialchars($lot['title']) ?></h2>
                        
                        <div class="mb-3">
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Lot Number:</div>
                                <div class="col-md-7 fw-medium"><?= htmlspecialchars($lot['lot_number']) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Auction:</div>
                                <div class="col-md-7">
                                    <a href="<?= BASE_URL ?>auctions/view?id=<?= $auction['id'] ?>"><?= htmlspecialchars($auction['title']) ?></a>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Starting Price:</div>
                                <div class="col-md-7"><?= number_format($lot['starting_price']) ?>€</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Current Price:</div>
                                <div class="col-md-7 fw-bold text-primary"><?= number_format($lot['current_price']) ?>€</div>
                            </div>
                            <?php if ($lot['reserve_price']): ?>
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Reserve Price:</div>
                                <div class="col-md-7"><?= number_format($lot['reserve_price']) ?>€</div>
                            </div>
                            <?php endif; ?>
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Created:</div>
                                <div class="col-md-7"><?= date('M j, Y, g:i A', strtotime($lot['created_at'])) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 text-muted">Last Updated:</div>
                                <div class="col-md-7"><?= date('M j, Y, g:i A', strtotime($lot['updated_at'])) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h5>Description</h5>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($lot['description'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Bid History</h5>
            </div>
            <div class="card-body">
                <?php if (empty($bids)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No bids have been placed on this lot yet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Bid ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Max Amount</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bids as $bid): ?>
                                    <tr <?= isset($winner) && $winner['id'] === $bid['id'] ? 'class="table-success"' : '' ?>>
                                        <td><?= $bid['id'] ?></td>
                                        <td><?= htmlspecialchars($bid['user_name']) ?></td>
                                        <td class="fw-bold"><?= number_format($bid['amount']) ?>€</td>
                                        <td><?= $bid['max_amount'] ? number_format($bid['max_amount']) . '€' : 'N/A' ?></td>
                                        <td><?= date('M j, Y, g:i A', strtotime($bid['placed_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Right column with auction details and winner info -->
    <div class="col-lg-4">
        <!-- Auction Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Auction Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-5 text-muted">Status:</div>
                    <div class="col-md-7">
                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5 text-muted">Start Date:</div>
                    <div class="col-md-7"><?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5 text-muted">End Date:</div>
                    <div class="col-md-7"><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></div>
                </div>
                
                <?php if ($auction['status'] === 'live'): ?>
                    <div class="alert alert-success mt-3 mb-0">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-clock-history fs-3"></i>
                            </div>
                            <div>
                                <strong>Auction is LIVE</strong><br>
                                <span>Ends in: </span>
                                <div class="auction-timer" data-end="<?= $auction['end_date'] ?>">
                                    <span class="days">00</span>d
                                    <span class="hours">00</span>h
                                    <span class="minutes">00</span>m
                                    <span class="seconds">00</span>s
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Winner Information -->
        <?php if ($auction['status'] === 'ended'): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Winner Information</h5>
                </div>
                <div class="card-body">
                    <?php if ($winner): ?>
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i> This lot has been sold.
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-5 text-muted">Winner:</div>
                            <div class="col-md-7 fw-bold"><?= htmlspecialchars($winner['user_name']) ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-5 text-muted">Winning Bid:</div>
                            <div class="col-md-7 fw-bold"><?= number_format($winner['amount']) ?>€</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-5 text-muted">Bid Time:</div>
                            <div class="col-md-7"><?= date('M j, Y, g:i A', strtotime($winner['placed_at'])) ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-5 text-muted">Reserve Met:</div>
                            <div class="col-md-7">
                                <?php if (!$lot['reserve_price'] || $winner['amount'] >= $lot['reserve_price']): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">No</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> This lot did not receive any bids.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Admin Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>lots/edit?id=<?= $lot['id'] ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Lot
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteLotModal">
                        <i class="bi bi-trash me-2"></i>Delete Lot
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteLotModal" tabindex="-1" aria-labelledby="deleteLotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLotModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the lot "<?= htmlspecialchars($lot['title']) ?>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="<?= BASE_URL ?>lots/delete?id=<?= $lot['id'] ?>" class="btn btn-danger">Delete Lot</a>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown timer for live auctions
document.addEventListener('DOMContentLoaded', function() {
    const timers = document.querySelectorAll('.auction-timer');
    
    timers.forEach(function(timer) {
        const endTime = new Date(timer.dataset.end).getTime();
        
        const updateTimer = function() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                timer.innerHTML = "Auction has ended";
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