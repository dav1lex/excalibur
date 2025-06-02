<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Manage Auctions</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= BASE_URL ?>auctions/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Auction
        </a>
        <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="col-12">
        <div class="container mt-3">
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
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Auction List</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="<?= BASE_URL ?>admin/auctions" class="btn btn-outline-primary">All</a>
                    <a href="<?= BASE_URL ?>admin/auctions?status=draft" class="btn btn-outline-secondary">Draft</a>
                    <a href="<?= BASE_URL ?>admin/auctions?status=upcoming" class="btn btn-outline-info">Upcoming</a>
                    <a href="<?= BASE_URL ?>admin/auctions?status=live" class="btn btn-outline-success">Live</a>
                    <a href="<?= BASE_URL ?>admin/auctions?status=ended" class="btn btn-outline-dark">Ended</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Lots</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($auctions)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No auctions found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($auctions as $auction): ?>
                                    <tr>
                                        <td><?= $auction['id'] ?></td>
                                        <td><?= htmlspecialchars($auction['title']) ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = '';
                                            $badgeText = ucfirst($auction['status']);
                                            
                                            switch($auction['status']) {
                                                case 'draft':
                                                    $badgeClass = 'bg-secondary';
                                                    break;
                                                case 'upcoming':
                                                    $badgeClass = 'bg-info';
                                                    break;
                                                case 'live':
                                                    $badgeClass = 'bg-success';
                                                    break;
                                                case 'ended':
                                                    $badgeClass = 'bg-dark';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-secondary';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= $badgeText ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y, g:i A', strtotime($auction['start_date'])) ?></td>
                                        <td><?= date('M j, Y, g:i A', strtotime($auction['end_date'])) ?></td>
                                        <td>
                                            <?= $lotCounts[$auction['id']] ?? 0 ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>auctions/view?id=<?= $auction['id'] ?>" class="btn btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>auctions/edit/<?= $auction['id'] ?>" class="btn btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>lots/create?auction_id=<?= $auction['id'] ?>" class="btn btn-success" title="Add Lot">
                                                    <i class="bi bi-plus-circle"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAuctionModal<?= $auction['id'] ?>" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Auction Modal -->
                                    <div class="modal fade" id="deleteAuctionModal<?= $auction['id'] ?>" tabindex="-1" aria-labelledby="deleteAuctionModalLabel<?= $auction['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteAuctionModalLabel<?= $auction['id'] ?>">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the auction "<?= htmlspecialchars($auction['title']) ?>"? This action cannot be undone. Lots associated with this auction will also be deleted.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="<?= BASE_URL ?>auctions/delete?id=<?= $auction['id'] ?>" class="btn btn-danger">Delete Auction</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 