<?php
require_once 'models/BaseModel.php';

class Bid extends BaseModel {
    
    /**
     * Get all bids
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT b.*, l.title as lot_title, l.current_price, l.starting_price, a.title as auction_title, 
                a.end_date as auction_end_date, u.name as user_name
                FROM bids b
                JOIN lots l ON b.lot_id = l.id
                JOIN auctions a ON l.auction_id = a.id
                JOIN users u ON b.user_id = u.id
                ORDER BY b.placed_at DESC";
        
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
     * Get bids by lot ID
     */
    public function getByLotId($lot_id, $limit = null) {
        $sql = "SELECT b.*, u.name as user_name 
                FROM bids b
                JOIN users u ON b.user_id = u.id
                WHERE b.lot_id = :lot_id
                ORDER BY b.amount DESC, b.placed_at ASC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get bids by user ID
     */
    public function getByUserId($user_id, $limit = null) {
        $sql = "SELECT b.*, l.title as lot_title, l.current_price, l.image_path,
                a.title as auction_title, a.status as auction_status, a.end_date as auction_end_date
                FROM bids b
                JOIN lots l ON b.lot_id = l.id
                JOIN auctions a ON l.auction_id = a.id
                WHERE b.user_id = :user_id
                ORDER BY b.placed_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user's winning bids (highest bid on ended auctions)
     */
    public function getUserWinningBids($user_id) {
        $sql = "SELECT b.*, l.title as lot_title, l.current_price, l.image_path,
                a.title as auction_title, a.end_date as auction_end_date
                FROM bids b
                JOIN lots l ON b.lot_id = l.id
                JOIN auctions a ON l.auction_id = a.id
                WHERE b.user_id = :user_id
                AND a.status = 'ended'
                AND b.amount = l.current_price
                ORDER BY a.end_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Place a new bid
     */
    public function placeBid($data) {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Insert bid record
            $sql = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                    VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':lot_id', $data['lot_id'], PDO::PARAM_INT);
            $stmt->bindParam(':amount', $data['amount'], PDO::PARAM_INT);
            
            // Handle max_amount (for proxy bidding)
            if (isset($data['max_amount']) && $data['max_amount'] > $data['amount']) {
                $stmt->bindParam(':max_amount', $data['max_amount'], PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':max_amount', null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            $bidId = $this->conn->lastInsertId();
            
            // Update lot's current price
            $sqlUpdate = "UPDATE lots SET current_price = :amount, updated_at = NOW() WHERE id = :lot_id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':amount', $data['amount'], PDO::PARAM_INT);
            $stmtUpdate->bindParam(':lot_id', $data['lot_id'], PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            // Commit transaction
            $this->conn->commit();
            
            return $bidId;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Process proxy bidding
     * This is called after a new bid is placed to handle automatic proxy bidding
     */
    public function processProxyBidding($lot_id) {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Get the current highest bid
            $sqlHighest = "SELECT * FROM bids WHERE lot_id = :lot_id ORDER BY amount DESC LIMIT 1";
            $stmtHighest = $this->conn->prepare($sqlHighest);
            $stmtHighest->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
            $stmtHighest->execute();
            $highestBid = $stmtHighest->fetch(PDO::FETCH_ASSOC);
            
            if (!$highestBid) {
                $this->conn->commit();
                return true; // No bids yet
            }
            
            // Get all proxy bids for this lot (excluding the highest bidder)
            $sqlProxy = "SELECT * FROM bids 
                        WHERE lot_id = :lot_id 
                        AND max_amount IS NOT NULL 
                        AND max_amount > :current_amount
                        AND user_id != :highest_user_id
                        ORDER BY max_amount DESC, placed_at ASC";
            
            $stmtProxy = $this->conn->prepare($sqlProxy);
            $stmtProxy->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
            $stmtProxy->bindParam(':current_amount', $highestBid['amount'], PDO::PARAM_INT);
            $stmtProxy->bindParam(':highest_user_id', $highestBid['user_id'], PDO::PARAM_INT);
            $stmtProxy->execute();
            
            $proxyBids = $stmtProxy->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($proxyBids)) {
                $this->conn->commit();
                return true; // No proxy bids to process
            }
            
            // Get the highest proxy bid
            $topProxyBid = $proxyBids[0];
            
            // Calculate the new bid amount
            // If the highest bidder has a max_amount, we need to check if it's higher than the proxy bid
            if ($highestBid['max_amount'] !== null) {
                // If highest bidder's max is higher than proxy bidder's max
                if ($highestBid['max_amount'] >= $topProxyBid['max_amount']) {
                    // Set current price to proxy bidder's max
                    $newAmount = $topProxyBid['max_amount'];
                    
                    // Insert automatic bid for highest bidder
                    $sqlInsert = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                                VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
                    $stmtInsert = $this->conn->prepare($sqlInsert);
                    $stmtInsert->bindParam(':user_id', $highestBid['user_id'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':amount', $newAmount, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':max_amount', $highestBid['max_amount'], PDO::PARAM_INT);
                    $stmtInsert->execute();
                } else {
                    // Proxy bidder's max is higher, so they outbid the highest bidder
                    // Set current price to highest bidder's max + 1 (or increment by standard amount)
                    $newAmount = $highestBid['max_amount'] + 1;
                    
                    // Insert automatic bid for proxy bidder
                    $sqlInsert = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                                VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
                    $stmtInsert = $this->conn->prepare($sqlInsert);
                    $stmtInsert->bindParam(':user_id', $topProxyBid['user_id'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':amount', $newAmount, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':max_amount', $topProxyBid['max_amount'], PDO::PARAM_INT);
                    $stmtInsert->execute();
                }
            } else {
                // Highest bidder doesn't have a max, so proxy bidder outbids them
                $newAmount = $highestBid['amount'] + 1;
                
                // If proxy max is higher than current bid + 1
                if ($topProxyBid['max_amount'] > $newAmount) {
                    // Insert automatic bid for proxy bidder
                    $sqlInsert = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                                VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
                    $stmtInsert = $this->conn->prepare($sqlInsert);
                    $stmtInsert->bindParam(':user_id', $topProxyBid['user_id'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':amount', $newAmount, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':max_amount', $topProxyBid['max_amount'], PDO::PARAM_INT);
                    $stmtInsert->execute();
                }
            }
            
            // Update lot's current price
            $sqlUpdate = "UPDATE lots SET current_price = :amount, updated_at = NOW() WHERE id = :lot_id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':amount', $newAmount, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            // Commit transaction
            $this->conn->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user is the highest bidder on a lot
     */
    public function isHighestBidder($lot_id, $user_id) {
        $sql = "SELECT COUNT(*) FROM bids 
                WHERE lot_id = :lot_id 
                AND user_id = :user_id 
                AND amount = (SELECT MAX(amount) FROM bids WHERE lot_id = :lot_id)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Count total bids
     */
    public function countTotal() {
        $sql = "SELECT COUNT(*) FROM bids";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Count bids by user
     */
    public function countByUser($user_id) {
        $sql = "SELECT COUNT(*) FROM bids WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Count bids by lot
     */
    public function countByLot($lot_id) {
        $sql = "SELECT COUNT(*) FROM bids WHERE lot_id = :lot_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get bid by ID
     */
    public function getById($id) {
        $sql = "SELECT b.*, l.title as lot_title 
                FROM bids b
                LEFT JOIN lots l ON b.lot_id = l.id
                WHERE b.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get highest bid for a lot (excluding a specific bid)
     */
    public function getHighestBidForLot($lot_id, $exclude_bid_id = null) {
        $sql = "SELECT * FROM bids 
                WHERE lot_id = :lot_id";
        
        if ($exclude_bid_id !== null) {
            $sql .= " AND id != :exclude_bid_id";
        }
        
        $sql .= " ORDER BY amount DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
        
        if ($exclude_bid_id !== null) {
            $stmt->bindParam(':exclude_bid_id', $exclude_bid_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Delete a bid
     */
    public function delete($id) {
        $sql = "DELETE FROM bids WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Update a bid
     */
    public function update($id, $data) {
        $sql = "UPDATE bids SET ";
        $params = [];
        
        foreach ($data as $key => $value) {
            $params[] = "$key = :$key";
        }
        
        $sql .= implode(', ', $params);
        $sql .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }
} 