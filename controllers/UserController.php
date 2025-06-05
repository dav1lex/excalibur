<?php
require_once 'controllers/BaseController.php';
require_once 'models/User.php';

class UserController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function dashboard() {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to access your dashboard.');
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        // Get user data
        $userId = $_SESSION['user_id'];
        $userData = $this->userModel->getById($userId);
        
        // Load required models
        require_once 'models/Bid.php';
        require_once 'models/Watchlist.php';
        
        $bidModel = new Bid();
        $watchlistModel = new Watchlist();
        
        // Get user stats
        $stats = [
            'activeBids' => $bidModel->countUniqueActiveLotsByUser($userId),
            'wonItems' => 0,
            'wonItemsValue' => $bidModel->getTotalWonItemsValue($userId),
            'watchlist' => $watchlistModel->countByUser($userId)
        ];
        
        // Get count of won items
        $wonBids = $bidModel->getUserWinningBids($userId);
        $stats['wonItems'] = count($wonBids);
        
        $this->render('user/dashboard', [
            'title' => 'My Dashboard - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'userData' => $userData,
            'stats' => $stats
        ]);
    }
    
    public function bids() {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to view your bids.');
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        $this->render('user/bids', [
            'title' => 'My Bids - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'bids' => [] // Will implement in next phase
        ]);
    }
    
    public function watchlist() {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to view your watchlist.');
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        $this->render('user/watchlist', [
            'title' => 'My Watchlist - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'watchlist' => [] // Will implement in next phase
        ]);
    }
    
    public function profile() {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to edit your profile.');
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $userData = $this->userModel->getById($userId);
        
        $this->render('user/profile', [
            'title' => 'My Profile - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'userData' => $userData
        ]);
    }
    
    public function updateProfile() {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to update your profile.');
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'user/profile');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate input
        if (empty($name) || empty($email)) {
            $this->setErrorMessage('Please fill in all required fields.');
            $this->redirect(BASE_URL . 'user/profile');
            return;
        }
        
        // Get current user data for validation
        $userData = $this->userModel->getById($userId);
        
        // Prepare data for update
        $data = [
            'name' => $name,
            'email' => $email
        ];
        
        // If password change is requested
        if (!empty($newPassword)) {
            // Verify current password
            if (!password_verify($currentPassword, $userData['password'])) {
                $this->setErrorMessage('Current password is incorrect.');
                $this->redirect(BASE_URL . 'user/profile');
                return;
            }
            
            // Check if new passwords match
            if ($newPassword !== $confirmPassword) {
                $this->setErrorMessage('New passwords do not match.');
                $this->redirect(BASE_URL . 'user/profile');
                return;
            }
            
            $data['password'] = $newPassword;
        }
        
        // Update user
        if ($this->userModel->update($userId, $data)) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            $this->setSuccessMessage('Profile updated successfully.');
        } else {
            $this->setErrorMessage('Error updating profile. Please try again.');
        }
        
        $this->redirect(BASE_URL . 'user/profile');
    }
} 