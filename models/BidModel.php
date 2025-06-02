<?php

class BidModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllBids() {
        $query = "SELECT * FROM bids ORDER BY placed_at DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getBidsByUserId($userId) {
        $query = "SELECT b.*, l.title as lot_title, l.lot_number, a.title as auction_title 
                  FROM bids b 
                  JOIN lots l ON b.lot_id = l.id 
                  JOIN auctions a ON l.auction_id = a.id 
                  WHERE b.user_id = :user_id 
                  ORDER BY b.placed_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getBidsByLotId($lotId) {
        $query = "SELECT b.*, u.name as user_name 
                  FROM bids b 
                  JOIN users u ON b.user_id = u.id 
                  WHERE b.lot_id = :lot_id 
                  ORDER BY b.placed_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lot_id', $lotId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getHighestBidForLot($lotId) {
        $query = "SELECT * FROM bids WHERE lot_id = :lot_id ORDER BY amount DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lot_id', $lotId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function placeBid($data) {
        $query = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                  VALUES (:user_id, :lot_id, :amount, :max_amount, :placed_at)";
        
        $stmt = $this->conn->prepare($query);
        
        $now = date('Y-m-d H:i:s');
        
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':lot_id', $data['lot_id']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':max_amount', $data['max_amount']);
        $stmt->bindParam(':placed_at', $now);
        
        if ($stmt->execute()) {
            // Update the current price of the lot
            $lotModel = new LotModel();
            $lotModel->updateCurrentPrice($data['lot_id'], $data['amount']);
            
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function getUserBidForLot($userId, $lotId) {
        $query = "SELECT * FROM bids WHERE user_id = :user_id AND lot_id = :lot_id ORDER BY placed_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':lot_id', $lotId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getWonBidsByUserId($userId) {
        $query = "SELECT b.*, l.title as lot_title, l.lot_number, a.title as auction_title 
                  FROM bids b 
                  JOIN lots l ON b.lot_id = l.id 
                  JOIN auctions a ON l.auction_id = a.id 
                  WHERE b.user_id = :user_id 
                  AND a.status = 'ended' 
                  AND b.amount = (SELECT MAX(amount) FROM bids WHERE lot_id = b.lot_id) 
                  ORDER BY b.placed_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countBids() {
        $query = "SELECT COUNT(*) as count FROM bids";
        $result = $this->conn->query($query)->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function countBidsByUserId($userId) {
        $query = "SELECT COUNT(*) as count FROM bids WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function countWonBidsByUserId($userId) {
        $query = "SELECT COUNT(*) as count 
                  FROM bids b 
                  JOIN lots l ON b.lot_id = l.id 
                  JOIN auctions a ON l.auction_id = a.id 
                  WHERE b.user_id = :user_id 
                  AND a.status = 'ended' 
                  AND b.amount = (SELECT MAX(amount) FROM bids WHERE lot_id = b.lot_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
} 