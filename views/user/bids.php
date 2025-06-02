<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="display-5 mb-4 border-bottom pb-2">My Bids</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASE_URL ?>user/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Bidding History</h5>
                    <div class="btn-group">
                        <a href="<?= BASE_URL ?>user/bids" class="btn btn-sm btn-outline-secondary <?= !isset($_GET['status']) ? 'active' : '' ?>">All Bids</a>
                        <a href="<?= BASE_URL ?>user/bids?status=active" class="btn btn-sm btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'active' ? 'active' : '' ?>">Active</a>
                        <a href="<?= BASE_URL ?>user/bids?status=won" class="btn btn-sm btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'won' ? 'active' : '' ?>">Won</a>
                        <a href="<?= BASE_URL ?>user/bids?status=outbid" class="btn btn-sm btn-outline-secondary <?= isset($_GET['status']) && $_GET['status'] === 'outbid' ? 'active' : '' ?>">Outbid</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($bids)): ?>
                        <div class="alert alert-info text-center py-4">
                            <h5 class="mb-3">No bids found</h5>
                            <p>You haven't placed any bids yet.</p>
                            <a href="<?= BASE_URL ?>auctions" class="btn btn-primary mt-2">Browse Current Auctions</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Lot</th>
                                        <th>Auction</th>
                                        <th>Bid Amount</th>
                                        <th>Max Bid</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bids as $bid): ?>
                                        <tr>
                                            <td><a href="<?= BASE_URL ?>lots/<?= $bid['lot_id'] ?>"><?= htmlspecialchars($bid['lot_title']) ?></a></td>
                                            <td><a href="<?= BASE_URL ?>auctions/<?= $bid['auction_id'] ?>"><?= htmlspecialchars($bid['auction_title']) ?></a></td>
                                            <td class="fw-bold">$<?= number_format($bid['amount']) ?></td>
                                            <td><?= $bid['max_amount'] ? '$'.number_format($bid['max_amount']) : '<span class="text-muted">N/A</span>' ?></td>
                                            <td>
                                                <?php if ($bid['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif ($bid['status'] === 'outbid'): ?>
                                                    <span class="badge bg-warning text-dark">Outbid</span>
                                                <?php elseif ($bid['status'] === 'won'): ?>
                                                    <span class="badge bg-primary">Won</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Lost</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M j, Y g:i a', strtotime($bid['created_at'])) ?></td>
                                            <td>
                                                <?php if ($bid['status'] === 'outbid' && strtotime($bid['auction_end_date']) > time()): ?>
                                                    <a href="<?= BASE_URL ?>lots/<?= $bid['lot_id'] ?>" class="btn btn-sm btn-outline-primary">Bid Again</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 