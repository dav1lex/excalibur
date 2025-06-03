<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Edit Auction</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= BASE_URL ?>admin/auctions" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Auctions
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
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Auction Details</h5>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>auctions/update/<?= $auction['id'] ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($auction['description']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="auction_image" class="form-label">Auction Image</label>
                        <?php if (!empty($auction['image_path'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL . htmlspecialchars($auction['image_path']) ?>" alt="Current auction image" class="img-thumbnail" style="max-height: 200px;">
                                <p class="form-text">Current image. Upload a new one to replace it.</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="auction_image" name="auction_image" accept="image/jpeg,image/png,image/jpg">
                        <div class="form-text">
                            Optional. Maximum size: 6MB. Formats: JPG, PNG.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($auction['start_date'])) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($auction['end_date'])) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= $auction['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="upcoming" <?= $auction['status'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                            <option value="live" <?= $auction['status'] === 'live' ? 'selected' : '' ?>>Live</option>
                            <option value="ended" <?= $auction['status'] === 'ended' ? 'selected' : '' ?>>Ended</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Update Auction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>lots/create?auction_id=<?= $auction['id'] ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add New Lot
                    </a>
                    <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>" class="btn btn-info">
                        <i class="bi bi-eye"></i> View Auction Page
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAuctionModal<?= $auction['id'] ?>">
                        <i class="bi bi-trash"></i> Delete Auction
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Delete modal -->
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
                <a href="<?= BASE_URL ?>auctions/delete/<?= $auction['id'] ?>" class="btn btn-danger">Delete Auction</a>
            </div>
        </div>
    </div>
</div>

<!-- Lots Management Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h3">Lots in this Auction</h2>
            <a href="<?= BASE_URL ?>lots/create?auction_id=<?= $auction['id'] ?>" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Add New Lot
            </a>
        </div>
        
        <?php if (empty($lots)): ?>
            <div class="alert alert-info">
                No lots have been added to this auction yet. Use the "Add New Lot" button to add lots.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Lot #</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Starting Price</th>
                            <th>Current Price</th>
                            <th>Reserve Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lots as $lot): ?>
                            <tr>
                                <td><?= htmlspecialchars($lot['lot_number']) ?></td>
                                <td>
                                    <?php if (!empty($lot['image_path'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" alt="Lot Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($lot['title']) ?></td>
                                <td>$<?= number_format($lot['starting_price']) ?></td>
                                <td>$<?= number_format($lot['current_price']) ?></td>
                                <td><?= $lot['reserve_price'] ? '$' . number_format($lot['reserve_price']) : 'None' ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>auctions/<?= $auction['id'] ?>/lots/<?= $lot['id'] ?>" class="btn btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>lots/edit?id=<?= $lot['id'] ?>" class="btn btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>lots/delete?id=<?= $lot['id'] ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this lot? This cannot be undone.')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </a>
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