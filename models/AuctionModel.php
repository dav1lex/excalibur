<?php

class AuctionModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllAuctions() {
        $query = "SELECT * FROM auctions ORDER BY id DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPublicAuctions() {
        $query = "SELECT * FROM auctions WHERE status IN ('upcoming', 'live', 'ended') ORDER BY id DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAuctionsByStatus($status) {
        $query = "SELECT * FROM auctions WHERE status = :status ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAuctionById($id) {
        $query = "SELECT * FROM auctions WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createAuction($data) {
        $query = "INSERT INTO auctions (title, description, start_date, end_date, status) 
                  VALUES (:title, :description, :start_date, :end_date, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function updateAuction($id, $data) {
        $query = "UPDATE auctions SET 
                  title = :title, 
                  description = :description, 
                  start_date = :start_date, 
                  end_date = :end_date, 
                  status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }
    
    public function deleteAuction($id) {
        $query = "DELETE FROM auctions WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function countAuctions() {
        $query = "SELECT COUNT(*) as count FROM auctions";
        $result = $this->conn->query($query)->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function updateAuctionStatuses() {
        $now = date('Y-m-d H:i:s');
        
        // Update upcoming auctions to live if start date has passed
        $query1 = "UPDATE auctions SET status = 'live' 
                   WHERE status = 'upcoming' AND start_date <= :now";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(':now', $now);
        $stmt1->execute();
        
        // Update live auctions to ended if end date has passed
        $query2 = "UPDATE auctions SET status = 'ended' 
                   WHERE status = 'live' AND end_date <= :now";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':now', $now);
        $stmt2->execute();
        
        return true;
    }
    
    public function getLiveAuctions() {
        $query = "SELECT * FROM auctions WHERE status = 'live' ORDER BY end_date ASC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUpcomingAuctions() {
        $query = "SELECT * FROM auctions WHERE status = 'upcoming' ORDER BY start_date ASC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
} 