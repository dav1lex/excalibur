<?php
require_once 'models/BaseModel.php';

class Auction extends BaseModel
{

    /**
     * Get all auctions
     */
    public function getAll($limit = null, $offset = 0)
    {
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
    public function getAllPublic($limit = null, $offset = 0)
    {
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
    public function getUpcomingAuctions()
    {
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
    public function getLiveAuctions()
    {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM auctions WHERE status = 'live' OR (start_date <= :now1 AND end_date >= :now2 AND status != 'draft') ORDER BY end_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':now1', $now);
        $stmt->bindParam(':now2', $now);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ended auctions
     */
    public function getEndedAuctions()
    {
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
    public function getById($id)
    {
        $sql = "SELECT * FROM auctions WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new auction
     */
    public function create($data)
    {
        $sql = "INSERT INTO auctions (title, description, start_date, end_date, status, image_path, created_at, updated_at) 
                VALUES (:title, :description, :start_date, :end_date, :status, :image_path, NOW(), NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':image_path', $data['image_path']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Update an auction
     */
    public function update($id, $data)
    {
        // Build dynamic update query
        $fields = [];
        $params = [':id' => $id];

        // Dynamically build the SET clause based on provided data
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE auctions SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $param => &$value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam($param, $value, $type);
        }

        return $stmt->execute();
    }

    /**
     * Delete an auction
     */
    public function delete($id)
    {
        $sql = "DELETE FROM auctions WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Count total auctions
     */
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) FROM auctions";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Update auction statuses based on start_date and end_date
     * This handles auction transitions and sends winning notifications
     */
    public function updateStatuses()
    {
        // Find live auctions that should now be ended
        $sqlLiveEnded = "SELECT id FROM auctions WHERE status = 'live' AND end_date <= NOW()";
        $stmtLiveEnded = $this->conn->prepare($sqlLiveEnded);
        $stmtLiveEnded->execute();
        $auctionsToEnd = $stmtLiveEnded->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($auctionsToEnd)) {
            require_once 'controllers/BidController.php';
            $bidController = new BidController();

            $auctionsUpdated = [];

            foreach ($auctionsToEnd as $auctionId) {
                try {
                    $this->conn->beginTransaction();

                    // Update auction status to ended
                    $sqlUpdate = "UPDATE auctions SET status = 'ended', updated_at = NOW() WHERE id = :id AND status = 'live'";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    $stmtUpdate->bindParam(':id', $auctionId, PDO::PARAM_INT);
                    $stmtUpdate->execute();
                    $rowsAffected = $stmtUpdate->rowCount();

                    if ($rowsAffected > 0) {
                        // Get all lots in this auction
                        $sqlLots = "SELECT id FROM lots WHERE auction_id = :auction_id";
                        $stmtLots = $this->conn->prepare($sqlLots);
                        $stmtLots->bindParam(':auction_id', $auctionId, PDO::PARAM_INT);
                        $stmtLots->execute();
                        $lots = $stmtLots->fetchAll(PDO::FETCH_COLUMN);

                        // For each lot, update bid statuses
                        foreach ($lots as $lotId) {
                            // Find the highest bid for this lot
                            $sqlHighestBid = "SELECT id FROM bids WHERE lot_id = :lot_id AND status = 'active' ORDER BY amount DESC LIMIT 1";
                            $stmtHighestBid = $this->conn->prepare($sqlHighestBid);
                            $stmtHighestBid->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
                            $stmtHighestBid->execute();
                            $highestBidId = $stmtHighestBid->fetchColumn();

                            if ($highestBidId) {
                                // Update the highest bid to 'won'
                                $sqlUpdateWinner = "UPDATE bids SET status = 'won' WHERE id = :bid_id";
                                $stmtUpdateWinner = $this->conn->prepare($sqlUpdateWinner);
                                $stmtUpdateWinner->bindParam(':bid_id', $highestBidId, PDO::PARAM_INT);
                                $stmtUpdateWinner->execute();

                                // Update all other active bids for this lot to 'lost'
                                $sqlUpdateLosers = "UPDATE bids SET status = 'lost' WHERE lot_id = :lot_id AND id != :bid_id AND status = 'active'";
                                $stmtUpdateLosers = $this->conn->prepare($sqlUpdateLosers);
                                $stmtUpdateLosers->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
                                $stmtUpdateLosers->bindParam(':bid_id', $highestBidId, PDO::PARAM_INT);
                                $stmtUpdateLosers->execute();
                            }
                        }

                        // Add this auction to the list of successfully updated auctions
                        $auctionsUpdated[] = $auctionId;
                    }

                    $this->conn->commit();
                } catch (Exception $e) {
                    $this->conn->rollBack();
                    error_log("Error updating auction status: " . $e->getMessage());
                }
            }

            // Send notifications for each successfully updated auction AFTER all transactions are committed
            foreach ($auctionsUpdated as $auctionId) {
                $bidController->sendWinningNotifications($auctionId, false, true);
            }
        }

        // Handle Upcoming to Live transition
        $sqlUpcomingLive = "UPDATE auctions SET status = 'live', updated_at = NOW() 
                            WHERE status = 'upcoming' AND start_date <= NOW() AND end_date > NOW()";
        $stmtUpcomingLive = $this->conn->prepare($sqlUpcomingLive);
        $stmtUpcomingLive->execute();

        return true;
    }

    /**
     * Get auctions by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT * FROM auctions WHERE status = :status ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}