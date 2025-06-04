<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Add New Lot</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= BASE_URL ?>admin/lots?auction_id=<?= $auction['id'] ?>" class="btn btn-outline-secondary">
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
                <form action="<?= BASE_URL ?>lots/store" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="auction_id" value="<?= $auction['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lot_number" class="form-label">Lot Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">LOT-</span>
                            <input type="text" class="form-control" id="lot_number" name="lot_number" pattern="[0-9]{1,3}" maxlength="3" required placeholder="e.g. 001">
                        </div>
                        <div class="form-text">
                            <?php if (isset($lastLotNumber)): ?>
                                Last lot number: <?= htmlspecialchars($lastLotNumber) ?>
                            <?php else: ?>
                                Enter lot number, it will be formatted as LOT-001, LOT-002, etc.
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="starting_price" class="form-label">Starting Price ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="starting_price" name="starting_price" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reserve_price" class="form-label">Reserve Price ($) <small class="text-muted">(Optional)</small></label>
                            <input type="number" class="form-control" id="reserve_price" name="reserve_price" min="1">
                            <div class="form-text">Minimum price for the lot to sell. Leave empty for no reserve.</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image <small class="text-muted">(Optional)</small></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png">
                        <div class="form-text">Max file size: 6MB. Allowed formats: JPG, PNG</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Create Lot</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Use clear, descriptive titles for better visibility.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Include detailed descriptions with condition information.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        High-quality images increase bidder interest.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Set reasonable starting prices to encourage bidding.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> 