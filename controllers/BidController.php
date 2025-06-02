<?php
require_once 'controllers/BaseController.php';
require_once 'models/Bid.php';
require_once 'models/Lot.php';
require_once 'models/Auction.php';
require_once 'models/User.php';
require_once 'models/Watchlist.php';

class BidController extends BaseController
{
    private $bidModel;
    private $lotModel;
    private $auctionModel;
    private $userModel;
    private $watchlistModel;

    public function __construct()
    {
        $this->bidModel = new Bid();
        $this->lotModel = new Lot();
        $this->auctionModel = new Auction();
        $this->userModel = new User();
        $this->watchlistModel = new Watchlist();
    }

    /**
     * Place a bid
     */
    public function place()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('You must be logged in to place bids.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL);
            return;
        }

        // Get and validate input
        $lot_id = isset($_POST['lot_id']) ? (int) $_POST['lot_id'] : 0;
        $bid_amount = isset($_POST['bid_amount']) ? (int) $_POST['bid_amount'] : 0;
        $max_amount = isset($_POST['max_amount']) && !empty($_POST['max_amount']) ? (int) $_POST['max_amount'] : null;

        // Get lot information
        $lot = $this->lotModel->getById($lot_id);

        if (!$lot) {
            $this->setErrorMessage('Lot not found.');
            $this->redirect(BASE_URL . 'auctions');
            return;
        }

        // Check if auction is live
        if ($lot['auction_status'] !== 'live') {
            $this->setErrorMessage('Bidding is only allowed on live auctions.');
            $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
            return;
        }

        // Validate bid amount
        if ($bid_amount <= $lot['current_price']) {
            $this->setErrorMessage('Your bid must be higher than the current bid.');
            $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
            return;
        }

        // Validate max amount if provided
        if ($max_amount !== null && $max_amount < $bid_amount) {
            $this->setErrorMessage('Maximum bid amount must be greater than or equal to your bid amount.');
            $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
            return;
        }

        // Place the bid
        $bidData = [
            'user_id' => $_SESSION['user_id'],
            'lot_id' => $lot_id,
            'amount' => $bid_amount,
            'max_amount' => $max_amount
        ];

        $bidId = $this->bidModel->placeBid($bidData);

        if ($bidId) {
            // Process proxy bidding
            $this->bidModel->processProxyBidding($lot_id);

            $this->setSuccessMessage('Your bid has been placed successfully.');
        } else {
            $this->setErrorMessage('There was an error placing your bid. Please try again.');
        }

        $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
    }

    /**
     * View user's bids
     */
    public function myBids()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('You must be logged in to view your bids.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        $user_id = $_SESSION['user_id'];
        $bids = $this->bidModel->getByUserId($user_id);
        $winningBids = $this->bidModel->getUserWinningBids($user_id);

        $this->render('user/my_bids', [
            'title' => 'My Bids - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'bids' => $bids,
            'winningBids' => $winningBids
        ]);
    }

    /**
     * Add lot to watchlist
     */
    public function addToWatchlist($lot_id = null)
    {
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('You must be logged in to add items to your watchlist.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL);
            return;
        }

        if (!$lot_id && isset($_POST['lot_id'])) {
            $lot_id = (int)$_POST['lot_id'];
        }
        $lot_id = (int)$lot_id;

        if (!$lot_id) {
            $this->setErrorMessage('Lot ID is required.');
            $this->redirectBack(); // implement later
            return;
        }

        // Verify lot exists
        $lot = $this->lotModel->getById($lot_id);
        if (!$lot) {
            $this->setErrorMessage('Lot not found.');
            $this->redirect(BASE_URL . 'auctions');
            return;
        }

        // Add to watchlist
        $result = $this->watchlistModel->add($_SESSION['user_id'], $lot_id);

        if ($result) {
            $this->setSuccessMessage('Lot added to your watchlist.');
        } else {
            $this->setErrorMessage('There was an error adding this lot to your watchlist.');
        }

        // Redirect back 
        $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
    }

    /**
     * Remove lot from watchlist
     */
    public function removeFromWatchlist($lot_id = null)
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('You must be logged in to manage your watchlist.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        // Ensure this is a POST request now
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setErrorMessage('Invalid request method.');
            $this->redirectBack(); 
            return;
        }

        // Get lot_id from parameter (from route)
        $lot_id = (int)$lot_id;

        if (!$lot_id) {
            $this->setErrorMessage('Lot ID is required.');
            $this->redirectBack();
            return;
        }

        // Remove from watchlist
        $result = $this->watchlistModel->remove($_SESSION['user_id'], $lot_id);

        if ($result) {
            $this->setSuccessMessage('Lot removed from your watchlist.');
        } else {
            $this->setErrorMessage('There was an error removing this lot from your watchlist.');
        }

        // Check if we're coming from the watchlist page
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'user/watchlist') !== false) {
            $this->redirect(BASE_URL . 'user/watchlist');
        } else {
            $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
        }
    }

    /**
     * View user's watchlist
     */
    public function watchlist()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('You must be logged in to view your watchlist.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        $user_id = $_SESSION['user_id'];
        $watchlist = $this->watchlistModel->getByUserId($user_id);

        $this->render('user/watchlist', [
            'title' => 'My Watchlist - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'watchlist' => $watchlist
        ]);
    }
}