<?php

// Define routes
$routes = [
    // Home
    '' => ['HomeController', 'index'],
    'home' => ['HomeController', 'index'],
    
    // Authentication
    'login' => ['AuthController', 'login'],
    'register' => ['AuthController', 'register'],
    'logout' => ['AuthController', 'logout'],
    
    // Admin routes
    'admin/dashboard' => ['AdminController', 'dashboard'],
    'admin/users' => ['AdminController', 'users'],
    'admin/edit-user' => ['AdminController', 'editUser'],
    'admin/delete-user' => ['AdminController', 'deleteUser'],
    'admin/update-user' => ['AdminController', 'updateUser'],
    'admin/auctions' => ['AdminController', 'auctions'],
    'admin/lots' => ['AdminController', 'lots'],
    
    // User routes
    'user/dashboard' => ['UserController', 'dashboard'],
    'user/profile' => ['UserController', 'profile'],
    'user/update-profile' => ['UserController', 'updateProfile'],
    'user/bids' => ['UserController', 'bids'],
    'user/watchlist' => ['UserController', 'watchlist'],
    
    // Auction routes
    'auctions' => ['AuctionsController', 'index'],
    'auctions/view' => ['AuctionsController', 'view'],
    'auctions/create' => ['AuctionsController', 'create'],
    'auctions/store' => ['AuctionsController', 'store'],
    'auctions/edit/{id}' => ['AuctionsController', 'edit'],
    'auctions/update/{id}' => ['AuctionsController', 'update'],
    'auctions/delete' => ['AuctionsController', 'delete'],
    
    // Lot routes
    'lots/view' => ['LotsController', 'view'],
    'lots/create' => ['LotsController', 'create'],
    'lots/store' => ['LotsController', 'store'],
    'lots/edit' => ['LotsController', 'edit'],
    'lots/update/{id}' => ['LotsController', 'update'],
    'lots/delete' => ['LotsController', 'delete'],
    
    // Bid routes
    'bids/place' => ['BidsController', 'place'],
    'admin/bids' => ['AdminController', 'bids']
];

// Process the current request
$requestUrl = isset($_GET['url']) ? $_GET['url'] : '';
$requestUrl = rtrim($requestUrl, '/');

// Check for dynamic routes with parameters
$matchFound = false;
foreach ($routes as $route => $handler) {
    // Check if route contains a parameter placeholder like {id}
    if (strpos($route, '{') !== false) {
        $routePattern = preg_replace('/{[^}]+}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        
        if (preg_match($routePattern, $requestUrl, $matches)) {
            array_shift($matches); // Remove the full match
            $controller = $handler[0];
            $method = $handler[1];
            
            // Instantiate controller
            $controllerInstance = new $controller();
            
            // Call method with parameters
            call_user_func_array([$controllerInstance, $method], $matches);
            
            $matchFound = true;
            break;
        }
    }
}

// If no dynamic route matched, try static routes
if (!$matchFound) {
    if (isset($routes[$requestUrl])) {
        $controller = $routes[$requestUrl][0];
        $method = $routes[$requestUrl][1];
        
        // Instantiate controller
        $controllerInstance = new $controller();
        
        // Call method
        $controllerInstance->$method();
    } else {
        // Handle 404
        header("HTTP/1.0 404 Not Found");
        include 'views/errors/404.php';
    }
} 