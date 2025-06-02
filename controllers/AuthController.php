<?php
require_once 'controllers/BaseController.php';
require_once 'models/User.php';

class AuthController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $this->render('auth/login', [
            'title' => 'Login - ' . SITE_NAME
        ]);
    }
    
    public function loginPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($email) || empty($password)) {
            $this->setErrorMessage('Please enter both email and password');
            $this->redirect(BASE_URL . 'login');
            return;
        }
        
        // Attempt login
        $user = $this->userModel->validateLogin($email, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            $this->setSuccessMessage('You have been logged in successfully');
            $this->redirectToDashboard();
        } else {
            $this->setErrorMessage('Invalid email or password');
            $this->redirect(BASE_URL . 'login');
        }
    }
    
    public function register() {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $this->render('auth/register', [
            'title' => 'Register - ' . SITE_NAME
        ]);
    }
    
    public function registerPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'register');
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate input
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            $this->setErrorMessage('Please fill in all fields');
            $this->redirect(BASE_URL . 'register');
            return;
        }
        
        if ($password !== $confirmPassword) {
            $this->setErrorMessage('Passwords do not match');
            $this->redirect(BASE_URL . 'register');
            return;
        }
        
        // Check if email already exists
        if ($this->userModel->getByEmail($email)) {
            $this->setErrorMessage('Email already exists');
            $this->redirect(BASE_URL . 'register');
            return;
        }
        
        // Create new user
        $userId = $this->userModel->create($name, $email, $password);
        
        if ($userId) {
            $this->setSuccessMessage('Registration successful. You can now login.');
            $this->redirect(BASE_URL . 'login');
        } else {
            $this->setErrorMessage('Registration failed. Please try again.');
            $this->redirect(BASE_URL . 'register');
        }
    }
    
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        $this->redirect(BASE_URL);
    }
    
    // Helper method to redirect users to their appropriate dashboard
    private function redirectToDashboard() {
        if ($_SESSION['user_role'] === 'admin') {
            $this->redirect(BASE_URL . 'admin/dashboard');
        } else {
            $this->redirect(BASE_URL . 'user/dashboard');
        }
    }
} 