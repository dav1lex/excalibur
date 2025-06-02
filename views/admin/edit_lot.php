<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Edit Lot</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= BASE_URL ?>admin/lots?auction_id=<?= $lot['auction_id'] ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Lots
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
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Lot Details for: <?= htmlspecialchars($auction['title']) ?></h5>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>lots/update?id=<?= $lot['id'] ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="auction_id" value="<?= $lot['auction_id'] ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($lot['title']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lot_number" class="form-label">Lot Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lot_number" name="lot_number" value="<?= htmlspecialchars($lot['lot_number']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($lot['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="starting_price" class="form-label">Starting Price ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="starting_price" name="starting_price" min="1" value="<?= $lot['starting_price'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reserve_price" class="form-label">Reserve Price ($) <small class="text-muted">(Optional)</small></label>
                            <input type="number" class="form-control" id="reserve_price" name="reserve_price" min="1" value="<?= $lot['reserve_price'] ?>">
                            <div class="form-text">Minimum price for the lot to sell. Leave empty for no reserve.</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_price" class="form-label">Current Price ($)</label>
                        <input type="number" class="form-control" id="current_price" name="current_price" value="<?= $lot['current_price'] ?>" readonly>
                        <div class="form-text">This is automatically updated when bids are placed.</div>
                    </div>
                    
                    <?php if (!empty($lot['image_path'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" alt="Lot Image" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Update Image <small class="text-muted">(Optional)</small></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png">
                        <div class="form-text">Max file size: 6MB. Allowed formats: JPG, PNG. Leave empty to keep current image.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Update Lot</button>
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
                    <a href="<?= BASE_URL ?>lots/view?id=<?= $lot['id'] ?>" class="btn btn-info">
                        <i class="bi bi-eye"></i> View Lot Page
                    </a>
                    <a href="<?= BASE_URL ?>lots/delete?id=<?= $lot['id'] ?>" class="btn btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this lot? This cannot be undone.')">
                        <i class="bi bi-trash"></i> Delete Lot
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($bids)): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bid History</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($bids as $bid): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>$<?= number_format($bid['amount']) ?></strong> by 
                                        <?= htmlspecialchars($bid['user_name']) ?>
                                    </div>
                                    <small class="text-muted"><?= date('M j, Y, g:i A', strtotime($bid['created_at'])) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div> 