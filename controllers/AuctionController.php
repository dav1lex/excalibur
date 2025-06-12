<?php
require_once 'controllers/BaseController.php';
require_once 'models/Auction.php';
require_once 'models/Lot.php';
require_once 'models/User.php';

class AuctionController extends BaseController
{
    private $auctionModel;
    private $lotModel;
    private $userModel;

    public function __construct()
    {
        $this->auctionModel = new Auction();
        $this->lotModel = new Lot();
        $this->userModel = new User();
    }

    /**
     * List all auctions page
     */
    public function index()
    {
        $filter = $_GET['filter'] ?? 'all';

        // Get auctions based on filter
        switch ($filter) {
            case 'upcoming':
                $auctions = $this->auctionModel->getUpcomingAuctions();
                $title = 'Upcoming Auctions';
                break;
            case 'live':
                $auctions = $this->auctionModel->getLiveAuctions();
                $title = 'Live Auctions';
                break;
            case 'ended':
                $auctions = $this->auctionModel->getEndedAuctions();
                $title = 'Past Auctions';
                break;
            default:
                // By default, we show all auctions except 'drafts'.
                // This keeps unfinished auctions from showing up on the public site.
                $auctions = $this->auctionModel->getAllPublic();
                $title = 'All Auctions';
        }

        $this->render('auctions/index', [
            'title' => $title . ' - ' . SITE_NAME,
            'auctions' => $auctions,
            'filter' => $filter,
            'user' => $this->getCurrentUser()
        ]);
    }

    /**
     * This function is smart. It can show a whole auction page OR a single lot page.
     *
     * Example URL for a whole auction: /auctions/12
     * Example URL for a single lot:   /auctions/12/lots/5
     *
     * @param string|int $auction_id_from_url The ID of the auction from the URL.
     * @param string|int|null $lot_id_from_url (Optional) The ID of the lot from the URL.
     */
    public function view($auction_id_from_url, $lot_id_from_url = null)
    {
        $auction_id = (int) $auction_id_from_url;
        $auction = $this->auctionModel->getById($auction_id);

        if (!$auction) {
            $this->setErrorMessage('Auction not found.');
            $this->redirect(BASE_URL . 'auctions');
            return;
        }

        if ($lot_id_from_url !== null) {
            // URL indicates a specific lot view: /auctions/{auction_id}/lots/{lot_id}
            $this->_displayLotPage($auction, (int) $lot_id_from_url);
        } else {
            // URL indicates an auction view: /auctions/{auction_id}
            $this->_displayAuctionPage($auction);
        }
    }

    /**
     *  function to show the main page for an auction, with all its lots listed.
     *
     * @param array $auction The auction data.
     */
    private function _displayAuctionPage(array $auction)
    {
        $lots = $this->lotModel->getByAuctionId($auction['id']);

        $this->render('auctions/view', [
            'title' => $auction['title'] . ' - ' . SITE_NAME,
            'auction' => $auction,
            'lots' => $lots,
            'user' => $this->getCurrentUser()
        ]);
    }

