<!-- auction list public page /auctions/ -->
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">Auctions</h1>
        </div>
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

    <?php if (empty($auctions)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-info-circle fs-2 mb-3 d-block"></i>
            <h4 class="mb-1">No auctions found</h4>
            <p class="mb-0 text-muted">There are no auctions available at the moment. Please check back later.</p>
        </div>
    <?php else: ?>
        <?php
        // Group auctions by status
        $groupedAuctions = [
            'live' => [],
            'upcoming' => [],
            'ended' => []
        ];
        foreach ($auctions as $auction) {
            if (isset($groupedAuctions[$auction['status']])) {
                $groupedAuctions[$auction['status']][] = $auction;
            }
        }

        // Status info for tabs
        $statusInfo = [
            'live' => ['title' => 'Live', 'icon' => 'bi-broadcast'],
            'upcoming' => ['title' => 'Upcoming', 'icon' => 'bi-calendar-event'],
            'ended' => ['title' => 'Ended', 'icon' => 'bi-archive']
        ];
        ?>

        <div class="auction-tabs-container">
            <!-- Navigation Tabs -->
            <ul class="nav nav-pills mb-3" id="auctionsTab" role="tablist">
                <?php foreach ($statusInfo as $status => $info): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $status === 'live' ? 'active' : '' ?>" id="<?= $status ?>-tab"
                            data-bs-toggle="pill" data-bs-target="#<?= $status ?>-pane" type="button" role="tab"
                            aria-controls="<?= $status ?>-pane" aria-selected="<?= $status === 'live' ? 'true' : 'false' ?>">
                            <i class="bi <?= $info['icon'] ?> me-2"></i>
                            <?= $info['title'] ?>
                            <span class="badge rounded-pill ms-2 <?= $status === 'live' ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= count($groupedAuctions[$status]) ?>
                            </span>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="auctionsTabContent">
                <?php foreach ($statusInfo as $status => $info): ?>
                    <div class="tab-pane fade <?= $status === 'live' ? 'show active' : '' ?>" id="<?= $status ?>-pane"
                        role="tabpanel" aria-labelledby="<?= $status ?>-tab" tabindex="0">

                        <?php if (empty($groupedAuctions[$status])): ?>
                            <div class="text-center p-5 bg-light rounded">
                                <p class="text-muted mb-0">No <?= strtolower($info['title']) ?> auctions to display.</p>
                            </div>
                        <?php else: ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <?php foreach ($groupedAuctions[$status] as $auction): ?>
                                    <div class="col">
                                        <div class="card h-100 auction-card">
                                            <div class="card-img-top auction-card-img">
                                                <?php if (!empty($auction['image_path'])): ?>
                                                    <img src="<?= BASE_URL . htmlspecialchars($auction['image_path']) ?>"
                                                        class="img-fluid" alt="<?= htmlspecialchars($auction['title']) ?>">
                                                <?php else: ?>
                                                    <div class="no-image-placeholder">
                                                        <i class="bi bi-image text-secondary"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($auction['title']) ?></h5>
                                                <p class="card-text text-muted auction-description">
                                                    <?= htmlspecialchars(mb_substr($auction['description'], 0, 100)) ?>...
                                                </p>
                                                
                                                <div class="auction-date mb-3">
                                                    <?php if ($status === 'upcoming'): ?>
                                                        <span class="text-muted small">Starts:</span>
                                                        <div class="fw-bold"><?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?></div>
                                                    <?php elseif ($status === 'live'): ?>
                                                        <span class="text-danger small">Ends:</span>
                                                        <div class="fw-bold"><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></div>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Ended:</span>
                                                        <div class="fw-bold"><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent text-center border-top-0">
                                                <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>" 
                                                   class="btn btn-primary rounded-pill w-100">
                                                    <i class="bi bi-eye me-1"></i> View Auction
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .auction-tabs-container {
        background-color: #ffffff;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid #dee2e6;
    }

    .nav-pills .nav-link {
        color: #6c757d;
        background-color: #f8f9fa;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        border: 1px solid #dee2e6;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .nav-pills .nav-link.active .badge {
        background-color: #fff !important;
        color: #0d6efd !important;
    }

    .tab-content {
        padding-top: 1rem;
    }
    
    .auction-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid rgba(0,0,0,0.125);
    }
    
    
    .auction-card-img {
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    .auction-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .no-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .no-image-placeholder i {
        font-size: 3rem;
        opacity: 0.3;
    }
    
    .auction-description {
        min-height: 3rem;
    }
    
    @media (max-width: 767.98px) {
        .auction-tabs-container {
            padding: 0.75rem;
        }
        
        .nav-pills .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
        
        .auction-card-img {
            height: 160px;
        }
    }
</style>