<?php
require_once 'controllers/BaseController.php';
require_once 'models/Lot.php';
require_once 'models/Auction.php';
require_once 'models/User.php';
require_once 'models/Bid.php';
require_once 'models/Watchlist.php';

class LotController extends BaseController
{
    private $lotModel;
    private $auctionModel;
    private $userModel;
    private $bidModel;

    public function __construct()
    {
        $this->lotModel = new Lot();
        $this->auctionModel = new Auction();
        $this->userModel = new User();
        $this->bidModel = new Bid();
    }

    /**
     * Show individual lot page
     */
    public function view($id = null)
    {
        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $lot = $this->lotModel->getById($id);

        if (!$lot) {
            $this->setErrorMessage('Lot not found');
            $this->redirect(BASE_URL . 'auctions');
            return;
        }

        // Get auction info
        $auction = $this->auctionModel->getById($lot['auction_id']);

        // Add auction status and dates
        $auction['auction_status'] = $auction['status'];
        $auction['auction_start_date'] = $auction['start_date'];
        $auction['auction_end_date'] = $auction['end_date'];

        // Get related lots
        $relatedLots = $this->lotModel->getRelated($lot['auction_id'], $id, 4);

        // Get bid history
        $bidModel = new Bid();
        $bids = $bidModel->getByLotId($id, 10); // Get the 10 most recent bids

        // Check if the lot is in user's watchlist
        $inWatchlist = false;
        if ($this->isLoggedIn()) {
            $watchlistModel = new Watchlist();
            $inWatchlist = $watchlistModel->isInWatchlist($_SESSION['user_id'], $id);
        }

        $this->render('lots/view', [
            'title' => $lot['title'] . ' - ' . SITE_NAME,
            'lot' => $lot,
            'auction' => $auction,
            'relatedLots' => $relatedLots,
            'bids' => $bids,
            'inWatchlist' => $inWatchlist,
            'user' => $this->getCurrentUser()
        ]);
    }

    /**
     * Admin: Create lot form
     */
    public function create($auction_id = null)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        if (!$auction_id) {
            $auction_id = isset($_GET['auction_id']) ? (int) $_GET['auction_id'] : 0;
        }

        // If no auction_id provided, redirect to lots page to select an auction
        if (!$auction_id) {
            $this->setErrorMessage('Please select an auction to add a lot to.');
            $this->redirect(BASE_URL . 'admin/lots');
            return;
        }

        // Verify auction exists
        $auction = $this->auctionModel->getById($auction_id);

        if (!$auction) {
            $this->setErrorMessage('Auction not found');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        $this->render('admin/create_lot', [
            'title' => 'Add Lot - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'auction' => $auction
        ]);
    }

    /**
     * Admin: Store new lot
     */
    public function store()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        $auction_id = isset($_POST['auction_id']) ? (int) $_POST['auction_id'] : 0;

        // Validate and sanitize input
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $lot_number = $_POST['lot_number'] ?? '';
        $starting_price = isset($_POST['starting_price']) ? (int) $_POST['starting_price'] : 0;
        $reserve_price = isset($_POST['reserve_price']) ? (int) $_POST['reserve_price'] : null;

