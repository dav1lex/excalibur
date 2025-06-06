<?php
require_once 'controllers/BaseController.php';
require_once 'models/User.php';
require_once 'utils/EmailService.php';

class AuthController extends BaseController
{
    private $userModel;
    private $emailService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->emailService = new EmailService();
    }

    public function login()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $this->render('auth/login', [
            'title' => 'Login - ' . SITE_NAME
        ]);
    }

    public function loginPost()
    {
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

        if ($user === 'unconfirmed') {
            $this->setErrorMessage('Please confirm your email address before logging in');
            $this->redirect(BASE_URL . 'login');
            return;
        } elseif ($user) {
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
            return;
        }
    }

    public function register()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $this->render('auth/register', [
            'title' => 'Register - ' . SITE_NAME
        ]);
    }

    public function registerPost()
    {
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
        $result = $this->userModel->create($name, $email, $password);

        if ($result) {
            // Send confirmation email
            if ($this->emailService->sendConfirmationEmail($email, $name, $result['confirmation_token'])) {
                $this->setSuccessMessage('Registration successful. Please check your email to confirm your account.');
            } else {
                $this->setSuccessMessage('Registration successful, but confirmation did not send.');
            }
            $this->redirect(BASE_URL . 'login');
        } else {
            $this->setErrorMessage('Registration failed. Please try again.');
            $this->redirect(BASE_URL . 'register');
        }
    }

    public function confirmEmail()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->setErrorMessage('Invalid confirmation link');
            $this->redirect(BASE_URL);
            return;
        }

        // Get user by token
        $user = $this->userModel->getUserByToken($token);

        if (!$user) {
            $this->setErrorMessage('Invalid or expired confirmation link');
            $this->redirect(BASE_URL);
            return;
        }

        // Confirm email
        if ($this->userModel->confirmEmail($token)) {
            $this->setSuccessMessage('Email confirmed successfully. You can now login.');
            $this->redirect(BASE_URL . 'login');
        } else {
            $this->setErrorMessage('Error confirming email. Please try again or contact support.');
            $this->redirect(BASE_URL);
        }
    }

    public function resendConfirmation()
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $this->render('auth/resend_confirmation', [
            'title' => 'Resend Confirmation - ' . SITE_NAME
        ]);
    }

    public function resendConfirmationPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'resend-confirmation');
            return;
        }

        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $this->setErrorMessage('Please enter your email address');
            $this->redirect(BASE_URL . 'resend-confirmation');
            return;
        }

        $user = $this->userModel->getByEmail($email);

        if (!$user) {
            $this->setErrorMessage('Email not found');
            $this->redirect(BASE_URL . 'resend-confirmation');
            return;
        }

        if ($user['is_confirmed'] == 1) {
            $this->setErrorMessage('This email is already confirmed');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        // Regenerate token and send email
        $token = $this->userModel->regenerateToken($email);

        if ($token && $this->emailService->sendConfirmationEmail($email, $user['name'], $token)) {
            $this->setSuccessMessage('Confirmation email sent. Please check your inbox.');
            $this->redirect(BASE_URL . 'login');
        } else {
            $this->setErrorMessage('Error sending confirmation email. Please try again later.');
            $this->redirect(BASE_URL . 'resend-confirmation');
        }
    }

    public function logout()
    {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        session_destroy();

        $this->redirect(BASE_URL);
    }

    public function forgotPassword()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        $this->render('auth/forgot_password', [
            'title' => 'Forgot Password - ' . SITE_NAME
        ]);
    }
    
    public function forgotPasswordPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'forgot-password');
            return;
        }

        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $this->setErrorMessage('Please enter your email address');
            $this->redirect(BASE_URL . 'forgot-password');
            return;
        }

        $user = $this->userModel->getByEmail($email);

        if (!$user) {
            // show success message even if email doesn't exist, safer?
            $this->setSuccessMessage('If your email exists in our system, you will receive a password reset link shortly.');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        // Generate reset token and save it
        $token = $this->userModel->createPasswordResetToken($email);

        if ($token && $this->emailService->sendPasswordResetEmail($email, $user['name'], $token)) {
            $this->setSuccessMessage('Password reset instructions have been sent to your email.');
        } else {
            $this->setErrorMessage('Error sending password reset email. Please try again later.');
        }
        
        $this->redirect(BASE_URL . 'login');
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->setErrorMessage('Invalid password reset link');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        // Verify token
        $user = $this->userModel->getUserByResetToken($token);

        if (!$user) {
            $this->setErrorMessage('Invalid or expired password reset link');
            $this->redirect(BASE_URL . 'login');
            return;
        }

        $this->render('auth/reset_password', [
            'title' => 'Reset Password - ' . SITE_NAME,
            'token' => $token
        ]);
    }

    public function resetPasswordPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'login');
            return;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $this->setErrorMessage('All fields are required');
            $this->redirect(BASE_URL . 'reset-password?token=' . urlencode($token));
            return;
        }

        if ($password !== $confirmPassword) {
            $this->setErrorMessage('Passwords do not match');
            $this->redirect(BASE_URL . 'reset-password?token=' . urlencode($token));
            return;
        }

        // Verify token and update password
        if ($this->userModel->resetPassword($token, $password)) {
            $this->setSuccessMessage('Your password has been updated successfully. You can now login with your new password.');
            $this->redirect(BASE_URL . 'login');
        } else {
            $this->setErrorMessage('Error resetting password. Please try again or contact support.');
            $this->redirect(BASE_URL . 'reset-password?token=' . urlencode($token));
        }
    }
    
    // Helper method to redirect users to their appropriate dashboard
    private function redirectToDashboard()
    {
        if ($_SESSION['user_role'] === 'admin') {
            $this->redirect(BASE_URL . 'admin/dashboard');
        } else {
            $this->redirect(BASE_URL . 'user/dashboard');
        }
    }
}