<?php
class BaseController {
    // Render a view with data
    protected function render($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Check if this is an admin or user view
        if (strpos($view, 'admin/') === 0) {
            // For admin views, use admin layout
            include_once "views/layouts/admin_layout.php";
        } else if (strpos($view, 'user/') === 0) {
            // For user views, use user layout
            include_once "views/layouts/user_layout.php";
        } else {
            // For regular views, use standard layout
            include_once "views/layouts/header.php";
            include_once "views/{$view}.php";
            include_once "views/layouts/footer.php";
        }
        
        // Get content and end buffering
        $content = ob_get_clean();
        
        echo $content;
    }
    
    // Redirect to a URL
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
    
    // Display error message
    protected function setErrorMessage($message) {
        $_SESSION['error_message'] = $message;
    }
    
    // Display success message
    protected function setSuccessMessage($message) {
        $_SESSION['success_message'] = $message;
    }
} 