        // Basic validation
        if (empty($title) || empty($description) || empty($lot_number) || $starting_price <= 0) {
            $this->setErrorMessage('Please fill in all required fields with valid values');
            $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auction_id);
            return;
        }

        // Process image if uploaded
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Configure file upload
            $target_dir = 'public/uploads/lots/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            // Check file size
            if ($_FILES['image']['size'] > 6000000) { // 6MB
                $this->setErrorMessage('Sorry, your file is too large (max 6MB)');
                $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auction_id);
                return;
            }

            // Only allow jpg and png
            $allowed_types = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($file_extension), $allowed_types)) {
                $this->setErrorMessage('Sorry, only JPG and PNG files are allowed');
                $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auction_id);
                return;
            }

            // Upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                $this->setErrorMessage('Sorry, there was an error uploading your file');
                $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auction_id);
                return;
            }
        }

        // Create lot
        $lotId = $this->lotModel->create([
            'auction_id' => $auction_id,
            'title' => $title,
            'description' => $description,
            'lot_number' => $lot_number,
            'starting_price' => $starting_price,
            'reserve_price' => $reserve_price,
            'image_path' => $image_path
        ]);

        if ($lotId) {
            $this->setSuccessMessage('Lot created successfully');
            $this->redirect(BASE_URL . 'admin/lots?auction_id=' . $auction_id);
        } else {
            $this->setErrorMessage('Error creating lot');
            $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auction_id);
        }
    }

    /**
     * Admin: Edit lot form
     */
    public function edit($id = null)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $lot = $this->lotModel->getById($id);

        if (!$lot) {
            $this->setErrorMessage('Lot not found');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        // Get auction info
        $auction = $this->auctionModel->getById($lot['auction_id']);

        $this->render('admin/edit_lot', [
            'title' => 'Edit Lot - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'lot' => $lot,
            'auction' => $auction
        ]);
    }

    /**
     * Admin: Update lot
     */
    public function update($id = null)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        // If id is not in the path, get it from query params
        if (!$id && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            $this->redirect(BASE_URL . 'admin/auctions'); // Or perhaps admin/lots
            return;
        }

        $id = (int) $id;
        $lot = $this->lotModel->getById($id);

        if (!$lot) {
            $this->setErrorMessage('Lot not found');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        $auction_id = $lot['auction_id'];

        // Validate and sanitize input
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $lot_number = $_POST['lot_number'] ?? '';
        $starting_price = isset($_POST['starting_price']) ? (int) $_POST['starting_price'] : 0;
        $reserve_price = isset($_POST['reserve_price']) && $_POST['reserve_price'] !== '' ? (int) $_POST['reserve_price'] : null;

        // Basic validation
        if (empty($title) || empty($description) || empty($lot_number) || $starting_price <= 0) {
            $this->setErrorMessage('Please fill in all required fields with valid values');
            $this->redirect(BASE_URL . 'lots/edit/' . $id);
            return;
        }

        // Prepare lot data
        $lotData = [
            'title' => $title,
            'description' => $description,
            'lot_number' => $lot_number,
            'starting_price' => $starting_price,
            'reserve_price' => $reserve_price
        ];

        // If starting_price is being changed AND there are no bids on this lot,
        // then current_price should also be updated to the new starting_price.
        if (isset($lotData['starting_price']) && $lotData['starting_price'] != $lot['starting_price']) {
            $bidCount = $this->bidModel->countByLot($id);
            if ($bidCount == 0) {
                $lotData['current_price'] = $lotData['starting_price'];
            }
        }

        // Process image if uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Configure file upload
            $target_dir = 'public/uploads/lots/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            // Check file size
            if ($_FILES['image']['size'] > 6000000) { // 6MB
                $this->setErrorMessage('Sorry, your file is too large (max 6MB)');
                $this->redirect(BASE_URL . 'lots/edit/' . $id);
                return;
            }

            // Only allow jpg and png
            $allowed_types = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($file_extension), $allowed_types)) {
                $this->setErrorMessage('Sorry, only JPG and PNG files are allowed');
                $this->redirect(BASE_URL . 'lots/edit/' . $id);
                return;
            }

            // Upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $lotData['image_path'] = $target_file;

                // Delete old image if exists
                if (!empty($lot['image_path']) && file_exists($lot['image_path'])) {
                    unlink($lot['image_path']);
                }
            } else {
                $this->setErrorMessage('Sorry, there was an error uploading your file');
                $this->redirect(BASE_URL . 'lots/edit/' . $id);
                return;
            }
        }

        // Update lot
        $result = $this->lotModel->update($id, $lotData);

        if ($result) {
            $this->setSuccessMessage('Lot updated successfully');
            $this->redirect(BASE_URL . 'lots/edit/' . $id);
        } else {
            $this->setErrorMessage('Error updating lot');
            $this->redirect(BASE_URL . 'lots/edit/' . $id);
        }
    }

    /**
     * Admin: Delete lot
     */
    public function delete($id = null)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $lot = $this->lotModel->getById($id);

        if (!$lot) {
            $this->setErrorMessage('Lot not found');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        $auction_id = $lot['auction_id'];

        // Delete lot image if exists
        if (!empty($lot['image_path']) && file_exists($lot['image_path'])) {
            unlink($lot['image_path']);
        }

        // Delete lot
        $result = $this->lotModel->delete($id);

        if ($result) {
            $this->setSuccessMessage('Lot deleted successfully');
        } else {
            $this->setErrorMessage('Error deleting lot');
        }

        $this->redirect(BASE_URL . 'admin/lots?auction_id=' . $auction_id);
    }
}