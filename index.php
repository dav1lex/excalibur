<?php
session_start();
// load all

//  config 
require_once 'config/config.php';

//  Database and Router 
require_once 'config/Database.php';
require_once 'config/Router.php';

//  Controllers
require_once 'controllers/BaseController.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AuctionController.php';
require_once 'controllers/LotController.php';
require_once 'controllers/BidController.php';

//  Models
require_once 'models/Auction.php';
require_once 'models/User.php';
require_once 'models/Lot.php';
require_once 'models/Bid.php';
require_once 'models/Watchlist.php'; 

// base path
$basePath = '';
if (isset($_ENV['BASE_PATH'])) {
    $basePath = $_ENV['BASE_PATH'];
} else {
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = ($scriptName === '/' || $scriptName === '\\') ? '' : $scriptName;
}

//  Router 
$router = new Router($basePath);

// Home 
$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);
$router->get('/how-to-bid', [HomeController::class, 'howToBid']);

// Auth 
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginPost']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'registerPost']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->post('/forgot-password-post', [AuthController::class, 'forgotPasswordPost']);
$router->get('/reset-password', [AuthController::class, 'resetPassword']);
$router->post('/reset-password-post', [AuthController::class, 'resetPasswordPost']);
$router->get('/confirm-email', [AuthController::class, 'confirmEmail']);
$router->get('/resend-confirmation', [AuthController::class, 'resendConfirmation']);
$router->post('/resend-confirmation-post', [AuthController::class, 'resendConfirmationPost']);

// Admin 
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/edit-user', [AdminController::class, 'editUser']);
$router->post('/admin/update-user', [AdminController::class, 'updateUser']);
$router->get('/admin/delete-user', [AdminController::class, 'deleteUser']);
$router->get('/admin/lots', [AdminController::class, 'lots']);
$router->get('/admin/view-lot', [AdminController::class, 'viewLot']);
$router->get('/admin/auctions', [AdminController::class, 'auctions']);
$router->get('/admin/bids', [AdminController::class, 'bids']);
$router->get('/admin/delete-bid', [AdminController::class, 'deleteBid']);

// User 
$router->get('/user/dashboard', [UserController::class, 'dashboard']);
$router->get('/user/profile', [UserController::class, 'profile']);
$router->post('/user/update-profile', [UserController::class, 'updateProfile']);
$router->get('/user/bids', [UserController::class, 'bids']);
$router->get('/user/watchlist', [BidController::class, 'watchlist']);

// Auction 
$router->get('/auctions', [AuctionController::class, 'index']);
$router->get('/auctions/create', [AuctionController::class, 'create']);
$router->post('/auctions/store', [AuctionController::class, 'store']);
$router->get('/auctions/edit/:id', [AuctionController::class, 'edit']);
$router->post('/auctions/update/:id', [AuctionController::class, 'update']);
$router->get('/auctions/delete/:id', [AuctionController::class, 'delete']);
$router->get('/auctions/end/:id', [AuctionController::class, 'endAuction']);
$router->get('/auctions/:auction_id/lots/:lot_id', [AuctionController::class, 'view']);
$router->get('/auctions/:id', [AuctionController::class, 'view']);

// Lot 
$router->get('/lots/view', [LotController::class, 'view']);
$router->get('/lots/create', [LotController::class, 'create']);
$router->post('/lots/store', [LotController::class, 'store']);
$router->get('/lots/edit', [LotController::class, 'edit']);
$router->get('/lots/edit/:id', [LotController::class, 'edit']);
$router->post('/lots/update/:id', [LotController::class, 'update']);
$router->get('/lots/delete', [LotController::class, 'delete']);

// Bid & wlist 
$router->post('/bids/place', [BidController::class, 'place']);
$router->post('/watchlist/add/:id', [BidController::class, 'addToWatchlist']);
$router->post('/watchlist/remove/:id', [BidController::class, 'removeFromWatchlist']);

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

// update auction statuses before resolving routes
$auctionModel = new Auction();
$auctionModel->updateStatuses();

// resolve, so if user goes to /auctions/1, it will go to the auction controller and the index method
$router->resolve(); 