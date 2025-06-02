<?php

class AuctionsController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Get filter from query string
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        
        // Get auctions based on filter
        $auctionModel = new AuctionModel();
        
        if ($filter === 'all') {
            // For public view, only show upcoming, live, and ended auctions (no drafts)
            $auctions = $auctionModel->getPublicAuctions();
        } else {
            $auctions = $auctionModel->getAuctionsByStatus($filter);
        }
        
        $this->renderView('auctions/index', [
            'auctions' => $auctions,
            'filter' => $filter
        ]);
    }
    
    public function view() {
        // Get auction ID from query string
        $auctionId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$auctionId) {
            $_SESSION['error_message'] = "Auction ID is required.";
            $this->redirect(BASE_URL . 'auctions');
            return;
        }
        
        // Get auction details
        $auctionModel = new AuctionModel();
        $auction = $auctionModel->getAuctionById($auctionId);
        
        if (!$auction) {
            $_SESSION['error_message'] = "Auction not found.";
            $this->redirect(BASE_URL . 'auctions');
            return;
        }
        
        // If auction is in draft status and user is not admin, redirect to auctions page
        if ($auction['status'] === 'draft' && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')) {
            $_SESSION['error_message'] = "This auction is not available for viewing.";
            $this->redirect(BASE_URL . 'auctions');
            return;
        }
        
        // Get lots for this auction
        $lotModel = new LotModel();
        $lots = $lotModel->getLotsByAuctionId($auctionId);
        
        $this->renderView('auctions/view', [
            'auction' => $auction,
            'lots' => $lots
        ]);
    }
    
    public function create() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        $this->renderAdminView('create_auction', []);
    }
    
    public function store() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $status = $_POST['status'] ?? 'draft';
            
            // Validate input
            if (empty($title) || empty($description) || empty($startDate) || empty($endDate)) {
                $_SESSION['error_message'] = "All fields are required.";
                $this->redirect(BASE_URL . 'auctions/create');
                return;
            }
            
            // Create auction
            $auctionModel = new AuctionModel();
            $auctionData = [
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status
            ];
            
            $auctionId = $auctionModel->createAuction($auctionData);
            
            if ($auctionId) {
                $_SESSION['success_message'] = "Auction created successfully.";
                $this->redirect(BASE_URL . 'admin/auctions');
            } else {
                $_SESSION['error_message'] = "Failed to create auction. Please try again.";
                $this->redirect(BASE_URL . 'auctions/create');
            }
        }
    }
    
    public function edit($id) {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Get auction details
        $auctionModel = new AuctionModel();
        $auction = $auctionModel->getAuctionById($id);
        
        if (!$auction) {
            $_SESSION['error_message'] = "Auction not found.";
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }
        
        // Get lots for this auction
        $lotModel = new LotModel();
        $lots = $lotModel->getLotsByAuctionId($id);
        
        $this->renderAdminView('edit_auction', [
            'auction' => $auction,
            'lots' => $lots
        ]);
    }
    
    public function update($id) {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $status = $_POST['status'] ?? 'draft';
            
            // Validate input
            if (empty($title) || empty($description) || empty($startDate) || empty($endDate)) {
                $_SESSION['error_message'] = "All fields are required.";
                $this->redirect(BASE_URL . 'auctions/edit/' . $id);
                return;
            }
            
            // Update auction
            $auctionModel = new AuctionModel();
            $auctionData = [
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status
            ];
            
            $success = $auctionModel->updateAuction($id, $auctionData);
            
            if ($success) {
                $_SESSION['success_message'] = "Auction updated successfully.";
                $this->redirect(BASE_URL . 'admin/auctions');
            } else {
                $_SESSION['error_message'] = "Failed to update auction. Please try again.";
                $this->redirect(BASE_URL . 'auctions/edit/' . $id);
            }
        }
    }
    
    public function delete() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Get auction ID from query string
        $auctionId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$auctionId) {
            $_SESSION['error_message'] = "Auction ID is required.";
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }
        
        // Check if auction has lots
        $lotModel = new LotModel();
        $lotCount = $lotModel->countLotsByAuctionId($auctionId);
        
        if ($lotCount > 0) {
            $_SESSION['error_message'] = "Cannot delete auction with lots. Please delete all lots first.";
            $this->redirect(BASE_URL . 'auctions/edit/' . $auctionId);
            return;
        }
        
        // Delete auction
        $auctionModel = new AuctionModel();
        $success = $auctionModel->deleteAuction($auctionId);
        
        if ($success) {
            $_SESSION['success_message'] = "Auction deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete auction. Please try again.";
        }
        
        $this->redirect(BASE_URL . 'admin/auctions');
    }
    
    private function checkAdminAccess() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Please log in to access this page.";
            $this->redirect(BASE_URL . 'login');
            exit;
        }
        
        // Check if user is admin
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error_message'] = "You don't have permission to access this page.";
            $this->redirect(BASE_URL);
            exit;
        }
    }
} 