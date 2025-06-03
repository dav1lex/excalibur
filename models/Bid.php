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
     * Records the user's bid and updates the lot's current price.
     */
    public function placeBid($data) {
        try {
            // Start a database transaction to ensure all operations succeed or fail together.
            $this->conn->beginTransaction();
            
            // SQL to insert the new bid into the 'bids' table.
            $sql = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                    VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':lot_id', $data['lot_id'], PDO::PARAM_INT);
            $stmt->bindParam(':amount', $data['amount'], PDO::PARAM_INT);
            
            // If a max_amount is provided (for proxy bidding) and it's valid, bind it.
            // Otherwise, store NULL for max_amount.
            if (isset($data['max_amount']) && $data['max_amount'] > $data['amount']) {
                $stmt->bindParam(':max_amount', $data['max_amount'], PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':max_amount', null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            $bidId = $this->conn->lastInsertId(); // Get the ID of the newly inserted bid.
            
            // SQL to update the lot's current_price with the amount of this new bid.
            $sqlUpdate = "UPDATE lots SET current_price = :amount, updated_at = NOW() WHERE id = :lot_id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':amount', $data['amount'], PDO::PARAM_INT);
            $stmtUpdate->bindParam(':lot_id', $data['lot_id'], PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            // If all database operations were successful, commit the transaction.
            $this->conn->commit();
            
            return $bidId; // Return the new bid's ID on success.
        } catch (Exception $e) {
            // If any error occurred, roll back the transaction to undo changes.
            $this->conn->rollBack();
            // Log the detailed error for server administrators.
            error_log("Error in placeBid: " . $e->getMessage()); 
            return false; // Indicate failure.
        }
    }
    
    /**
     * Process proxy bidding.
     * This method is called after a new bid is placed to handle automatic proxy bidding.
     * It checks if the new bid triggers any existing proxy bids and places them automatically.
     * 
     * @param int $lot_id_param The ID of the lot for which to process proxy bids.
     * @param int $recursionDepth Current recursion depth to prevent infinite loops.
     * @return bool True if processing was successful or not needed, false on error.
     */
    public function processProxyBidding($lot_id_param, $recursionDepth = 0) {
        // Ensure lot_id is an integer for database operations.
        $lot_id = intval($lot_id_param);

        // Safety check: Prevent infinite recursion if proxy bids trigger too many counter-bids.
        if ($recursionDepth > 5) {
            // Log this specific situation as it indicates a potential issue or complex scenario.
            error_log("Proxy bidding recursion depth limit reached for lot_id: $lot_id");
            return true; // Stop processing to prevent server overload.
        }
        
        try {
            // Start a database transaction for atomicity of operations.
            $this->conn->beginTransaction();
            
            // Step 1: Get the current highest bid for the lot.
            $sqlHighest = "SELECT * FROM bids WHERE lot_id = :lot_id ORDER BY amount DESC LIMIT 1";
            $stmtHighest = $this->conn->prepare($sqlHighest);
            $stmtHighest->bindParam(':lot_id', $lot_id, PDO::PARAM_INT);
            $stmtHighest->execute();
            $highestBid = $stmtHighest->fetch(PDO::FETCH_ASSOC);
            
            // If there are no bids on the lot yet, there's no proxy bidding to process.
            if (!$highestBid) {
                $this->conn->commit(); // Commit (though no changes were made in this path).
                return true;
            }
            
            // Step 2: Find all active proxy bids from other users that could potentially outbid the current highest bid.
            // - Must have a max_amount set.
            // - Their max_amount must be greater than the current highest bid's actual amount.
            // - Must not be from the user who currently holds the highest bid.
            // - Order by the highest max_amount first, then by the earliest placed bid.
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
            
            // If no competing proxy bids are found, no further action is needed in this cycle.
            if (empty($proxyBids)) {
                $this->conn->commit();
                return true;
            }
            
            // The strongest competing proxy bid (highest max_amount, earliest placed).
            $topProxyBid = $proxyBids[0];
            
            // Flag to track if an automatic bid was placed in this cycle.
            $newBidPlaced = false;
            
            // Step 3: Determine if and how an automatic bid should be placed.
            // Scenario A: The current highest bidder ALSO has a max_amount set (they might be a proxy bidder themselves).
            if ($highestBid['max_amount'] !== null) {
                // Scenario A1: The current highest bidder's max_amount is strong enough to beat or match the competing proxy bid.
                if ($highestBid['max_amount'] >= $topProxyBid['max_amount']) {
                    // Calculate the bid for the current highest bidder: just enough to outbid the competitor's max, up to their own max.
                    $newAmount = min($topProxyBid['max_amount'] + $this->getBidIncrement($topProxyBid['max_amount']), $highestBid['max_amount']);
                    
                    // Only place this new bid if it's actually higher than what they are currently bidding.
                    if ($newAmount > $highestBid['amount']) {
                        // Insert an automatic bid for the current highest bidder.
                        $sqlInsert = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                                    VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
                        $stmtInsert = $this->conn->prepare($sqlInsert);
                        $userId = intval($highestBid['user_id']);
                        $lotId = intval($lot_id); // Already int, but good practice.
                        $bidAmount = intval($newAmount);
                        $maxAmount = intval($highestBid['max_amount']);
                        $stmtInsert->bindParam(':user_id', $userId, PDO::PARAM_INT);
                        $stmtInsert->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
                        $stmtInsert->bindParam(':amount', $bidAmount, PDO::PARAM_INT);
                        $stmtInsert->bindParam(':max_amount', $maxAmount, PDO::PARAM_INT);
                        try {
                            $stmtInsert->execute();
                            $newBidPlaced = true;
                        } catch (PDOException $e) {
                            error_log("Error placing auto bid for highest bidder (Scenario A1): " . $e->getMessage() . " SQL: " . $sqlInsert);
                            throw $e; // Re-throw to be caught by the outer catch block.
                        }
                    }
                } else {
                    // Scenario A2: The competing proxy bid ($topProxyBid) has a higher max_amount.
                    // Calculate the bid for the competing proxy bidder: just enough to outbid the current highest bidder's max_amount.
                    $newAmount = $this->getNextMinimumBid($highestBid['max_amount']);
                    
                    // Only place this new bid if it's actually higher than the current highest bid amount.
                    // (This check might seem redundant if getNextMinimumBid always increases, but it's safe).
                    if ($newAmount > $highestBid['amount']) {
                        // Insert an automatic bid for the competing proxy bidder ($topProxyBid).
                        $sqlInsert = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                                    VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
                        $stmtInsert = $this->conn->prepare($sqlInsert);
                        $userId = intval($topProxyBid['user_id']);
                        $lotId = intval($lot_id);
                        $bidAmount = intval($newAmount);
                        $maxAmount = intval($topProxyBid['max_amount']);
                        $stmtInsert->bindParam(':user_id', $userId, PDO::PARAM_INT);
                        $stmtInsert->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
                        $stmtInsert->bindParam(':amount', $bidAmount, PDO::PARAM_INT);
                        $stmtInsert->bindParam(':max_amount', $maxAmount, PDO::PARAM_INT);
                        try {
                            $stmtInsert->execute();
                            $newBidPlaced = true;
                        } catch (PDOException $e) {
                            error_log("Error placing auto bid for competing proxy (Scenario A2): " . $e->getMessage() . " SQL: " . $sqlInsert);
                            throw $e;
                        }
                    }
                }
            } else {
                // Scenario B: The current highest bidder placed a simple bid (no max_amount set).
                // The competing proxy bidder ($topProxyBid) will attempt to outbid them.
                $newAmount = $this->getNextMinimumBid($highestBid['amount']);
                
                // Check if the competing proxy bidder's max_amount is sufficient.
                if ($topProxyBid['max_amount'] >= $newAmount) {
                    // Insert an automatic bid for the competing proxy bidder ($topProxyBid).
                    $sqlInsert = "INSERT INTO bids (user_id, lot_id, amount, max_amount, placed_at) 
                                VALUES (:user_id, :lot_id, :amount, :max_amount, NOW())";
                    $stmtInsert = $this->conn->prepare($sqlInsert);
                    $userId = intval($topProxyBid['user_id']);
                    $lotId = intval($lot_id);
                    $bidAmount = intval($newAmount);
                    $maxAmount = intval($topProxyBid['max_amount']);
                    $stmtInsert->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':amount', $bidAmount, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':max_amount', $maxAmount, PDO::PARAM_INT);
                    try {
                        $stmtInsert->execute();
                        $newBidPlaced = true;
                    } catch (PDOException $e) {
                        error_log("Error placing auto bid for competing proxy (Scenario B): " . $e->getMessage() . " SQL: " . $sqlInsert);
                        throw $e;
                    }
                }
            }
            
            // Step 4: Update the lot's current_price to the new highest bid after any automatic actions.
            $sqlUpdateLot = "UPDATE lots SET current_price = (SELECT MAX(amount) FROM bids WHERE lot_id = :sub_lot_id), updated_at = NOW() WHERE id = :main_lot_id";
            $stmtUpdateLot = $this->conn->prepare($sqlUpdateLot);
            $stmtUpdateLot->bindParam(':sub_lot_id', $lot_id, PDO::PARAM_INT);
            $stmtUpdateLot->bindParam(':main_lot_id', $lot_id, PDO::PARAM_INT);
            $stmtUpdateLot->execute();

            // Step 5: Commit all changes made in this transaction.
            $this->conn->commit();
            
            // Step 6: If an automatic bid was placed, re-run the process.
            // This handles cases where multiple proxy bids might compete with each other in sequence.
            if ($newBidPlaced) {
                return $this->processProxyBidding($lot_id, $recursionDepth + 1);
            }
            
            // If no new bid was placed, or processing is complete for this cycle.
            return true;
        } catch (Exception $e) {
            // If any error occurred during the process, roll back the transaction.
            $this->conn->rollBack();
            // Log the error for server administrators.
            error_log("Error in processProxyBidding: " . $e->getMessage());
            return false; // Indicate failure.
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
    
    /**
     * Calculate bid increment based on current price
     * 
     * @param int $currentPrice The current price of the lot
     * @return int The bid increment amount
     */
    public function getBidIncrement($currentPrice) {
        if ($currentPrice < 30) {
            return 2;
        } elseif ($currentPrice < 100) {
            return 5;
        } elseif ($currentPrice < 200) {
            return 10;
        } elseif ($currentPrice < 500) {
            return 20;
        } elseif ($currentPrice < 1000) {
            return 50;
        } else {
            return 100; // for over 1000, add 100
        }
    }
    
    /**
     * Calculate next minimum bid based on current price
     * 
     * @param int $currentPrice The current price of the lot
     * @return int The next minimum bid amount
     */
    public function getNextMinimumBid($currentPrice) {
        $increment = $this->getBidIncrement($currentPrice);
        return $currentPrice + $increment;
    }
} 