<?php

class LotModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllLots() {
        $query = "SELECT * FROM lots ORDER BY id DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllLotsWithAuctionInfo() {
        $query = "SELECT l.*, a.title as auction_title 
                  FROM lots l 
                  LEFT JOIN auctions a ON l.auction_id = a.id 
                  ORDER BY l.id DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLotsByAuctionId($auctionId) {
        $query = "SELECT * FROM lots WHERE auction_id = :auction_id ORDER BY lot_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':auction_id', $auctionId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLotById($id) {
        $query = "SELECT * FROM lots WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getLotWithAuctionInfo($id) {
        $query = "SELECT l.*, a.title as auction_title, a.status as auction_status, 
                         a.start_date as auction_start_date, a.end_date as auction_end_date 
                  FROM lots l 
                  LEFT JOIN auctions a ON l.auction_id = a.id 
                  WHERE l.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createLot($data) {
        $query = "INSERT INTO lots (auction_id, title, lot_number, description, starting_price, 
                                   current_price, reserve_price, image_path) 
                  VALUES (:auction_id, :title, :lot_number, :description, :starting_price, 
                          :current_price, :reserve_price, :image_path)";
        
        $stmt = $this->conn->prepare($query);
        
        // Set current price to starting price if not set
        if (!isset($data['current_price'])) {
            $data['current_price'] = $data['starting_price'];
        }
        
        $stmt->bindParam(':auction_id', $data['auction_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':lot_number', $data['lot_number']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':starting_price', $data['starting_price']);
        $stmt->bindParam(':current_price', $data['current_price']);
        $stmt->bindParam(':reserve_price', $data['reserve_price']);
        $stmt->bindParam(':image_path', $data['image_path']);
        
        return $stmt->execute();
    }
    
    public function updateLot($id, $data) {
        $query = "UPDATE lots SET 
                  auction_id = :auction_id,
                  title = :title,
                  lot_number = :lot_number,
                  description = :description,
                  starting_price = :starting_price,
                  current_price = :current_price,
                  reserve_price = :reserve_price";
        
        // Only update image if provided
        if (isset($data['image_path']) && !empty($data['image_path'])) {
            $query .= ", image_path = :image_path";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':auction_id', $data['auction_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':lot_number', $data['lot_number']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':starting_price', $data['starting_price']);
        $stmt->bindParam(':current_price', $data['current_price']);
        $stmt->bindParam(':reserve_price', $data['reserve_price']);
        
        if (isset($data['image_path']) && !empty($data['image_path'])) {
            $stmt->bindParam(':image_path', $data['image_path']);
        }
        
        return $stmt->execute();
    }
    
    public function deleteLot($id) {
        $query = "DELETE FROM lots WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function countLots() {
        $query = "SELECT COUNT(*) as count FROM lots";
        $result = $this->conn->query($query)->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function countLotsByAuctionId($auctionId) {
        $query = "SELECT COUNT(*) as count FROM lots WHERE auction_id = :auction_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':auction_id', $auctionId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function getRelatedLots($lotId, $auctionId, $limit = 4) {
        $query = "SELECT * FROM lots 
                  WHERE auction_id = :auction_id AND id != :lot_id 
                  ORDER BY lot_number ASC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':auction_id', $auctionId);
        $stmt->bindParam(':lot_id', $lotId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateCurrentPrice($id, $newPrice) {
        $query = "UPDATE lots SET current_price = :current_price WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':current_price', $newPrice);
        return $stmt->execute();
    }
} 