<?php
require_once 'controllers/BaseController.php';
require_once 'models/User.php';

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function dashboard()
    {
        // check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to access your dashboard.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        //just redirect admin to admin dashboard, ffs
        if($this->isAdmin()){
            $this->redirect(BASE_URL . 'admin/dashboard');
            return;
        }

        // get user data
        $userId = $_SESSION['user_id'];
        $userData = $this->userModel->getById($userId);

        // Load models
        require_once 'models/Bid.php';
        require_once 'models/Watchlist.php';

        $bidModel = new Bid();
        $watchlistModel = new Watchlist();

        // Get stats
        $stats = [
            'activeBids' => $bidModel->countUniqueActiveLotsByUser($userId),
            'wonItems' => 0,
            'wonItemsValue' => $bidModel->getTotalWonItemsValue($userId),
            'watchlist' => $watchlistModel->countByUser($userId)
        ];

        // Get won items, count
        $wonBids = $bidModel->getUserWinningBids($userId);
        $stats['wonItems'] = count($wonBids);

        $this->render('user/dashboard', [
            'title' => 'My Dashboard - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'userData' => $userData,
            'stats' => $stats
        ]);
    }

    public function bids()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->setErrorMessage('Please login to view your bids.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        // bid model
        require_once 'models/Bid.php';
        $bidModel = new Bid();
        $userId = $_SESSION['user_id'];
        $bids = $bidModel->getByUserId($userId);
        $winningBids = $bidModel->getUserWinningBids($userId);

        $this->render('user/my_bids', [
            'title' => 'My Bids - ' . SITE_NAME,
            'user' => $this->getCurrentUser(),
            'bids' => $bids,
            'winningBids' => $winningBids
        ]);
    }


    public function profile()
    {
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

    public function updateProfile()
    {
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

        // validate
        if (empty($name) || empty($email)) {
            $this->setErrorMessage('Please fill in all required fields.');
            $this->redirect(BASE_URL . 'user/profile');
            return;
        }

        // get current user data for validation
        $userData = $this->userModel->getById($userId);

        // prepare data for update
        $data = [
            'name' => $name,
            'email' => $email
        ];

        // if password change is requested
        if (!empty($newPassword)) {
            // verify current password
            if (!password_verify($currentPassword, $userData['password'])) {
                $this->setErrorMessage('Current password is incorrect.');
                $this->redirect(BASE_URL . 'user/profile');
                return;
            }

            // check if new passwords match
            if ($newPassword !== $confirmPassword) {
                $this->setErrorMessage('New passwords do not match.');
                $this->redirect(BASE_URL . 'user/profile');
                return;
            }

            $data['password'] = $newPassword;
        }

            // update user
        if ($this->userModel->update($userId, $data)) {
            // update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            $this->setSuccessMessage('Profile updated successfully.');
        } else {
            $this->setErrorMessage('Error updating profile. Please try again.');
        }

        $this->redirect(BASE_URL . 'user/profile');
    }
}