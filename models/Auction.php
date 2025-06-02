<?php
require_once 'models/BaseModel.php';

class Auction extends BaseModel {
    
    /**
     * Get all auctions
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM auctions ORDER BY start_date DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all public auctions (excluding drafts)
     */
    public function getAllPublic($limit = null, $offset = 0) {
        $sql = "SELECT * FROM auctions WHERE status != 'draft' ORDER BY start_date DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get upcoming auctions (status = upcoming or start_date in future)
     */
    public function getUpcomingAuctions() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM auctions WHERE (status = 'upcoming' OR (start_date > :now AND status != 'draft')) ORDER BY start_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get live auctions (current date between start and end date)
     */
    public function getLiveAuctions() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM auctions WHERE status = 'live' OR (start_date <= :now AND end_date >= :now AND status != 'draft') ORDER BY end_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get ended auctions
     */
    public function getEndedAuctions() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM auctions WHERE status = 'ended' OR (end_date < :now AND status != 'draft') ORDER BY end_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get auction by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM auctions WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create a new auction
     */
    public function create($data) {
        $sql = "INSERT INTO auctions (title, description, start_date, end_date, status, created_at, updated_at) 
                VALUES (:title, :description, :start_date, :end_date, :status, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($sql);
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
    
    /**
     * Update an auction
     */
    public function update($id, $data) {
        $sql = "UPDATE auctions SET 
                title = :title,
                description = :description,
                start_date = :start_date,
                end_date = :end_date,
                status = :status,
                updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete an auction
     */
    public function delete($id) {
        $sql = "DELETE FROM auctions WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Count total auctions
     */
    public function countTotal() {
        $sql = "SELECT COUNT(*) FROM auctions";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Count live auctions
     */
    public function countLive() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT COUNT(*) FROM auctions WHERE status = 'live' OR (start_date <= :now AND end_date >= :now AND status != 'draft')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Update auction statuses based on current time
     * This should be called regularly via cron or at application start
     */
    public function updateStatuses() {
        $now = date('Y-m-d H:i:s');
        
        // Update draft auctions that should be upcoming
        $sql = "UPDATE auctions SET status = 'upcoming', updated_at = NOW() 
                WHERE status = 'draft' AND start_date > ? 
                AND TIMESTAMPDIFF(MINUTE, updated_at, NOW()) > 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $now);
        $stmt->execute();
        
        // Update to live
        $sql = "UPDATE auctions SET status = 'live', updated_at = NOW() 
                WHERE (status = 'upcoming' OR status = 'draft') 
                AND start_date <= ? AND end_date >= ?
                AND TIMESTAMPDIFF(MINUTE, updated_at, NOW()) > 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $now);
        $stmt->bindParam(2, $now);
        $stmt->execute();
        
        // Update to ended
        $sql = "UPDATE auctions SET status = 'ended', updated_at = NOW() 
                WHERE status != 'ended' AND end_date < ?
                AND TIMESTAMPDIFF(MINUTE, updated_at, NOW()) > 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $now);
        $stmt->execute();
        
        return true;
    }
    
    /**
     * Get auctions by status
     */
    public function getByStatus($status) {
        $sql = "SELECT * FROM auctions WHERE status = :status ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 