    /**
     *  function to show the detailed page for just one lot.
     *
     * @param array $auction The parent auction data.
     * @param int $lot_id The ID of the lot to display.
     */
    private function _displayLotPage(array $auction, int $lot_id)
    {
        $current_lot = $this->lotModel->getById($lot_id);

        // Verify the lot exists and belongs to the specified auction
        if (!$current_lot || $current_lot['auction_id'] != $auction['id']) {
            $this->setErrorMessage('Lot not found in this auction.');
            $this->redirect(BASE_URL . 'auctions/' . $auction['id']);
            return;
        }

        $user = $this->getCurrentUser();
        $bidModel = new Bid();
        $watchlistModel = new Watchlist();

        $bids = $bidModel->getByLotId($lot_id);

        $inWatchlist = false;
        if ($user) {
            $inWatchlist = $watchlistModel->isInWatchlist($user['id'], $lot_id);
        }

        // We want to show "related items", so we grab all other lots from the same auction.
        $allLotsInAuction = $this->lotModel->getByAuctionId($auction['id']);
        $relatedLots = array_filter($allLotsInAuction, function ($related_lot_item) use ($lot_id) {
            return $related_lot_item['id'] != $lot_id;
        });

        $data = [
            'title' => $current_lot['title'] . ' - ' . $auction['title'] . ' - ' . SITE_NAME,
            'lot' => $current_lot,
            'auction' => $auction,
            'bids' => $bids,
            'user' => $user,
            'inWatchlist' => $inWatchlist,
            'relatedLots' => $relatedLots
        ];

        // The 'lots/view.php' template shows all the details for a single lot.
        // It needs both the $lot data and the parent $auction data for things like checking the auction status or end date.
        $this->render('lots/view', $data);
    }

    /**

    /**
     * Admin: Create auction 
     */
    public function create()
    {
        // Check if user is admin
        $this->isAdmin();

        $this->render('admin/create_auction', [
            'title' => 'Create Auction - ' . SITE_NAME,
            'user' => $this->getCurrentUser()
        ]);
    }

    /**
     * Admin: Takes the info from the 'create auction' form and saves it to the database.
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

        // Validate and sanitize input
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        $image_path = null;

        // Simple check to make sure the important fields aren't empty.
        if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
            $this->setErrorMessage('Please fill in all required fields');
            $this->redirect(BASE_URL . 'auctions/create');
            return;
        }

        // If an image was uploaded, handle it.
        if (isset($_FILES['auction_image']) && $_FILES['auction_image']['error'] === UPLOAD_ERR_OK) {
            $image_path = $this->handleImageUpload($_FILES['auction_image'], 'auctions');
            if (!$image_path) {
                $this->setErrorMessage('Error uploading image. Please try again.');
                $this->redirect(BASE_URL . 'auctions/create');
                return;
            }
        }

        // Create auction
        $auctionId = $this->auctionModel->create([
            'title' => $title,
            'description' => $description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'image_path' => $image_path
        ]);

        if ($auctionId) {
            $this->setSuccessMessage('Auction created successfully');
            $this->redirect(BASE_URL . 'admin/auctions');
        } else {
            $this->setErrorMessage('Error creating auction');
            $this->redirect(BASE_URL . 'auctions/create');
        }
    }

    /**
     * Admin: Shows the page for editing an existing auction.
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

        $auction = $this->auctionModel->getById($id);

        if (!$auction) {
            $this->setErrorMessage('Auction not found');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        // Get lots for this auction
        $lots = $this->lotModel->getByAuctionId($id);

        $this->render('admin/edit_auction', [
            'title' => 'Edit Auction - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'auction' => $auction,
            'lots' => $lots
        ]);
    }

    /**
     * Admin: Takes the new info from the 'edit auction' form and updates the database.
     */
    public function update($id = null)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        // The ID can come from the URL path (like /edit/12) or a query string (?id=12). Made it flexible.
        if (!$id && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        $id = (int) $id;

        // Validate and sanitize input
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'draft';

        if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
            $this->setErrorMessage('Please fill in all required fields');
            $this->redirect(BASE_URL . 'auctions/edit/' . $id);
            return;
        }

        // Get the current auction data to find the old image path.
        $currentAuction = $this->auctionModel->getById($id);
        $image_path = $currentAuction['image_path'];

        // If a new image is uploaded, deal with it.
        if (isset($_FILES['auction_image']) && $_FILES['auction_image']['error'] === UPLOAD_ERR_OK) {
            // A new image was uploaded, so let's delete the old one first.
            if (!empty($image_path)) {
                $this->deleteAuctionImage($image_path);
            }

            $image_path = $this->handleImageUpload($_FILES['auction_image'], 'auctions');
            if (!$image_path) {
                $this->setErrorMessage('Error uploading image. Please try again.');
                $this->redirect(BASE_URL . 'auctions/edit/' . $id);
                return;
            }
        }

