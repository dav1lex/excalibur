<div class="container py-4">
    <!-- Breadcrumbs and Messages -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>auctions">Auctions</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>"><?= htmlspecialchars($auction['title']) ?></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($lot['title']) ?></li>
                </ol>
            </nav>

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

    <!-- Lot Header -->
    <div class="row mb-4">
        <div class="col-md-12 card">
            <div class="d-flex justify-content-between align-items-center py-2">
                <div>
                    <h1 class="h2 mb-1"><?= htmlspecialchars($lot['title']) ?></h1>
                    <p class="text-success fw-bold mb-0">#<?= htmlspecialchars($lot['lot_number']) ?></p>
                </div>
                <div class="text-end">
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
                    <span class="badge <?= $badgeClass ?> fs-6 mb-2"><?= $badgeText ?></span>

                    <?php if ($auction['status'] === 'upcoming'): ?>
                        <div class="text-muted small">
                            <i class="bi bi-calendar-event me-1"></i> Starts:
                            <?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?>
                        </div>
                    <?php elseif ($auction['status'] === 'live'): ?>
                        <div class="text-danger fw-bold small">
                            <i class="bi bi-alarm me-1"></i> Ends:
                            <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted small">
                            <i class="bi bi-calendar-check me-1"></i> Ended:
                            <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Image and Bidding -->
    <div class="row mb-4">
        <!-- Image Column -->
        <div class="col-md-7 mb-4 mb-md-0">
            <div class="card border-0">
                <div class="card-body p-0">
                    <?php if (!empty($lot['image_path'])): ?>
                        <div class="image-container text-center bg-light rounded p-3"
                            style="max-height: 500px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="img-fluid"
                                alt="<?= htmlspecialchars($lot['title']) ?>"
                                style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                    <?php else: ?>
                        <div class="bg-light text-center py-5 rounded">
                            <i class="bi bi-image text-muted" style="font-size: 8rem;"></i>
                            <p class="text-muted mt-3">No image available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description Card -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><?= nl2br(htmlspecialchars($lot['description'])) ?></p>
                </div>
            </div>

            <!-- Bidd histo -->
            <?php if (!empty($bids)): ?>
                <div class="card my-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Bid History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Bidder</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bids as $bid): ?>
                                        <tr>
                                            <td><?= date('M j, g:i A', strtotime($bid['placed_at'])) ?></td>
                                            <td>
                                                <?php
                                                // Show partial name for privacy
                                                $name_parts = explode(' ', $bid['user_name']);
                                                $first_name = $name_parts[0];
                                                $last_initial = !empty($name_parts[1]) ? substr($name_parts[1], 0, 1) . '.' : '';
                                                echo $first_name . ' ' . $last_initial;
                                                ?>
                                            </td>
                                            <td class="fw-bold">$<?= number_format($bid['amount']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bidding Column -->
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Bidding Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <span class="fw-bold">Starting Price:</span><br>
                            <span class="text-muted">$<?= number_format($lot['starting_price']) ?></span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold">Current Bid:</span><br>
                            <span class="fs-4 text-success fw-bold">$<?= number_format($lot['current_price']) ?></span>
                        </div>
                    </div>
                    <div class="auction-timer border rounded bg-light p-2 my-3 text-center "
                        data-end="<?= $auction['end_date'] ?>">
                        <span >Time left:</span>
                        <span class="days">00</span>d
                        <span class="hours">00</span>h
                        <span class="minutes">00</span>m
                        <span class="seconds">00</span>s
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
                        <a href="<?= BASE_URL ?>login?redirect=<?= urlencode(CURRENT_URL) ?>"
                            class="btn btn-outline-secondary w-100 mb-3">
                            <i class="bi bi-bookmark-plus"></i> Login to Add to Watchlist
                        </a>
                    <?php endif; ?>

                    <?php if ($auction['status'] === 'live'): ?>
                        <hr>
                        <?php if (isset($user)): ?>
                            <?php
                            $bidModel = new Bid();
                            $minimumBid = $bidModel->getNextMinimumBid($lot['current_price']);
                            $increment = $bidModel->getBidIncrement($lot['current_price']);
                            ?>
                            <form action="<?= BASE_URL ?>bids/place" method="post" class="mt-3">
                                <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                                <div class="mb-3">
                                    <label for="bid_amount" class="form-label">Your Bid Amount ($)</label>
                                    <input type="number" class="form-control" id="bid_amount" name="bid_amount"
                                        min="<?= $minimumBid ?>" value="<?= $minimumBid ?>" required>
                                    <div class="form-text">Minimum bid: $<?= number_format($minimumBid) ?> (Increment:
                                        $<?= $increment ?>)</div>
                                </div>
                                <div class="mb-3">
                                    <label for="max_amount" class="form-label">Maximum Bid Amount (Optional)</label>
                                    <input type="number" class="form-control" id="max_amount" name="max_amount"
                                        min="<?= $minimumBid ?>">
                                    <div class="form-text">Set a maximum amount for proxy bidding.</div>
                                </div>
                                <div class="alert alert-info mb-3">
                                    <strong><i class="bi bi-info-circle"></i> Proxy Bidding:</strong> The system will bid on
                                    your behalf up to your maximum amount, as needed to outbid others.
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Place Bid</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Please <a href="<?= BASE_URL ?>login" class="alert-link">login</a> to place a bid.
                            </div>
                        <?php endif; ?>
                    <?php elseif ($auction['status'] === 'upcoming'): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> Bidding will open when the auction starts.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary">
                            <i class="bi bi-exclamation-circle me-2"></i> This auction has ended.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <?php if (!empty($relatedLots)): ?>
        <div class="row">
            <div class="col-12">
                <h2 class="h4 mb-3 border-bottom pb-2">Other Lots in this Auction</h2>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <?php foreach ($relatedLots as $relatedLot): ?>
                        <div class="col">
                            <div class="card h-100">
                                <div
                                    style="height: 140px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    <?php if (!empty($relatedLot['image_path'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($relatedLot['image_path']) ?>" class="img-fluid"
                                            alt="<?= htmlspecialchars($relatedLot['title']) ?>"
                                            style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <i class="bi bi-image text-secondary" style="font-size: 2rem;"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-2">
                                    <h6 class="card-title text-truncate" title="<?= htmlspecialchars($relatedLot['title']) ?>">
                                        <?= htmlspecialchars($relatedLot['title']) ?></h6>
                                    <p class="card-text small mb-1">
                                        <strong>Current:</strong> $<?= number_format($relatedLot['current_price']) ?>
                                    </p>
                                    <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>/lots/<?= $relatedLot['id'] ?>"
                                        class="btn btn-sm btn-outline-primary w-100">View Details</a>
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
    document.addEventListener('DOMContentLoaded', function () {
        const timers = document.querySelectorAll('.auction-timer');

        timers.forEach(timer => {
            const endTime = new Date(timer.dataset.end).getTime();

            const updateTimer = function () {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    timer.innerHTML = '<span class="badge bg-danger">Auction has ended</span>';
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