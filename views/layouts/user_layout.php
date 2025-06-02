<?php
// Include the header
include_once 'views/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar toggle button for mobile -->
        <div class="d-md-none">
            <button class="btn btn-primary position-fixed mt-2 ms-2" id="sidebarToggle" style="z-index: 1001;">
                <i class="bi bi-list"></i> Menu
            </button>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebar" style="min-height: calc(100vh - 56px);">
            <div class="position-sticky pt-3">
                <div class="d-flex justify-content-between align-items-center px-3 mt-2 mb-3 d-md-none">
                    <h5 class="m-0">User Menu</h5>
                    <button class="btn btn-sm btn-outline-secondary sidebar-close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>My Account</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'user/dashboard') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>user/dashboard">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'user/profile') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>user/profile">
                            <i class="bi bi-person me-2"></i> My Profile
                        </a>
                    </li>
                </ul>
                
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>My Activity</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'user/bids') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>user/bids">
                            <i class="bi bi-cash-stack me-2"></i> My Bids
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'user/watchlist') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>user/watchlist">
                            <i class="bi bi-bookmark-heart me-2"></i> My Watchlist
                        </a>
                    </li>
                </ul>
                
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Auctions</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>auctions">
                            <i class="bi bi-calendar-event me-2"></i> Browse Auctions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>logout">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <?php include_once "views/{$view}.php"; ?>
        </main>
    </div>
</div>

<?php
// Add custom styles for user area
?>
<style>
    .sidebar {
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }
    
    .sidebar .nav-link {
        color: #333;
        font-weight: 500;
        padding: .5rem 1rem;
    }
    
    .sidebar .nav-link.active {
        color: #007bff;
        background-color: rgba(0, 123, 255, .1);
    }
    
    .sidebar .nav-link:hover {
        color: #007bff;
    }
    
    .sidebar-heading {
        font-size: .75rem;
        text-transform: uppercase;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>public/js/script.js"></script>

<!-- Sidebar toggle script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.querySelector('.sidebar-close');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
        
        if (sidebarClose && sidebar) {
            sidebarClose.addEventListener('click', function() {
                sidebar.classList.remove('show');
            });
        }
    });
</script>
</body>
</html> 