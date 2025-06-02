<?php
require_once 'models/BaseModel.php';

class Watchlist extends BaseModel {
    
    /**
     * Add lot to user's watchlist
     */
    public function add($user_id, $lot_id) {
        // Check if already in watchlist
        if ($this->isInWatchlist($user_id, $lot_id)) {
            return true; // Already in watchlist, consider it a success
        }
        
        $sql = "INSERT INTO watchlist (user_id, lot_id, added_at) VALUES (:user_id, :lot_id, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Remove lot from user's watchlist
     */
    public function remove($user_id, $lot_id) {
        $sql = "DELETE FROM watchlist WHERE user_id = :user_id AND lot_id = :lot_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Check if lot is in user's watchlist
     */
    public function isInWatchlist($user_id, $lot_id) {
        $sql = "SELECT COUNT(*) FROM watchlist WHERE user_id = :user_id AND lot_id = :lot_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get user's watchlist
     */
    public function getByUserId($user_id) {
        $sql = "SELECT w.*, l.title, l.lot_number, l.starting_price, l.current_price, l.image_path,
                a.id as auction_id, a.title as auction_title, a.status as auction_status, a.end_date
                FROM watchlist w
                JOIN lots l ON w.lot_id = l.id
                JOIN auctions a ON l.auction_id = a.id
                WHERE w.user_id = :user_id
                ORDER BY w.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count items in user's watchlist
     */
    public function countByUser($user_id) {
        $sql = "SELECT COUNT(*) FROM watchlist WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
} 