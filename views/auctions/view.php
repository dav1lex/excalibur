<div class="container py-4">
    <!-- ekmek kirintisi -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>auctions">Auctions</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($auction['title']) ?>
                    </li>
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

    <!-- Row 1 -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h1 class="card-title h2 mb-3"><?= htmlspecialchars($auction['title']) ?></h1>
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
                            <div class="d-flex align-items-center mb-3">
                                <small class="text-muted">
                                    <?php if ($auction['status'] === 'upcoming'): ?>
                                        <i class="bi bi-calendar-event me-1"></i> Starts:
                                        <?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?>
                                    <?php elseif ($auction['status'] === 'live'): ?>
                                        <i class="bi bi-alarm me-1"></i> Ends:
                                        <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                                    <?php else: ?>
                                        <i class="bi bi-calendar-check me-1"></i> Ended:
                                        <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                                    <?php endif; ?>
                                </small>
                            </div>

                            <?php if ($auction['status'] === 'live'): ?>
                                <div class="auction-timer-container bg-light p-3 rounded mb-4">
                                    <h5 class="mb-2 text-center">Time Remaining:</h5>
                                    <div class="auction-timer d-flex justify-content-center"
                                        data-end="<?= $auction['end_date'] ?>">
                                        <div class="timer-block bg-dark text-white p-2 rounded text-center me-2">
                                            <span class="days d-block h4 mb-0">00</span><small>Days</small>
                                        </div>
                                        <div class="timer-block bg-dark text-white p-2 rounded text-center me-2">
                                            <span class="hours d-block h4 mb-0">00</span><small>Hours</small>
                                        </div>
                                        <div class="timer-block bg-dark text-white p-2 rounded text-center me-2">
                                            <span class="minutes d-block h4 mb-0">00</span><small>Minutes</small>
                                        </div>
                                        <div class="timer-block bg-dark text-white p-2 rounded text-center">
                                            <span class="seconds d-block h4 mb-0">00</span><small>Seconds</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                                <a href="<?= BASE_URL ?>auctions/edit/<?= $auction['id'] ?>"
                                    class="btn btn-outline-primary mt-3">
                                    <i class="bi bi-pencil"></i> Edit Auction
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item ps-0">
                                    <h5 class="card-subtitle mb-2 text-muted">Auction Details</h5>
                                    <div class="card-text mb-3">
                                        <?= nl2br(htmlspecialchars($auction['description'])) ?>
                                    </div>
                                </li>
                                <li class="list-group-item ps-0 d-flex justify-content-between align-items-center">
                                    Status <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                </li>
                                <li class="list-group-item ps-0">
                                    <i class="bi bi-calendar-event me-2"></i>Start Date <br>
                                    <small
                                        class="text-muted ms-3"><?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?></small>
                                </li>
                                <li class="list-group-item ps-0">
                                    <i class="bi bi-calendar-check me-2"></i>End Date <br>
                                    <small
                                        class="text-muted ms-3"><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></small>
                                </li>
                                <li class="list-group-item ps-0 d-flex justify-content-between align-items-center">
                                    Lots <span class="badge bg-primary rounded-pill"><?= count($lots) ?></span>
                                </li>

                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <hr>
    <!-- Row 2 -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h3 mb-0"></i>Lots</h2>
            </div>

            <?php if (empty($lots)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i> No lot available.
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($lots as $lot): ?>
                        <div class="col mb-4">
                            <div class="card h-100">
                                <!-- images 2500x1250 def -->
                                <div
                                    style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    <?php if (!empty($lot['image_path'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" class="img-fluid"
                                            alt="<?= htmlspecialchars($lot['title']) ?>"
                                            style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-secondary"><?= htmlspecialchars($lot['lot_number']) ?></span>
                                        <?php if ($auction['status'] === 'live'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif ($auction['status'] === 'upcoming'): ?>
                                            <span class="badge bg-info text-dark">Upcoming</span>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($lot['title']) ?></h5>
                                    <p class="card-text small text-muted mb-3">
                                        <?= substr(htmlspecialchars($lot['description']), 0, 80) ?>...
                                    </p>
                                    <p class="mb-3">
                                        <strong>Starting Price:</strong><br>
                                        <span class="text-muted">$<?= number_format($lot['starting_price']) ?></span>
                                    </p>
                                    <p class="mb-3">
                                        <strong>Current Bid:</strong><br>
                                        <span
                                            class="text-success fw-bold fs-5">$<?= number_format($lot['current_price']) ?></span>
                                    </p>
                                    <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>/lots/<?= $lot['id'] ?>" class="btn btn-primary w-100">
                                        <i class="bi bi-eye"></i> View Lot
                                    </a>
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
    // Countdown 
    document.addEventListener('DOMContentLoaded', function () {
        const timers = document.querySelectorAll('.auction-timer');
        timers.forEach(timer => {
            const endTime = new Date(timer.dataset.end).getTime();
            const updateTimer = function () {
                const now = new Date().getTime();
                const distance = endTime - now;
                if (distance < 0) {
                    timer.innerHTML = '<div class="alert alert-warning text-center p-2">Auction has ended</div>';
                    const lotCards = document.querySelectorAll('.lot-card .badge.bg-success');
                    lotCards.forEach(badge => {
                        badge.classList.remove('bg-success');
                        badge.classList.add('bg-secondary');
                        badge.textContent = 'Ended';
                    });
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
