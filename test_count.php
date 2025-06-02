<?php
// Simple test script to debug lot counting

// Load config and models
require_once 'config/config.php';
require_once 'models/Lot.php';

// Create a lot model instance
$lotModel = new Lot();

// Get all auction IDs
require_once 'models/Auction.php';
$auctionModel = new Auction();
$auctions = $auctionModel->getAll();

echo "<h1>Auction Lot Counts</h1>";
echo "<table border='1'>";
echo "<tr><th>Auction ID</th><th>Auction Title</th><th>Lot Count</th></tr>";

foreach ($auctions as $auction) {
    $auction_id = $auction['id'];
    $title = $auction['title'];
    $count = $lotModel->countByAuction($auction_id);
    
    echo "<tr>";
    echo "<td>$auction_id</td>";
    echo "<td>$title</td>";
    echo "<td>$count</td>";
    echo "</tr>";
}

echo "</table>";

// Now check all auctions with direct SQL
echo "<h2>Direct SQL Count</h2>";
echo "<pre>";

// Get database connection
require_once 'config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$sql = "SELECT auction_id, COUNT(*) as count FROM lots GROUP BY auction_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($counts);

echo "</pre>";
?> 