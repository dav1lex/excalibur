<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h1 class="display-5 mb-0">My Bids</h1>
            <a href="<?= BASE_URL ?>user/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

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

    <?php if (empty($bids) && empty($winningBids)): ?>
        <div class="alert alert-info text-center py-4">
            <i class="bi bi-info-circle fs-3 mb-2 d-block"></i>
            <h4 class="mb-1">No bids placed yet</h4>
            <p class="mb-3">You haven't placed any bids yet.</p>
            <a href="<?= BASE_URL ?>auctions" class="btn btn-primary rounded-pill">Browse Auctions</a>
        </div>
    <?php else: ?>
        <?php
        // Group bids by auction and lot
        $bidsByAuction = [];
        $auctionStatuses = [];

        // Process all bids including winning bids
        $allBids = array_merge($bids, $winningBids);

        foreach ($allBids as $bid) {
            $auctionId = $bid['auction_id'] ?? 0;
            $lotId = $bid['lot_id'];

            if (!isset($bidsByAuction[$auctionId])) {
                $bidsByAuction[$auctionId] = [
                    'auction_title' => $bid['auction_title'],
                    'auction_status' => $bid['auction_status'] ?? 'ended',
                    'lots' => []
                ];
                $auctionStatuses[$auctionId] = $bid['auction_status'] ?? 'ended';
            }

            if (!isset($bidsByAuction[$auctionId]['lots'][$lotId])) {
                $bidsByAuction[$auctionId]['lots'][$lotId] = [
                    'lot_title' => $bid['lot_title'],
                    'lot_id' => $lotId,
                    'image_path' => $bid['image_path'],
                    'current_price' => $bid['current_price'],
                    'lot_number' => $bid['lot_number'] ?? '',
                    'bids' => []
                ];
            }

            // Avoid duplicate bids
            $bidExists = false;
            foreach ($bidsByAuction[$auctionId]['lots'][$lotId]['bids'] as $existingBid) {
                if ($existingBid['id'] == $bid['id']) {
                    $bidExists = true;
                    break;
                }
            }

            if (!$bidExists) {
                $bidsByAuction[$auctionId]['lots'][$lotId]['bids'][] = $bid;
            }
        }

        // sotr: live first, then ended
        $statusOrder = ['live' => 1, 'ended' => 2];
        uasort($bidsByAuction, function ($a, $b) use ($statusOrder) {
            return $statusOrder[$a['auction_status']] <=> $statusOrder[$b['auction_status']];
        });
        ?>

        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-light py-2">
                <ul class="nav nav-tabs card-header-tabs" id="bidTabs" role="tablist">
                    <?php
                    $activeStatuses = array_unique(array_values($auctionStatuses));
                    $statusLabels = [
                        'live' => '<i class="bi bi-broadcast me-1"></i>Live Auctions',
                        'ended' => '<i class="bi bi-archive me-1"></i>Past Auctions'
                    ];

                    $firstTab = true;
                    foreach ($activeStatuses as $status):
                        $itemCount = 0;
                        foreach ($bidsByAuction as $auction) {
                            if ($auction['auction_status'] === $status) {
                                $itemCount += count($auction['lots']);
                            }
                        }
                        ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $firstTab ? 'active' : '' ?>" id="<?= $status ?>-tab"
                                data-bs-toggle="tab" data-bs-target="#<?= $status ?>-pane" type="button" role="tab"
                                aria-controls="<?= $status ?>-pane" aria-selected="<?= $firstTab ? 'true' : 'false' ?>">
                                <?= $statusLabels[$status] ?>
                                <span class="badge bg-secondary rounded-pill ms-1"><?= $itemCount ?></span>
                            </button>
                        </li>
                        <?php $firstTab = false; ?>
                    <?php endforeach; ?>
            </ul>
        </div>
            <div class="card-body p-0">
                <div class="tab-content" id="bidTabsContent">
                    <?php 
                    $firstPane = true;
                    foreach ($activeStatuses as $currentStatus): ?>
                        <div class="tab-pane fade <?= $firstPane ? 'show active' : '' ?>" 
                             id="<?= $currentStatus ?>-pane" 
                             role="tabpanel" 
                             aria-labelledby="<?= $currentStatus ?>-tab">
                            
                            <?php 
                            // Filter auctions by current status
                            $filteredAuctions = array_filter($bidsByAuction, function($auction) use ($currentStatus) {
                                return $auction['auction_status'] === $currentStatus;
                            });
                            
                            foreach ($filteredAuctions as $auctionId => $auction): ?>
                                <div class="auction-section p-3 border-bottom">
                                    <h5 class="mb-3">
                                        <a href="<?= BASE_URL ?>auctions/<?= $auctionId ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($auction['auction_title']) ?>
                                        </a>
                                        <?php 
                                        $badgeClass = 'bg-secondary';
                                        $badgeText = 'Draft';
                                        
                                        if ($auction['auction_status'] === 'live') {
                                            $badgeClass = 'bg-success';
                                            $badgeText = 'Live';
                                        } elseif ($auction['auction_status'] === 'ended') {
                                            $badgeClass = 'bg-dark';
                                            $badgeText = 'Ended';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> ms-2"><?= $badgeText ?></span>
                                    </h5>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;"></th>
                                                    <th>Lot</th>
                                                    <th>Winning Bid</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($auction['lots'] as $lotId => $lot): 
                                                    // Get highest bid for this lot
                                                    $highestBid = null;
                                                    $isWinner = false;
                                                    
                                                    foreach ($lot['bids'] as $bid) {
                                                        if ($highestBid === null || $bid['amount'] > $highestBid['amount']) {
                                                            $highestBid = $bid;
                                                        }
                                                        
                                                        if ($bid['amount'] == $lot['current_price'] && $currentStatus === 'ended') {
                                                            $isWinner = true;
                                                        }
                                                    }
                                                    
                                                    if (!$highestBid) continue;
                                                ?>
                                                    <tr class="<?= $isWinner ? 'table-success' : '' ?>">
                                                        <td class="align-middle">
                                                            <div class="lot-image rounded bg-white border" style="width: 40px; height: 30px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                                <?php if (!empty($lot['image_path'])): ?>
                                                                    <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="img-fluid" alt="" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                                                <?php else: ?>
                                                                    <i class="bi bi-image text-secondary" style="font-size: 1rem; opacity: 0.5;"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            <a href="<?= BASE_URL ?>auctions/<?= $auctionId ?>/lots/<?= $lotId ?>" class="text-decoration-none fw-medium">
                                                                <?= htmlspecialchars($lot['lot_number'] ?: $lot['lot_title']) ?>
                                                            </a>
                                                        </td>
                                                        
                                                        <td class="align-middle"><?= number_format($lot['current_price']) ?> €</td>
                                                        <td class="align-middle">
                                                            <?php 
                                                            if ($highestBid['amount'] == $lot['current_price']) {
                                                                if ($currentStatus === 'ended') {
                                                                    echo '<span class="badge bg-success">Won</span>';
                                                                } else {
                                                                    echo '<span class="badge bg-primary">Highest</span>';
                                                                }
                                                            } else {
                                                                echo '<span class="badge bg-secondary">Outbid</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="align-middle"><?= date('M j, g:i A', strtotime($highestBid['placed_at'])) ?></td>
                                                        <td class="align-middle text-end">
                                                            <a href="<?= BASE_URL ?>auctions/<?= $auctionId ?>/lots/<?= $lotId ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    
                                                    <?php if (count($lot['bids']) > 1): ?>
                                                    <tr class="bid-history-row">
                                                        <td colspan="7" class="p-0">
                                                            <div class="bid-history bg-light p-2" style="display: none;">
                                                                <h6 class="mb-2 ps-2 small fw-bold">Bid History</h6>
                                                                <table class="table table-sm mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Amount</th>
                                                                            <th>Max</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php 
                                                                        // Sort bids by date descending
                                                                        usort($lot['bids'], function($a, $b) {
                                                                            return strtotime($b['placed_at']) - strtotime($a['placed_at']);
                                                                        });
                                                                        
                                                                        foreach ($lot['bids'] as $bid): 
                                                                            if ($bid['id'] == $highestBid['id']) continue; // Skip the highest bid as it's shown in the main row
                                                                        ?>
                                                                            <tr>
                                                                                <td><?= date('M j, g:i A', strtotime($bid['placed_at'])) ?></td>
                                                                                <td class="fw-medium"><?= number_format($bid['amount']) ?> €</td>
                                                                                <td>
                                                                                    <?php if (!empty($bid['max_amount'])): ?>
                                                                                        <span class="text-muted"><?= number_format($bid['max_amount']) ?> €</span>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted">—</span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge bg-secondary">Outbid</span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="table-light">
                                                        <td colspan="7" class="text-center p-1">
                                                            <button type="button" class="btn btn-sm btn-link text-muted toggle-bid-history">
                                                                <span class="show-text"><i class="bi bi-chevron-down"></i> Show Bid History (<?= count($lot['bids']) - 1 ?>)</span>
                                                                <span class="hide-text" style="display: none;"><i class="bi bi-chevron-up"></i> Hide Bid History</span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <?php if ($currentStatus === 'ended'): ?>
                                        <?php
                                        // Calculate payment summary for won items
                                        $wonItems = 0;
                                        $totalHammerPrice = 0;
                                        
                                        foreach ($auction['lots'] as $lot) {
                                            // Check if user has won this lot
                                            $isWon = false;
                                            foreach ($lot['bids'] as $bid) {
                                                if ($bid['amount'] == $lot['current_price']) {
                                                    $isWon = true;
                                                    break;
                                                }
                                            }
                                            
                                            if ($isWon) {
                                                $wonItems++;
                                                $totalHammerPrice += $lot['current_price'];
                                            }
                                        }
                                        
                                        // Only show payment summary if user has won items
                                        if ($wonItems > 0):
                                            $premiumAmount = $totalHammerPrice * 0.15;
                                            $totalPayment = $totalHammerPrice + $premiumAmount;
                                        ?>
                                        <div class="payment-summary-container mt-3">
                                            <div class="d-flex justify-content-between align-items-center bg-light p-3 border rounded">
                                                <div>
                                                    <span class="fw-bold"><i class="bi bi-credit-card me-2"></i> Pay <span class="text-primary"><?= number_format($totalPayment) ?> €</span></span>
                                                    <span class="text-muted ms-2">(<?= $wonItems ?> won <?= $wonItems > 1 ? 'items' : 'item' ?>)</span>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary me-2 toggle-payment-details">
                                                        <span class="show-details"><i class="bi bi-chevron-down"></i> View Details</span>
                                                        <span class="hide-details" style="display: none;"><i class="bi bi-chevron-up"></i> Hide Details</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="payment-details bg-light p-3 border-start border-end border-bottom rounded-bottom" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-3">Payment Details</h6>
                                                        <p class="mb-1">Won Items: <span class="fw-medium"><?= $wonItems ?></span></p>
                                                        <p class="mb-1">Total Hammer Price: <span class="fw-medium"><?= number_format($totalHammerPrice) ?> €</span></p>
                                                        <p class="mb-1">Buyer's Premium (15%): <span class="fw-medium"><?= number_format($premiumAmount) ?> €</span></p>
                                                        <p class="mb-0 fw-bold">Total Payment Due: <span class="text-primary"><?= number_format($totalPayment) ?> €</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-3">Won Items</h6>
                                                        <ul class="list-unstyled">
                                                            <?php 
                                                            $count = 0;
                                                            foreach ($auction['lots'] as $lot) {
                                                                $isWon = false;
                                                                foreach ($lot['bids'] as $bid) {
                                                                    if ($bid['amount'] == $lot['current_price']) {
                                                                        $isWon = true;
                                                                        break;
                                                                    }
                                                                }
                                                                
                                                                if ($isWon) {
                                                                    $count++;
                                                                    echo '<li class="mb-1">' . htmlspecialchars($lot['lot_number'] ?: $lot['lot_title']) . ': <span class="fw-medium">' . number_format($lot['current_price']) . ' €</span></li>';
                                                                    
                                                                    // Limit to 5 items to avoid too long list
                                                                    if ($count >= 5 && count($auction['lots']) > 5) {
                                                                        echo '<li class="text-muted">+ ' . ($wonItems - 5) . ' more items</li>';
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <a href="#" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-credit-card me-1"></i> Pay Now
                                                    </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php $firstPane = false; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 500;
    }
    
    .auction-section:not(:last-child) {
        border-bottom: 1px solid #dee2e6;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle bid history
    const toggleButtons = document.querySelectorAll('.toggle-bid-history');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr').previousElementSibling;
            const bidHistory = row.querySelector('.bid-history');
            const showText = this.querySelector('.show-text');
            const hideText = this.querySelector('.hide-text');
            
            if (bidHistory.style.display === 'none') {
                bidHistory.style.display = 'block';
                showText.style.display = 'none';
                hideText.style.display = 'inline';
            } else {
                bidHistory.style.display = 'none';
                showText.style.display = 'inline';
                hideText.style.display = 'none';
            }
        });
    });

    // Toggle payment details
    const togglePaymentDetails = document.querySelectorAll('.toggle-payment-details');
    togglePaymentDetails.forEach(button => {
        button.addEventListener('click', function() {
            const details = this.closest('.payment-summary-container').querySelector('.payment-details');
            const showDetails = this.querySelector('.show-details');
            const hideDetails = this.querySelector('.hide-details');
            
            if (details.style.display === 'none') {
                details.style.display = 'block';
                showDetails.style.display = 'none';
                hideDetails.style.display = 'inline';
            } else {
                details.style.display = 'none';
                showDetails.style.display = 'inline';
                hideDetails.style.display = 'none';
            }
        });
    });
});
</script> 