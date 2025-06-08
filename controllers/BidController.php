<?php
require_once 'controllers/BaseController.php';
require_once 'models/Bid.php';
require_once 'models/Lot.php';
require_once 'models/Auction.php';
require_once 'models/User.php';
require_once 'models/Watchlist.php';
require_once 'utils/EmailService.php';

class BidController extends BaseController
{
    private $bidModel;
    private $lotModel;
    private $auctionModel;
    private $userModel;
    private $watchlistModel;
    private $emailService;

    public function __construct()
    {
        $this->bidModel = new Bid();
        $this->lotModel = new Lot();
        $this->auctionModel = new Auction();
        $this->userModel = new User();
        $this->watchlistModel = new Watchlist();
        $this->emailService = new EmailService();
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

        // Get current highest bidder information before placing new bid
        $currentHighestBid = $this->bidModel->getCurrentHighestBid($lot_id);
        $outbidUserId = null;

        if ($currentHighestBid) {
            $outbidUserId = $currentHighestBid['user_id'];
        }

        // Calculate minimum bid amount
        $minimumBid = $this->bidModel->getNextMinimumBid($lot['current_price']);

        // Validate bid amount
        if ($bid_amount < $minimumBid) {
            $this->setErrorMessage('Your bid must be at least ' . $minimumBid . '€. The bid increment for this price range is ' . $this->bidModel->getBidIncrement($lot['current_price']) . '€.');
            $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
            return;
        }

        // Validate max amount if provided
        if ($max_amount !== null) {
            if ($max_amount < $bid_amount) {
                $this->setErrorMessage('Maximum bid amount must be greater than or equal to your bid amount.');
                $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
                return;
            }

            // Log for debugging
            error_log("User {$_SESSION['user_id']} placed a bid with amount: $bid_amount and max_amount: $max_amount on lot: $lot_id");
        } else {
            error_log("User {$_SESSION['user_id']} placed a regular bid with amount: $bid_amount on lot: $lot_id");
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
            $proxyResult = $this->bidModel->processProxyBidding($lot_id, 0);

            // Send outbid notification to the previous highest bidder if exists
            if ($outbidUserId && $outbidUserId != $_SESSION['user_id']) {
                $outbidUser = $this->userModel->getById($outbidUserId);
                if ($outbidUser) {
                    $this->emailService->sendOutbidNotification(
                        $outbidUser['email'],
                        $outbidUser['first_name'] . ' ' . $outbidUser['last_name'],
                        $lot['title'],
                        $lot_id
                    );
                    error_log("Outbid notification sent to user: {$outbidUser['email']} for lot: {$lot['title']}");
                }
            }

            if ($proxyResult) {
                if ($max_amount !== null) {
                    $this->setSuccessMessage('Your bid has been placed successfully with a maximum amount of ' . $max_amount . '€. The system will automatically bid on your behalf up to this amount.');
                } else {
                    $this->setSuccessMessage('Your bid has been placed successfully.');
                }
            } else {
                $this->setSuccessMessage('Your bid was placed, but there was an issue with proxy bidding. Please check the current status.');
            }
        } else {
            $this->setErrorMessage('There was an error placing your bid. Please try again.');
        }

        $this->redirect(BASE_URL . 'lots/view?id=' . $lot_id);
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
            $lot_id = (int) $_POST['lot_id'];
        }
        $lot_id = (int) $lot_id;

        if (!$lot_id) {
            $this->setErrorMessage('Lot ID is required.');
            $this->redirectBack();
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

        // Redirect back to the auction/lot page
        $this->redirect(BASE_URL . 'auctions/' . $lot['auction_id'] . '/lots/' . $lot_id);
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

        // Ensure POST 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setErrorMessage('Invalid request method.');
            $this->redirectBack();
            return;
        }

        // Get lot_id from parameter (from route)
        $lot_id = (int) $lot_id;

        if (!$lot_id) {
            $this->setErrorMessage('Lot ID is required.');
            $this->redirectBack();
            return;
        }

        // Fetch lot details to get auction_id for redirect, and to ensure lot exists
        $lot = $this->lotModel->getById($lot_id);
        if (!$lot) {
            $this->setErrorMessage('Lot not found.');
            // If lot not found, redirect to a general page, or user's watchlist if that's the referer
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referer, 'user/watchlist') !== false) {
                $this->redirect(BASE_URL . 'user/watchlist');
            } else {
                $this->redirect(BASE_URL . 'auctions');
            }
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
            // Redirect back to the auction/lot page
            $this->redirect(BASE_URL . 'auctions/' . $lot['auction_id'] . '/lots/' . $lot_id);
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

    /**
     * Send winning notifications when an auction ends
     * This method should be called by a cron job or when an auction is closed manually
     * 
     * @param int $auction_id The auction ID
     * @param bool $shouldRedirect Whether to redirect after sending notifications (default: true)
     * @return bool True if notifications were sent successfully
     */
    public function sendWinningNotifications($auction_id, $shouldRedirect = true)
    {
        // Check if user is admin or has appropriate permissions
        if (!$this->isAdmin()) {
            $this->setErrorMessage('You do not have permission to perform this action.');
            if ($shouldRedirect) {
                $this->redirect(BASE_URL);
            }
            return false;
        }

        // Get all lots in the auction
        $lots = $this->lotModel->getByAuctionId($auction_id);

        foreach ($lots as $lot) {
            // Get the winning bid for each lot
            $winningBid = $this->bidModel->getWinningBid($lot['id']);

            if ($winningBid) {
                // Get the winning user's details
                $winner = $this->userModel->getById($winningBid['user_id']);

                if ($winner) {
                    // Send winning notification
                    $this->emailService->sendWinningNotification(
                        $winner['email'],
                        $winner['name'],
                        $lot['title'],
                        $lot['id'],
                        $winningBid['amount']
                    );

                    error_log("Winning notification sent to user: {$winner['email']} for lot: {$lot['title']}");
                }
            }
        }

        $this->setSuccessMessage('Winning notifications sent successfully.');

        if ($shouldRedirect) {
            $this->redirect(BASE_URL . 'auctions/edit/' . $auction_id);
        }

        return true;
    }
}   