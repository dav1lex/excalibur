<?php
// Debug script to check lot counts and auction status

// Start session
session_start();

// Load config and required files
require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'models/Lot.php';
require_once 'models/Auction.php';

// Create database connection
$database = new Database();
$conn = $database->getConnection();

// Get models
$lotModel = new Lot();
$auctionModel = new Auction();

// Function to print debug info
function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

echo "<h1>Debug Information</h1>";

// Check database connection
echo "<h2>Database Connection</h2>";
echo "Connection Status: " . ($conn ? "Connected" : "Failed");

// Check auctions table
echo "<h2>Auctions Table</h2>";
$sql = "SELECT * FROM auctions";
$stmt = $conn->prepare($sql);
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
debug($auctions);

// Check lots table
echo "<h2>Lots Table</h2>";
$sql = "SELECT * FROM lots";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
debug($lots);

// Check lot counts by auction
echo "<h2>Lot Counts by Auction</h2>";
echo "<table border='1'>";
echo "<tr><th>Auction ID</th><th>Auction Title</th><th>Lot Count</th><th>Direct SQL Count</th></tr>";

foreach ($auctions as $auction) {
    $auction_id = $auction['id'];
    $title = $auction['title'];
    
    // Get count via model
    $model_count = $lotModel->countByAuction($auction_id);
    
    // Get count via direct SQL
    $sql = "SELECT COUNT(*) FROM lots WHERE auction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $auction_id, PDO::PARAM_INT);
    $stmt->execute();
    $direct_count = $stmt->fetchColumn();
    
    echo "<tr>";
    echo "<td>$auction_id</td>";
    echo "<td>$title</td>";
    echo "<td>$model_count</td>";
    echo "<td>$direct_count</td>";
    echo "</tr>";
}

echo "</table>";

// Check auction status updates
echo "<h2>Test Auction Status Update</h2>";

// Current statuses
echo "<h3>Current Statuses</h3>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Start Date</th><th>End Date</th></tr>";

foreach ($auctions as $auction) {
    echo "<tr>";
    echo "<td>{$auction['id']}</td>";
    echo "<td>{$auction['title']}</td>";
    echo "<td>{$auction['status']}</td>";
    echo "<td>{$auction['start_date']}</td>";
    echo "<td>{$auction['end_date']}</td>";
    echo "</tr>";
}

echo "</table>";

// Run update function
$auctionModel->updateStatuses();

// Updated statuses
echo "<h3>Updated Statuses</h3>";
$sql = "SELECT * FROM auctions";
$stmt = $conn->prepare($sql);
$stmt->execute();
$updated_auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Start Date</th><th>End Date</th></tr>";

foreach ($updated_auctions as $auction) {
    echo "<tr>";
    echo "<td>{$auction['id']}</td>";
    echo "<td>{$auction['title']}</td>";
    echo "<td>{$auction['status']}</td>";
    echo "<td>{$auction['start_date']}</td>";
    echo "<td>{$auction['end_date']}</td>";
    echo "</tr>";
}

echo "</table>";

// Check edit auction form
echo "<h2>Edit Auction Form</h2>";
echo "URL Format: <code>" . BASE_URL . "auctions/edit/[auction_id]</code>";
?> 