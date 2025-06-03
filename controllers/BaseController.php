<?php
class BaseController {
    protected function render($view, $data = []) {
        extract($data);
        ob_start();
        
        if (strpos($view, 'admin/') === 0) {
            include_once "views/layouts/admin_layout.php";
        } else if (strpos($view, 'user/') === 0) {
            include_once "views/layouts/user_layout.php";
        } else {
            include_once "views/layouts/header.php";
            include_once "views/{$view}.php";
            include_once "views/layouts/footer.php";
        }
        
        $content = ob_get_clean();
        
        echo $content;
    }
    
    // Redirect to URL
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    // Get POST data
    protected function getPostData() {
        return $_POST;
    }
    
    // Get query parameters
    protected function getQueryParams() {
        return $_GET;
    }
    
    // Check if user is logged in
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Check if user is admin
    protected function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    // Get current logged in user
    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }
    
    //  error message
    protected function setErrorMessage($message) {
        $_SESSION['error_message'] = $message;
    }
    
    //  success message
    protected function setSuccessMessage($message) {
        $_SESSION['success_message'] = $message;
    }

    protected function ensureAdmin() {
        if (!$this->isAdmin()) {
            $this->setErrorMessage('Access denied. Admin privileges required.');
            $this->redirect(BASE_URL);
            exit; // remember, good practice to use exit
        }
    }
} 