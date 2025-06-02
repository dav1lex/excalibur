<?php
// Start session
session_start();

// Load config file
require_once 'config/config.php';

// Load Database and Router classes
require_once 'config/Database.php';
require_once 'config/Router.php';

// Load Controllers
require_once 'controllers/BaseController.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AuctionController.php';
require_once 'controllers/LotController.php';
require_once 'controllers/BidController.php';

// Load Models
require_once 'models/Auction.php';

// Create Router instance
$router = new Router();

// Define routes
// Home routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);

// Auth routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginPost']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'registerPost']);
$router->get('/logout', [AuthController::class, 'logout']);

// Admin routes
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/edit-user', [AdminController::class, 'editUser']);
$router->post('/admin/update-user', [AdminController::class, 'updateUser']);
$router->get('/admin/delete-user', [AdminController::class, 'deleteUser']);
$router->get('/admin/lots', [AdminController::class, 'lots']);
$router->get('/admin/auctions', [AdminController::class, 'auctions']);
$router->get('/admin/bids', [AdminController::class, 'bids']);
$router->get('/admin/delete-bid', [AdminController::class, 'deleteBid']);

// User routes
$router->get('/user/dashboard', [UserController::class, 'dashboard']);
$router->get('/user/profile', [UserController::class, 'profile']);
$router->post('/user/update-profile', [UserController::class, 'updateProfile']);
$router->get('/user/bids', [BidController::class, 'myBids']);
$router->get('/user/watchlist', [BidController::class, 'watchlist']);

// Auction routes
$router->get('/auctions', [AuctionController::class, 'index']);
$router->get('/auctions/view', [AuctionController::class, 'view']);
$router->get('/admin/auctions', [AuctionController::class, 'manage']);
$router->get('/auctions/create', [AuctionController::class, 'create']);
$router->post('/auctions/store', [AuctionController::class, 'store']);
$router->get('/auctions/edit', [AuctionController::class, 'edit']);
$router->get('/auctions/edit/:id', [AuctionController::class, 'edit']);
$router->post('/auctions/update', [AuctionController::class, 'update']);
$router->get('/auctions/delete', [AuctionController::class, 'delete']);

// Lot routes
$router->get('/lots/view', [LotController::class, 'view']);
$router->get('/lots/create', [LotController::class, 'create']);
$router->post('/lots/store', [LotController::class, 'store']);
$router->get('/lots/edit', [LotController::class, 'edit']);
$router->get('/lots/edit/:id', [LotController::class, 'edit']);
$router->post('/lots/update', [LotController::class, 'update']);
$router->get('/lots/delete', [LotController::class, 'delete']);

// Bid routes
$router->post('/bids/place', [BidController::class, 'place']);
$router->post('/watchlist/add', [BidController::class, 'addToWatchlist']);
$router->get('/watchlist/remove', [BidController::class, 'removeFromWatchlist']);

// 404 Not Found
$router->notFound(function() {
    http_response_code(404);
    include_once 'views/layouts/header.php';
    echo '<div class="container mt-5">
            <div class="alert alert-danger">
                <h1>404 - Page Not Found</h1>
                <p>The page you are looking for does not exist.</p>
                <a href="' . BASE_URL . '" class="btn btn-primary">Go Home</a>
            </div>
          </div>';
    include_once 'views/layouts/footer.php';
});

// Update auction statuses before resolving routes
$auctionModel = new Auction();
$auctionModel->updateStatuses();

// Resolve the route
$router->resolve(); 