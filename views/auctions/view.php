<!-- auction detail public page -->
<div class="container py-4">
    <!-- Breadcrumbs -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>auctions">Auctions</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($auction['title']) ?></li>
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

    <!-- Main Auction Info Card -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-lg-4 p-3">
            <div class="row g-4">
                <!-- Left Column: Title, Description, Status -->
                <div class="col-lg-7">
                    <?php
                    $badgeClass = 'bg-secondary';
                    $badgeText = 'Draft';
                    if ($auction['status'] === 'upcoming') {
                        $badgeClass = 'bg-info text-dark';
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
                    <h1 class="h2 mb-3"><?= htmlspecialchars($auction['title']) ?></h1>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                </div>

                <!-- Right Column: Details & Timer -->
                <div class="col-lg-5">
                    <div class="bg-light p-3 rounded-3 h-100 d-flex flex-column">
                        <h5 class="mb-3 border-bottom pb-2">Auction Details</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between">
                                <strong><i class="bi bi-calendar-event me-2"></i>Start Date</strong>
                                <span><?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?></span>
                            </li>
                            <li class="d-flex justify-content-between mt-2">
                                <strong><i class="bi bi-calendar-check me-2"></i>End Date</strong>
                                <span><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></span>
                            </li>
                            <li class="d-flex justify-content-between mt-2">
                                <strong><i class="bi bi-list-nested me-2"></i>Total Lots</strong>
                                <span class="badge bg-primary rounded-pill"><?= count($lots) ?></span>
                            </li>
                        </ul>

                        <?php if ($auction['status'] === 'live'): ?>
                            <div class="mt-auto pt-3">
                                <hr class="my-2">
                                <div class="auction-timer text-center" data-end="<?= $auction['end_date'] ?>">
                                    <small class="text-danger fw-bold">Time Remaining:</small>
                                    <div class="d-flex justify-content-center h5 mb-0 mt-1 font-monospace">
                                        <span class="days">00</span>d :
                                        <span class="hours">00</span>h :
                                        <span class="minutes">00</span>m :
                                        <span class="seconds">00</span>s
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
            <div class="card-footer bg-light border-0 text-end py-2">
                <a href="<?= BASE_URL ?>auctions/edit/<?= $auction['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Edit Auction
                </a>
            </div>
        <?php endif; ?>
    </div>
    <hr>
    <!-- Lots Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h3 mb-0"><i class="bi bi-list-nested me-2"></i>Lots (<?= count($lots) ?>)</h2>
            </div>

            <?php if (empty($lots)): ?>
                <div class="alert alert-info text-center py-4">
                     <i class="bi bi-info-circle fs-3 mb-2 d-block"></i> No lots available for this auction.
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($lots as $lot): ?>
                        <div class="col mb-4">
                            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                                <div class="lot-image-container bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <?php if (!empty($lot['image_path'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="img-fluid" alt="<?= htmlspecialchars($lot['title']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <i class="bi bi-image text-secondary" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-3 d-flex flex-column">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-dark">#<?= htmlspecialchars($lot['lot_number']) ?></span>
                                        <?php if ($auction['status'] === 'live'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif ($auction['status'] === 'upcoming'): ?>
                                            <span class="badge bg-info text-dark">Upcoming</span>
                                        <?php elseif ($auction['status'] === 'ended'): ?>
                                            <span class="badge bg-secondary">Ended</span>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($lot['title']) ?></h5>
                                    <p class="card-text small text-muted mb-3">
                                        <?= mb_substr(htmlspecialchars($lot['description']), 0, 80) ?>...
                                    </p>
                                    <div class="mt-auto">
                                        <hr class="my-2">
                                        <?php if ($auction['status'] === 'live'): ?>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <small class="text-muted d-block">Starting</small>
                                                    <strong class="text-dark"><?= number_format($lot['starting_price']) ?> €</strong>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block">Current Bid</small>
                                                    <strong class="text-success fs-5"><?= number_format($lot['current_price']) ?> €</strong>
                                                </div>
                                            </div>
                                        <?php elseif ($auction['status'] === 'upcoming'): ?>
                                            <div>
                                                <small class="text-muted d-block">Starting Price</small>
                                                <strong class="text-dark fs-5"><?= number_format($lot['starting_price']) ?> €</strong>
                                            </div>
                                        <?php elseif ($auction['status'] === 'ended'): ?>
                                            <div>
                                                <small class="text-muted d-block">Final Price</small>
                                                <strong class="text-dark fs-5"><?= number_format($lot['current_price']) ?> €</strong>
                                            </div>
                                        <?php endif; ?>
                                        <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>/lots/<?= $lot['id'] ?>" class="btn btn-primary w-100 mt-3 rounded-pill">
                                            <i class="bi bi-eye me-1"></i> View Lot
                                        </a>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timer = document.querySelector('.auction-timer');
        if (timer) {
            const endTime = new Date(timer.dataset.end).getTime();
            const serverTimeOffset = <?= time() * 1000 ?> - Date.now(); // Calculate server-client time difference
            
            const updateTimer = function () {
                const clientNow = Date.now();
                const serverNow = clientNow + serverTimeOffset; // Use server time
                const distance = endTime - serverNow;

                if (distance < 0) {
                    timer.innerHTML = '<div class="text-danger fw-bold">Auction has ended</div>';
                    // Auto-refresh page after 3 seconds when timer ends
                    if (distance > -3000) { // Only schedule refresh once, when we're within 3 seconds of ending
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const a = timer.querySelector('.days');
                const b = timer.querySelector('.hours');
                const c = timer.querySelector('.minutes');
                const d = timer.querySelector('.seconds');

                if (a) a.textContent = String(days).padStart(2, '0');
                if (b) b.textContent = String(hours).padStart(2, '0');
                if (c) c.textContent = String(minutes).padStart(2, '0');
                if (d) d.textContent = String(seconds).padStart(2, '0');
            };
            updateTimer();
            setInterval(updateTimer, 1000);
        }
    });
</script>
