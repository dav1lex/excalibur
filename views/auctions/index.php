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
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 100px; padding-left: 1.25rem;">Image</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th class="text-end" style="padding-right: 1.25rem;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedAuctions[$status] as $auction): ?>
                                            <tr>
                                                <td style="padding-left: 1.25rem;">
                                                    <div class="auction-img-container rounded bg-white border"
                                                        style="width: 80px; height: 60px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                        <?php if (!empty($auction['image_path'])): ?>
                                                            <img src="<?= BASE_URL . htmlspecialchars($auction['image_path']) ?>"
                                                                class="img-fluid" alt="<?= htmlspecialchars($auction['title']) ?>"
                                                                style="object-fit: cover; width: 100%; height: 100%;">
                                                        <?php else: ?>
                                                            <i class="bi bi-image text-secondary"
                                                                style="font-size: 1.5rem; opacity: 0.5;"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($auction['title']) ?></h6>
                                                    <p class="small text-muted mb-0 text-truncate" style="max-width: 400px;">
                                                        <?= htmlspecialchars(mb_substr($auction['description'], 0, 80)) ?>...
                                                    </p>
                                                </td>
                                                <td>
                                                    <?php if ($status === 'upcoming'): ?>
                                                        <small class="text-muted d-block">Starts:</small>
                                                        <span
                                                            class="small text-dark fw-bold"><?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?></span>
                                                    <?php elseif ($status === 'live'): ?>
                                                        <small class="text-danger d-block">Ends:</small>
                                                        <span
                                                            class="small text-dark fw-bold"><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></span>
                                                    <?php else: ?>
                                                        <small class="text-muted d-block">Ended:</small>
                                                        <span
                                                            class="small text-dark fw-bold"><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end" style="padding-right: 1.25rem;">
                                                    <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                                        <i class="bi bi-eye me-1"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.04);
    }
</style>