        // Update auction
        $result = $this->auctionModel->update($id, [
            'title' => $title,
            'description' => $description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'image_path' => $image_path
        ]);

        if ($result) {
            $this->setSuccessMessage('Auction updated successfully');
        } else {
            $this->setErrorMessage('Error updating auction');
        }

        $this->redirect(BASE_URL . 'auctions/edit/' . $id);
    }

    /**
     * Takes an uploaded image, checks if it's okay (size, type), and saves it.
     *
     * @param array $file The $_FILES entry for the uploaded image.
     * @param string $folder The destination folder inside 'public/uploads/'.
     * @return string|false The path to the saved file if it worked, or false if it failed.
     */
    private function handleImageUpload($file, $folder)
    {
        $upload_dir = 'public/uploads/' . $folder . '/';

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Max file size is 6MB. Don't want people uploading huge files.
        if ($file['size'] > 6 * 1024 * 1024) {
            return false;
        }

        // Only allow common image types.
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }

        // Create a unique filename so we don't accidentally overwrite anything.
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filepath;
        }

        return false;
    }

    /**
     * Deletes an image file from the server.
     * 
     * @param string $image_path Path to the image file.
     * @return bool True if it worked or the file was already gone. False if there was an error.
     */
    private function deleteAuctionImage($image_path)
    {
        if (empty($image_path)) {
            return true; // Nothing to do.
        }
        
        if (file_exists($image_path)) {
            // The @ symbol stops PHP from throwing a warning if it fails. Less noise.
            return @unlink($image_path);
        }
        
        return true; // If the file isn't there, it's already "deleted". So that's a success.
    }

    /**
     * Admin: Deletes an auction from the database and also deletes its image.
     */
    public function delete($id = null)
    {
        // Check if user is admin
        $this->ensureAdmin();

        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        // IMPORTANT: You can't delete an auction if it still has lots inside.
        // This is to stop us from having "orphaned" lots that belong to nothing.
        $lots = $this->lotModel->getByAuctionId($id);
        if (count($lots) > 0) {
            $this->setErrorMessage('Cannot delete auction with lots. Please remove all lots first.');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        // Delete auction
        $result = $this->auctionModel->delete($id);
        $currentAuction = $this->auctionModel->getById($id);
        if (!empty($currentAuction['image_path']) && file_exists($currentAuction['image_path'])) {
            $this->deleteAuctionImage($currentAuction['image_path']);
        }
        if ($result) {
            $this->setSuccessMessage('Auction deleted successfully');
        } else {
            $this->setErrorMessage('Error deleting auction');
        }
      
        $this->redirect(BASE_URL . 'admin/auctions');
    }

    /**
     * Admin: Lets an admin manually end a 'live' auction and send emails to winners.
     */
    public function endAuction($id = null)
    {
        // Check if user is admin
        $this->ensureAdmin();

        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $auction = $this->auctionModel->getById($id);
        if (!$auction) {
            $this->setErrorMessage('Auction not found');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        // You can only manually end an auction that is 'live'. Makes sense.
        if ($auction['status'] !== 'live') {
            $this->setErrorMessage('Only live auctions can be ended manually.');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        // Update auction status to ended
        $result = $this->auctionModel->update($id, [
            'status' => 'ended'
        ]);

        if ($result) {
            // The BidController handles sending winner emails, so we call it here to do its job.
            // This keeps the code for auctions separate from the code for bidding.
            require_once 'controllers/BidController.php';
            $bidController = new BidController();
            $bidController->sendWinningNotifications($id, true, false);

            $this->setSuccessMessage('Auction ended successfully and winning notifications sent.');
        } else {
            $this->setErrorMessage('Error ending auction');
        }

        $this->redirect(BASE_URL . 'admin/auctions');
    }
}