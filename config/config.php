<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Database 
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

// Application cfg
define('SITE_NAME', $_ENV['SITE_NAME']);
define('BASE_URL', $_ENV['BASE_URL']);  // Update lateer

//  Upload 
define('MAX_FILE_SIZE', 6 * 1024 * 1024); // 6MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/images/');

// Session 
define('SESSION_LIFETIME', 60 * 60 * 1); // 1 hour 