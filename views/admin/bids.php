<?php
if (!isset($user) || $user['role'] !== 'admin') {
    header('Location: ' . BASE_URL);
    exit;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Bids</h1>
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
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($bid['placed_at']))) ?></td>
                                <td>
                                    <span class="badge bg-<?= $bid['status'] === 'winning' ? 'success' : 
                                        ($bid['status'] === 'outbid' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst(htmlspecialchars($bid['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteBidModal<?= $bid['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteBidModal<?= $bid['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this bid? This action cannot be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="<?= BASE_URL ?>admin/delete-bid?id=<?= $bid['id'] ?>" class="btn btn-danger">Delete</a>
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