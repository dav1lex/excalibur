<?php

class LotsController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function create() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Get auction ID from query string
        $auctionId = isset($_GET['auction_id']) ? $_GET['auction_id'] : null;
        
        if (!$auctionId) {
            $_SESSION['error_message'] = "Auction ID is required to create a lot.";
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }
        
        // Get auction details
        $auctionModel = new AuctionModel();
        $auction = $auctionModel->getAuctionById($auctionId);
        
        if (!$auction) {
            $_SESSION['error_message'] = "Auction not found.";
            $this->redirect(BASE_URL . 'admin/auctions');
            return;
        }
        
        $this->renderAdminView('create_lot', [
            'auction' => $auction
        ]);
    }
    
    public function store() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auctionId = $_POST['auction_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $lotNumber = $_POST['lot_number'] ?? '';
            $description = $_POST['description'] ?? '';
            $startingPrice = $_POST['starting_price'] ?? 0;
            $reservePrice = $_POST['reserve_price'] ?? null;
            
            // Validate input
            if (empty($auctionId) || empty($title) || empty($lotNumber) || empty($description) || empty($startingPrice)) {
                $_SESSION['error_message'] = "All required fields must be filled out.";
                $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auctionId);
                return;
            }
            
            // Handle image upload
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
                
                if ($imagePath === false) {
                    $_SESSION['error_message'] = "Failed to upload image. Please try again.";
                    $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auctionId);
                    return;
                }
            }
            
            // Create lot
            $lotModel = new LotModel();
            $lotData = [
                'auction_id' => $auctionId,
                'title' => $title,
                'lot_number' => $lotNumber,
                'description' => $description,
                'starting_price' => $startingPrice,
                'current_price' => $startingPrice, // Initially set to starting price
                'reserve_price' => $reservePrice,
                'image_path' => $imagePath
            ];
            
            $success = $lotModel->createLot($lotData);
            
            if ($success) {
                $_SESSION['success_message'] = "Lot created successfully.";
                $this->redirect(BASE_URL . 'auctions/edit/' . $auctionId);
            } else {
                $_SESSION['error_message'] = "Failed to create lot. Please try again.";
                $this->redirect(BASE_URL . 'lots/create?auction_id=' . $auctionId);
            }
        }
    }
    
    public function edit() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Get lot ID from query string
        $lotId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$lotId) {
            $_SESSION['error_message'] = "Lot ID is required.";
            $this->redirect(BASE_URL . 'admin/lots');
            return;
        }
        
        // Get lot details
        $lotModel = new LotModel();
        $lot = $lotModel->getLotById($lotId);
        
        if (!$lot) {
            $_SESSION['error_message'] = "Lot not found.";
            $this->redirect(BASE_URL . 'admin/lots');
            return;
        }
        
        // Get auction details
        $auctionModel = new AuctionModel();
        $auction = $auctionModel->getAuctionById($lot['auction_id']);
        
        // Get bid history
        $bidModel = new BidModel();
        $bids = $bidModel->getBidsByLotId($lotId);
        
        $this->renderAdminView('edit_lot', [
            'lot' => $lot,
            'auction' => $auction,
            'bids' => $bids
        ]);
    }
    
    public function update($id) {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auctionId = $_POST['auction_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $lotNumber = $_POST['lot_number'] ?? '';
            $description = $_POST['description'] ?? '';
            $startingPrice = $_POST['starting_price'] ?? 0;
            $currentPrice = $_POST['current_price'] ?? $startingPrice;
            $reservePrice = $_POST['reserve_price'] ?? null;
            
            // Validate input
            if (empty($auctionId) || empty($title) || empty($lotNumber) || empty($description) || empty($startingPrice)) {
                $_SESSION['error_message'] = "All required fields must be filled out.";
                $this->redirect(BASE_URL . 'lots/edit?id=' . $id);
                return;
            }
            
            // Get existing lot to check for image
            $lotModel = new LotModel();
            $existingLot = $lotModel->getLotById($id);
            
            if (!$existingLot) {
                $_SESSION['error_message'] = "Lot not found.";
                $this->redirect(BASE_URL . 'admin/lots');
                return;
            }
            
            // Handle image upload if a new image is provided
            $imagePath = $existingLot['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImagePath = $this->handleImageUpload($_FILES['image']);
                
                if ($newImagePath === false) {
                    $_SESSION['error_message'] = "Failed to upload image. Please try again.";
                    $this->redirect(BASE_URL . 'lots/edit?id=' . $id);
                    return;
                }
                
                // Delete old image if exists
                if (!empty($existingLot['image_path']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $existingLot['image_path'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $existingLot['image_path']);
                }
                
                $imagePath = $newImagePath;
            }
            
            // Update lot
            $lotData = [
                'auction_id' => $auctionId,
                'title' => $title,
                'lot_number' => $lotNumber,
                'description' => $description,
                'starting_price' => $startingPrice,
                'current_price' => $currentPrice,
                'reserve_price' => $reservePrice,
                'image_path' => $imagePath
            ];
            
            $success = $lotModel->updateLot($id, $lotData);
            
            if ($success) {
                $_SESSION['success_message'] = "Lot updated successfully.";
                $this->redirect(BASE_URL . 'auctions/edit/' . $auctionId);
            } else {
                $_SESSION['error_message'] = "Failed to update lot. Please try again.";
                $this->redirect(BASE_URL . 'lots/edit?id=' . $id);
            }
        }
    }
    
    public function delete() {
        // Check if user is logged in and is admin
        $this->checkAdminAccess();
        
        // Get lot ID from query string
        $lotId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$lotId) {
            $_SESSION['error_message'] = "Lot ID is required.";
            $this->redirect(BASE_URL . 'admin/lots');
            return;
        }
        
        // Get lot details to know which auction to redirect back to
        $lotModel = new LotModel();
        $lot = $lotModel->getLotById($lotId);
        
        if (!$lot) {
            $_SESSION['error_message'] = "Lot not found.";
            $this->redirect(BASE_URL . 'admin/lots');
            return;
        }
        
        $auctionId = $lot['auction_id'];
        
        // Delete image if exists
        if (!empty($lot['image_path']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $lot['image_path'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $lot['image_path']);
        }
        
        // Delete lot
        $success = $lotModel->deleteLot($lotId);
        
        if ($success) {
            $_SESSION['success_message'] = "Lot deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete lot. Please try again.";
        }
        
        $this->redirect(BASE_URL . 'auctions/edit/' . $auctionId);
    }
    
    public function view() {
        // Get lot ID from query string
        $lotId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$lotId) {
            $_SESSION['error_message'] = "Lot ID is required.";
            $this->redirect(BASE_URL . 'auctions');
            return;
        }
        
        // Get lot details with auction info
        $lotModel = new LotModel();
        $lot = $lotModel->getLotWithAuctionInfo($lotId);
        
        if (!$lot) {
            $_SESSION['error_message'] = "Lot not found.";
            $this->redirect(BASE_URL . 'auctions');
            return;
        }
        
        // Get related lots from the same auction
        $relatedLots = $lotModel->getRelatedLots($lotId, $lot['auction_id']);
        
        // Get user info if logged in
        $user = null;
        if (isset($_SESSION['user_id'])) {
            $userModel = new UserModel();
            $user = $userModel->getUserById($_SESSION['user_id']);
        }
        
        // Get bid history
        $bidModel = new BidModel();
        $bids = $bidModel->getBidsByLotId($lotId);
        
        $this->renderView('lots/view', [
            'lot' => $lot,
            'auction' => [
                'id' => $lot['auction_id'],
                'title' => $lot['auction_title'],
                'auction_status' => $lot['auction_status'],
                'auction_start_date' => $lot['auction_start_date'],
                'auction_end_date' => $lot['auction_end_date']
            ],
            'relatedLots' => $relatedLots,
            'user' => $user,
            'bids' => $bids
        ]);
    }
    
    private function handleImageUpload($file) {
        // Check file size (max 6MB)
        if ($file['size'] > 6 * 1024 * 1024) {
            return false;
        }
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = 'public/img/lots/';
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $uploadDir)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' . $targetPath)) {
            return '/' . $targetPath; // Return path relative to document root
        }
        
        return false;
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