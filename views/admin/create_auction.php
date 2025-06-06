<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h1 class="display-5 mb-0">Create Auction</h1>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Auction Details</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>auctions/store" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="auction_image" class="form-label">Auction Image</label>
                            <input type="file" class="form-control" id="auction_image" name="auction_image"
                                accept="image/jpeg,image/png,image/jpg">
                            <div class="form-text">
                                Optional. Maximum size: 6MB. Formats: JPG, PNG.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date & Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date & Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft">Draft</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="live">Live</option>
                                <option value="ended">Ended</option>
                            </select>
                            <div class="form-text">
                                Draft: Not visible to users<br>
                                Upcoming: Visible but bidding not open<br>
                                Live: Bidding open<br>
                                Ended: Auction closed
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Create Auction</button>
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
                            Create the auction first, then add lots to it.
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Set status to "Draft" while preparing the auction.
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Change status to "Upcoming" when ready to show to users.
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            The system will automatically update statuses based on dates.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Set minimum dates for start and end date inputs
            const now = new Date();
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Format date to YYYY-MM-DDThh:mm
            const formatDateForInput = (date) => {
                return date.getFullYear() + '-' +
                    String(date.getMonth() + 1).padStart(2, '0') + '-' +
                    String(date.getDate()).padStart(2, '0') + 'T' +
                    String(date.getHours()).padStart(2, '0') + ':' +
                    String(date.getMinutes()).padStart(2, '0');
            };

            // Set min attribute for start date to now
            startDateInput.min = formatDateForInput(now);

            // Update end date min when start date changes
            startDateInput.addEventListener('change', function () {
                if (this.value) {
                    const startDate = new Date(this.value);
                    endDateInput.min = formatDateForInput(startDate);

                    // If end date is before start date, update it
                    if (endDateInput.value && new Date(endDateInput.value) < startDate) {
                        endDateInput.value = formatDateForInput(startDate);
                    }
                }
            });
        });
    </script>