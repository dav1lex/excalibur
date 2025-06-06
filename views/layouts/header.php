<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="<?= BASE_URL ?>public/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <?php if (isset($isDashboardPage) && $isDashboardPage): ?>
                <!-- Sidebar Toggle for mobile -->
                <button class="btn btn-light d-md-none me-2" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
            <?php endif; ?>
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
                <i class="bi bi-gem me-2"></i>
                <span>NanoBid</span>
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- User menu dropdown for mobile -->
                <div class="d-md-none dropdown">
                    <button class="btn btn-light" type="button" id="userMenuMobile" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuMobile">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?><?= $_SESSION['user_role'] === 'admin' ? 'admin' : 'user' ?>/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            <?php endif; ?>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>"><i class="bi bi-house-door me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>auctions"><i class="bi bi-calendar-event me-1"></i> Auctions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>how-to-bid"><i class="bi bi-question-circle me-1"></i> How to Bid</a>
                    </li>
                </ul>
                <ul class="navbar-nav <?php if (isset($_SESSION['user_id'])){ echo 'd-none d-md-flex'; } ?>">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-sm btn-outline-secondary mx-1" href="<?= BASE_URL ?>admin/dashboard">
                                    <i class="bi bi-speedometer2 me-1"></i> Admin Panel
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-sm btn-outline-light mx-1" href="<?= BASE_URL ?>user/dashboard">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>logout"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-sm btn-outline-secondary mx-1" href="<?= BASE_URL ?>login"><i class="bi bi-person me-1"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-sm btn-primary mx-1" href="<?= BASE_URL ?>register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html> 