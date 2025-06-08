<?php
require_once 'controllers/BaseController.php';
require_once 'models/User.php';
require_once 'models/Auction.php';
require_once 'models/Lot.php';
require_once 'models/Bid.php';

class AdminController extends BaseController
{
    private $userModel;
    private $auctionModel;
    private $lotModel;
    private $bidModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auctionModel = new Auction();
        $this->lotModel = new Lot();
        $this->bidModel = new Bid();
    }

    public function dashboard()
    {
        $this->ensureAdmin();

        $stats = [
            'totalUsers' => count($this->userModel->getAll()),
            'totalAuctions' => $this->auctionModel->countTotal(),
            'totalLots' => $this->lotModel->countTotal(),
            'totalBids' => $this->bidModel->countTotal() ?? 0
        ];

        // Get all auctions 
        $auctions = $this->auctionModel->getAll();

        // Check if is selected
        $auctionTotal = null;
        $selectedAuction = null;

        if (isset($_GET['auction_id']) && !empty($_GET['auction_id'])) {
            $auction_id = (int) $_GET['auction_id'];

            // Get
            $selectedAuction = $this->auctionModel->getById($auction_id);

            if ($selectedAuction) {
                // Calculate from method
                $auctionTotal = $this->lotModel->calculateAuctionTotal($auction_id);
            }
        }

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'stats' => $stats,
            'auctions' => $auctions,
            'auctionTotal' => $auctionTotal,
            'selectedAuction' => $selectedAuction
        ]);
    }

    public function users()
    {
        $this->ensureAdmin();
        $role = isset($_GET['role']) ? $_GET['role'] : null;

        // Get users based on role filter
        if ($role && in_array($role, ['admin', 'user'])) {
            $users = $this->userModel->getByRole($role);
        } else {
            $users = $this->userModel->getAll();
        }

        $this->render('admin/users', [
            'title' => 'Manage Users - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'users' => $users,
            'current_role' => $role
        ]);
    }

    public function editUser($id = null)
    {
        $this->ensureAdmin();

        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $userData = $this->userModel->getById($id);

        if (!$userData) {
            $this->setErrorMessage('User not found.');
            $this->redirect(BASE_URL . 'admin/users');
            return;
        }

        $this->render('admin/edit_user', [
            'title' => 'Edit User - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'userData' => $userData
        ]);
    }

    public function updateUser()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/users');
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($name) || empty($email)) {
            $this->setErrorMessage('Please fill in all required fields.');
            $this->redirect(BASE_URL . 'admin/edit-user?id=' . $id);
            return;
        }

        // Prepare data for update
        $data = [
            'name' => $name,
            'email' => $email,
        ];

        // Add password if it's provided
        if (!empty($password)) {
            $data['password'] = $password;
        }

        // Update user
        if ($this->userModel->update($id, $data)) {
            $this->setSuccessMessage('User updated successfully.');
        } else {
            $this->setErrorMessage('Error updating user. Please try again.');
        }

        $this->redirect(BASE_URL . 'admin/users');
    }

    public function deleteUser()
    {
        $this->ensureAdmin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        // Don't allow deleting self
        if ($id === (int) $_SESSION['user_id']) {
            $this->setErrorMessage('You cannot delete your own account.');
            $this->redirect(BASE_URL . 'admin/users');
            return;
        }

        // Delete user
        if ($this->userModel->delete($id)) {
            $this->setSuccessMessage('User deleted successfully.');
        } else {
            $this->setErrorMessage('Error deleting user. Please try again.');
        }

        $this->redirect(BASE_URL . 'admin/users');
    }

    public function lots()
    {
        $this->ensureAdmin();
        $auction_id = isset($_GET['auction_id']) ? (int) $_GET['auction_id'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get all auctions for the dropdown
        $auctions = $this->auctionModel->getAll();
        $current_auction = null;

        // Get lots based on filters
        if (!empty($search)) {
            // If search term is provided, use search method
            $lots = $this->lotModel->search($search, $auction_id);
        } else if ($auction_id) {
            // If only auction filter is provided
            $lots = $this->lotModel->getByAuctionId($auction_id);
            $current_auction = $this->auctionModel->getById($auction_id);
        } else {
            // No filters, get all lots
            $lots = $this->lotModel->getAllWithAuctionTitle();
        }

        $this->render('admin/lots', [
            'title' => 'Manage Lots - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'lots' => $lots,
            'auctions' => $auctions,
            'current_auction' => $current_auction,
            'current_auction_id' => $auction_id,
            'search' => $search
        ]);
    }

    public function viewLot($id = null)
    {
        $this->ensureAdmin();

        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $lot = $this->lotModel->getById($id);

        if (!$lot) {
            $this->setErrorMessage('Lot not found');
            $this->redirect(BASE_URL . 'admin/lots');
            return;
        }

        // Get auction info
        $auction = $this->auctionModel->getById($lot['auction_id']);

        // Get bid history
        $bids = $this->bidModel->getByLotId($id);

        // Determine winner (highest bidder if auction has ended)
        $winner = null;
        if ($auction['status'] === 'ended' && !empty($bids)) {
            $winner = $bids[0]; // Bids are ordered by amount DESC
        }

        $this->render('admin/view_lot', [
            'title' => 'View Lot: ' . $lot['title'] . ' - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'lot' => $lot,
            'auction' => $auction,
            'bids' => $bids,
            'winner' => $winner
        ]);
    }

    public function auctions()
    {
        $this->ensureAdmin();
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        if ($status) {
            $auctions = $this->auctionModel->getByStatus($status);
        } else {
            $auctions = $this->auctionModel->getAll();
        }

        $lotCounts = [];
        if (!empty($auctions)) {
            foreach ($auctions as $auction) {
                $auction_id = $auction['id'];
                $count = $this->lotModel->countByAuction($auction_id);
                $lotCounts[$auction_id] = $count;
            }
        }

        $this->render('admin/auctions', [
            'title' => 'Manage Auctions - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'auctions' => $auctions,
            'lotCounts' => $lotCounts,
            'filter' => $status ?? 'all'
        ]);
    }

    public function bids()
    {
        $this->ensureAdmin();
        $bids = $this->bidModel->getAll();
        $this->render('admin/bids', [
            'title' => 'Manage Bids - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'bids' => $bids
        ]);
    }

    public function deleteBid()
    {
        $this->ensureAdmin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if (!$id) {
            $this->setErrorMessage('Invalid bid ID.');
            $this->redirect(BASE_URL . 'admin/bids');
            return;
        }

        // Get bid info before deletion for potential updates
        $bid = $this->bidModel->getById($id);

        if (!$bid) {
            $this->setErrorMessage('Bid not found.');
            $this->redirect(BASE_URL . 'admin/bids');
            return;
        }

        $lot_id = $bid['lot_id']; // Store lot_id before bid is deleted

        // Delete the bid
        if ($this->bidModel->delete($id)) {
            // After deleting a bid, always recalculate the lot's current price
            $newHighestBid = $this->bidModel->getHighestBidForLot($lot_id);

            if ($newHighestBid) {
                // If there are other bids, set current_price to the new highest bid amount
                $this->lotModel->update($lot_id, ['current_price' => $newHighestBid['amount']]);
            } else {
                // If no bids remain, set current_price back to starting_price
                $lotDetails = $this->lotModel->getById($lot_id);
                if ($lotDetails) {
                    $this->lotModel->update($lot_id, ['current_price' => $lotDetails['starting_price']]);
                }
            }

            $this->setSuccessMessage('Bid deleted successfully. Lot current price updated.');
        } else {
            $this->setErrorMessage('Error deleting bid. Please try again.');
        }

        $this->redirect(BASE_URL . 'admin/bids');
    }
}