<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'auction_platform');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('SITE_NAME', 'Auction Platform');
define('BASE_URL', 'http://localhost/');  // Update this based on your setup

// File Upload Configuration
define('MAX_FILE_SIZE', 6 * 1024 * 1024); // 6MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/images/');

// Session Configuration
define('SESSION_LIFETIME', 60 * 60 * 24); // 24 hours 