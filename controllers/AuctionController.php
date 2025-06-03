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
                // Get all auctions except drafts for public view
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
     * Show individual auction
     */
    public function view($id = null)
    {
        if (!$id) {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        }

        $auction = $this->auctionModel->getById($id);

        if (!$auction) {
            $this->setErrorMessage('Auction not found');
            $this->redirect(BASE_URL . 'auctions');
            return;
        }

        // Get lots for this auction
        $lots = $this->lotModel->getByAuctionId($id);

        $this->render('auctions/view', [
            'title' => $auction['title'] . ' - ' . SITE_NAME,
            'auction' => $auction,
            'lots' => $lots,
            'user' => $this->getCurrentUser()
        ]);
    }

    /**
     * Admin: Auction management dashboard
     */
    public function manage()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            return;
        }

        $auctions = $this->auctionModel->getAll();

        $this->render('admin/auctions', [
            'title' => 'Manage Auctions - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'auctions' => $auctions
        ]);
    }

    /**
     * Admin: Create auction form
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
     * Admin: Store new auction
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

        // Basic validation
        if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
            $this->setErrorMessage('Please fill in all required fields');
            $this->redirect(BASE_URL . 'auctions/create');
            return;
        }

        // Handle image upload if present
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
     * Admin: Edit auction form
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
     * Admin: Update auction
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

        // Basic validation
        if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
            $this->setErrorMessage('Please fill in all required fields');
            $this->redirect(BASE_URL . 'auctions/edit/' . $id);
            return;
        }

        // Get current auction data for image comparison
        $currentAuction = $this->auctionModel->getById($id);
        $image_path = $currentAuction['image_path'];

        // Handle image upload if present
        if (isset($_FILES['auction_image']) && $_FILES['auction_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if (!empty($image_path) && file_exists($image_path)) {
                @unlink($image_path);
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
     * Handle image upload for auctions
     */
    private function handleImageUpload($file, $folder)
    {
        $upload_dir = 'uploads/' . $folder . '/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Check file size (6MB max)
        if ($file['size'] > 6 * 1024 * 1024) {
            return false;
        }
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filepath;
        }
        
        return false;
    }

    /**
     * Admin: Delete auction
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

        // Check if auction has any lots
        $lots = $this->lotModel->getByAuctionId($id);
        if (count($lots) > 0) {
            $this->setErrorMessage('Cannot delete auction with lots. Please remove all lots first.');
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }

        // Delete auction
        $result = $this->auctionModel->delete($id);

        if ($result) {
            $this->setSuccessMessage('Auction deleted successfully');
        } else {
            $this->setErrorMessage('Error deleting auction');
        }

        $this->redirect(BASE_URL . 'admin/auctions');
    }
}