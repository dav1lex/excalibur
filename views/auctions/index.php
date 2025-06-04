<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 mb-4">Auctions</h1>
            
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
        <div class="col-md-4">
            <div class="btn-group float-md-end w-100 w-md-auto mb-3 mb-md-0" role="group">
                <a href="<?= BASE_URL ?>auctions?filter=all" class="btn btn<?= $filter === 'all' ? '' : '-outline' ?>-primary">All</a>
                <a href="<?= BASE_URL ?>auctions?filter=upcoming" class="btn btn<?= $filter === 'upcoming' ? '' : '-outline' ?>-primary">Upcoming</a>
                <a href="<?= BASE_URL ?>auctions?filter=live" class="btn btn<?= $filter === 'live' ? '' : '-outline' ?>-primary">Live</a>
                <a href="<?= BASE_URL ?>auctions?filter=ended" class="btn btn<?= $filter === 'ended' ? '' : '-outline' ?>-primary">Ended</a>
            </div>
        </div>
    </div>

    <?php if (empty($auctions)): ?>
        <div class="alert alert-info">
            No auctions found in this category. Please check back later.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($auctions as $auction): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
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
                        
                        <?php if (!empty($auction['image_path'])): ?>
                            <div class="position-relative">
                                <img src="<?= BASE_URL . htmlspecialchars($auction['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($auction['title']) ?>" style="height: 180px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                            </div>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($auction['title']) ?></h5>
                            <p class="card-text text-muted">
                                <?= substr(htmlspecialchars($auction['description']), 0, 100) ?>...
                            </p>
                            
                            <div class="mb-3">
                                <?php if ($auction['status'] === 'upcoming'): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event"></i> Starts: <?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?>
                                    </small>
                                <?php elseif ($auction['status'] === 'live'): ?>
                                    <small class="text-danger fw-bold">
                                        <i class="bi bi-alarm"></i> Ends: <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-check"></i> Ended: <?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>" class="btn btn-primary w-100">
                                <i class="bi bi-eye"></i> View Auction
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

main {
    flex: 1;
}


</style> 