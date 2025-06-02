<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>auctions">Auctions</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($auction['title']) ?></li>
                </ol>
            </nav>
            <h1 class="display-5 mb-2"><?= htmlspecialchars($auction['title']) ?></h1>
            
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
            <div class="d-block">
                <?php if ($auction['status'] === 'upcoming'): ?>
                    <div class="text-muted">
                        Starts: <?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?>
                    </div>
                <?php elseif ($auction['status'] === 'live'): ?>
                    <div class="text-danger fw-bold">
                        Ends: <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                        <div class="auction-timer" data-end="<?= $auction['end_date'] ?>">
                            <span class="days">00</span>d
                            <span class="hours">00</span>h
                            <span class="minutes">00</span>m
                            <span class="seconds">00</span>s
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-muted">
                        Ended: <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-3">Lots in this Auction</h2>
            
            <?php if (empty($lots)): ?>
                <div class="alert alert-info">
                    No lots have been added to this auction yet. Please check back later.
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($lots as $lot): ?>
                        <div class="col">
                            <div class="card h-100 card-auction">
                                <?php if (!empty($lot['image_path'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($lot['title']) ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light text-center py-5">
                                        <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($lot['title']) ?></h5>
                                    <p class="card-text text-muted">
                                        Lot #<?= htmlspecialchars($lot['lot_number']) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Current Bid:</strong> $<?= number_format($lot['current_price']) ?>
                                        </div>
                                        <?php if ($auction['status'] === 'live'): ?>
                                            <span class="badge bg-danger">Bidding Open</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="<?= BASE_URL ?>lots/view?id=<?= $lot['id'] ?>" class="btn btn-primary w-100">View Lot</a>
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