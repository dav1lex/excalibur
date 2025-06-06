<?php
// Include the header
include_once 'views/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebar" style="min-height: calc(100vh - 56px);">
            <div class="position-sticky pt-3">
                <div class="d-flex justify-content-between align-items-center px-3 mt-2 mb-3 d-md-none">
                    <h5 class="m-0">Admin Menu</h5>
                    <button class="btn btn-sm btn-outline-secondary sidebar-close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <div class="text-center mb-4 d-none d-md-block">
                    <div class="bg-dark text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                    <h6 class="mb-0 text-danger">Admin</h6>
                </div>
                
                <div class="px-3 mb-4">
                    <div class="sidebar-divider mb-3 d-flex align-items-center">
                        <span class="bg-secondary opacity-50" style="height: 1px; flex: 1;"></span>
                        <span class="mx-2 text-uppercase text-muted small fw-bold">Main</span>
                        <span class="bg-secondary opacity-50" style="height: 1px; flex: 1;"></span>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-1">
                            <a class="nav-link rounded <?= strpos($_SERVER['REQUEST_URI'], 'admin/dashboard') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/dashboard">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link rounded <?= strpos($_SERVER['REQUEST_URI'], 'admin/users') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/users">
                                <i class="bi bi-people me-2"></i> Users
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="px-3 mb-4">
                    <div class="sidebar-divider mb-3 d-flex align-items-center">
                        <span class="bg-secondary opacity-50" style="height: 1px; flex: 1;"></span>
                        <span class="mx-2 text-uppercase text-muted small fw-bold">Auctions</span>
                        <span class="bg-secondary opacity-50" style="height: 1px; flex: 1;"></span>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-1">
                            <a class="nav-link rounded <?= strpos($_SERVER['REQUEST_URI'], 'admin/auctions') !== false && !strpos($_SERVER['REQUEST_URI'], 'create') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/auctions">
                                <i class="bi bi-calendar-event me-2"></i> All Auctions
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link rounded <?= strpos($_SERVER['REQUEST_URI'], 'admin/lots') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/lots">
                                <i class="bi bi-collection me-2"></i> All Lots
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link rounded <?= strpos($_SERVER['REQUEST_URI'], 'admin/bids') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/bids">
                                <i class="bi bi-cash-stack me-2"></i> All Bids
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="px-3">
                    <div class="sidebar-divider mb-3 d-flex align-items-center">
                        <span class="bg-secondary opacity-50" style="height: 1px; flex: 1;"></span>
                        <span class="mx-2 text-uppercase text-muted small fw-bold">Account</span>
                        <span class="bg-secondary opacity-50" style="height: 1px; flex: 1;"></span>
                    </div>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item mb-1">
                            <a class="nav-link rounded text-danger" href="<?= BASE_URL ?>logout">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 content-push">
            <?php include_once "views/{$view}.php"; ?>
        </main>
    </div>
</div>

<style>
    .sidebar {
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }
    
    .sidebar .nav-link {
        color: #495057;
        font-weight: 500;
        padding: .75rem 1rem;
        transition: all 0.2s ease;
    }
    
    .sidebar .nav-link.active {
        color: #fff;
        background-color: #212529;
    }
    
    .sidebar .nav-link:hover {
        color: #212529;
        background-color: rgba(33, 37, 41, .1);
    }
    
    .sidebar-heading {
        font-size: .75rem;
        text-transform: uppercase;
    }
    
    @media (max-width: 767.98px) {
        body {
            overflow-x: hidden;
        }

        .content-push {
            transition: transform 0.3s ease;
        }

        body.sidebar-open .content-push {
            transform: translateX(280px);
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            width: 280px;
            padding: 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
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
        
        // Add push class to relevant elements
        const navbar = document.querySelector('.navbar');
        if (navbar) navbar.classList.add('content-push');

        function toggleSidebar() {
            const isMobile = window.innerWidth < 767.98;
            sidebar.classList.toggle('show');
            if (isMobile) {
                document.body.classList.toggle('sidebar-open');
            }
        }

        function closeSidebar() {
            const isMobile = window.innerWidth < 767.98;
            sidebar.classList.remove('show');
            if (isMobile) {
                document.body.classList.remove('sidebar-open');
            }
        }
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        
        if (sidebarClose && sidebar) {
            sidebarClose.addEventListener('click', closeSidebar);
        }
    });
</script>
</body>
</html> 