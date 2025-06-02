<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="display-5 mb-4 border-bottom pb-2">Manage Lots</h1>
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Filter Options</h5>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>admin/lots" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label for="auction_id" class="form-label">Filter by Auction</label>
                        <select name="auction_id" id="auction_id" class="form-select">
                            <option value="">All Auctions</option>
                            <?php foreach ($auctions as $auction): ?>
                                <option value="<?= $auction['id'] ?>" <?= isset($current_auction_id) && $current_auction_id == $auction['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($auction['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="search" class="form-label">Search Lots</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search by title or lot number" value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Lots</h5>
                <div>
                    <?php if (isset($auctions) && !empty($auctions)): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="addLotDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-circle"></i> Add New Lot
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="addLotDropdown">
                                <?php foreach ($auctions as $auction): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>lots/create?auction_id=<?= $auction['id'] ?>"><?= htmlspecialchars($auction['title']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>auctions/create" class="btn btn-primary btn-sm">
                            Create an Auction First
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Lot #</th>
                                <th>Auction</th>
                                <th>Starting Price</th>
                                <th>Current Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lots)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No lots found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lots as $lot): ?>
                                    <tr>
                                        <td><?= $lot['id'] ?></td>
                                        <td>
                                            <?php if (!empty($lot['image_path'])): ?>
                                                <img src="<?= BASE_URL . htmlspecialchars($lot['image_path']) ?>" alt="Lot Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($lot['title']) ?></td>
                                        <td><?= htmlspecialchars($lot['lot_number']) ?></td>
                                        <td><?= htmlspecialchars($lot['title']) ?></td>
                                        <td>$<?= number_format($lot['starting_price']) ?></td>
                                        <td>$<?= number_format($lot['current_price']) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= BASE_URL ?>lots/view?id=<?= $lot['id'] ?>" class="btn btn-info" title="View">
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 