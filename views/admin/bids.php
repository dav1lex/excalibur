<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h1 class="display-5 mb-0">Manage Bids</h1>
            <a href="<?= BASE_URL ?>admin/auctions" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Auctions
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

    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-cash-stack me-1"></i>
            All Bids
        </div>
        <div class="card-body">
            <?php if (empty($bids)): ?>
                <div class="alert alert-info">No bids found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lot</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Proxy</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bids as $bid): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bid['id']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>lots/view?id=<?= htmlspecialchars($bid['lot_id']) ?>">
                                            <?= htmlspecialchars($bid['lot_title'] ?? 'Lot #' . $bid['lot_id']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($bid['user_name'] ?? 'User #' . $bid['user_id']) ?></td>
                                    <td><?= htmlspecialchars(number_format($bid['amount'])) ?> €</td>
                                    <td><?= htmlspecialchars($bid['max_amount'] ? 'Yes' : 'No') ?></td>
                                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($bid['placed_at']))) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $bid['status'] === 'winning' ? 'success' :
                                            ($bid['status'] === 'outbid' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst(htmlspecialchars($bid['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteBidModal<?= $bid['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteBidModal<?= $bid['id'] ?>" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this bid? This action cannot be undone.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <a href="<?= BASE_URL ?>admin/delete-bid?id=<?= $bid['id'] ?>"
                                                            class="btn btn-danger">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>