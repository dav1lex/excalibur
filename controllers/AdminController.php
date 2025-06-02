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
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        $stats = [
            'totalUsers' => count($this->userModel->getAll()),
            'totalAuctions' => $this->auctionModel->countTotal(),
            'totalLots' => $this->lotModel->countTotal(),
            'totalBids' => $this->bidModel->countTotal() ?? 0
        ];

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'stats' => $stats
        ]);
    }

    public function users()
    {
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        $users = $this->userModel->getAll();

        $this->render('admin/users', [
            'title' => 'Manage Users - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'users' => $users
        ]);
    }

    public function editUser($id = null)
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
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/users');
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'user';
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
            'role' => $role
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
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

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
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        // Get auction filter if provided
        $auction_id = isset($_GET['auction_id']) ? (int)$_GET['auction_id'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get all auctions for the dropdown
        $auctions = $this->auctionModel->getAll();

        // Get lots filtered by auction if specified
        if ($auction_id) {
            $lots = $this->lotModel->getByAuctionId($auction_id);
        } else {
            $lots = $this->lotModel->getAll();
        }

        $this->render('admin/lots', [
            'title' => 'Manage Lots - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'lots' => $lots,
            'auctions' => $auctions,
            'current_auction_id' => $auction_id,
            'search' => $search
        ]);
    }

    public function auctions()
    {
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        if ($status) {
            $auctions = $this->auctionModel->getByStatus($status);
        } else {
            $auctions = $this->auctionModel->getAll();
        }

        $lotCounts = [];
        foreach ($auctions as $auction) {
            $auction_id = (int)$auction['id'];
            $lotCounts[$auction_id] = $this->lotModel->countByAuction($auction_id);
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
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        $bids = $this->bidModel->getAll();

        $this->render('admin/bids', [
            'title' => 'Manage Bids - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'bids' => $bids
        ]);
    }

    public function deleteBid()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }
        
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
        
        // Delete the bid
        if ($this->bidModel->delete($id)) {
            // If this was a winning bid, we might need to update the lot's current_bid
            if ($bid['status'] === 'winning') {
                // Find the next highest bid for this lot
                $nextHighestBid = $this->bidModel->getHighestBidForLot($bid['lot_id'], $id);
                
                if ($nextHighestBid) {
                    // Update the next highest bid to winning status
                    $this->bidModel->update($nextHighestBid['id'], ['status' => 'winning']);
                    
                    // Update the lot's current bid
                    $this->lotModel->update($bid['lot_id'], [
                        'current_bid' => $nextHighestBid['amount'],
                        'current_bidder_id' => $nextHighestBid['user_id']
                    ]);
                } else {
                    // No other bids, reset the lot
                    $this->lotModel->update($bid['lot_id'], [
                        'current_bid' => null,
                        'current_bidder_id' => null
                    ]);
                }
            }
            
            $this->setSuccessMessage('Bid deleted successfully.');
        } else {
            $this->setErrorMessage('Error deleting bid. Please try again.');
        }
        
        $this->redirect(BASE_URL . 'admin/bids');
    }

    protected function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}