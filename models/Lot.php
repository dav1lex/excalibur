<?php
require_once 'models/BaseModel.php';

class Lot extends BaseModel
{

    /**
     * Get all lots
     */
    public function getAll($limit = null, $offset = 0)
    {
        $sql = "SELECT l.*, a.title as auction_title FROM lots l 
                JOIN auctions a ON l.auction_id = a.id 
                ORDER BY l.created_at DESC";

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
     * Get lot by ID
     */
    public function getById($id)
    {
        $sql = "SELECT l.*, a.title as auction_title, a.status as auction_status, a.start_date as auction_start_date, a.end_date as auction_end_date 
                FROM lots l 
                JOIN auctions a ON l.auction_id = a.id 
                WHERE l.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get lots by auction ID
     */
    public function getByAuctionId($auction_id)
    {
        $sql = "SELECT * FROM lots WHERE auction_id = :auction_id ORDER BY lot_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':auction_id', $auction_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get related lots from same auction, excluding current lot
     */
    public function getRelated($auction_id, $exclude_id, $limit = 4)
    {
        $sql = "SELECT * FROM lots 
                WHERE auction_id = :auction_id 
                AND id != :exclude_id 
                ORDER BY RAND() 
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':auction_id', $auction_id, PDO::PARAM_INT);
        $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new lot
     */
    public function create($data)
    {
        $sql = "INSERT INTO lots (auction_id, title, description, lot_number, starting_price, reserve_price, image_path, current_price, created_at, updated_at) 
                VALUES (:auction_id, :title, :description, :lot_number, :starting_price, :reserve_price, :image_path, :current_price, NOW(), NOW())";

        $stmt = $this->conn->prepare($sql);

        // Set starting price as current price initially
        $current_price = $data['starting_price'];

        // Bind all parameters
        $stmt->bindParam(':auction_id', $data['auction_id'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':lot_number', $data['lot_number'], PDO::PARAM_STR);
        $stmt->bindParam(':starting_price', $data['starting_price'], PDO::PARAM_INT);
        $stmt->bindParam(':current_price', $current_price, PDO::PARAM_INT);

        // Handle potentially NULL values
        if ($data['reserve_price'] === null || $data['reserve_price'] === '') {
            $stmt->bindValue(':reserve_price', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':reserve_price', $data['reserve_price'], PDO::PARAM_INT);
        }

        if ($data['image_path'] === null || $data['image_path'] === '') {
            $stmt->bindValue(':image_path', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':image_path', $data['image_path'], PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Update a lot
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];

        // Dynamically build the SET clause based on provided data
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE lots SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $param => &$value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam($param, $value, $type);
        }

        return $stmt->execute();
    }

    /**
     * Delete a lot
     */
    public function delete($id)
    {
        $sql = "DELETE FROM lots WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    /**
     * Count total lots
     */
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) FROM lots";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Count lots by auction ID
     */
    public function countByAuction($auction_id)
    {
        // Cast auction_id to integer to ensure proper comparison
        $auction_id = (int) $auction_id;

        $sql = "SELECT COUNT(*) FROM lots WHERE auction_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $auction_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Search lots by description, title or lot number
     */
    public function search($search, $auction_id = null)
    {
        // Simple search query with basic conditions
        $sql = "SELECT l.*, a.title as auction_title 
                FROM lots l 
                JOIN auctions a ON l.auction_id = a.id 
                WHERE l.title LIKE ? OR l.description LIKE ?";

        // Add lot number search for numeric values
        if (is_numeric($search)) {
            $lot_number = 'LOT-' . sprintf("%03d", (int) $search);
            $sql .= " OR l.lot_number = ?";
        }

        // Add auction filter if provided
        if ($auction_id) {
            $sql .= " AND l.auction_id = ?";
        }

        // Add ordering
        $sql .= " ORDER BY l.lot_number ASC";

        // Prepare statement
        $stmt = $this->conn->prepare($sql);

        // Create parameters array
        $params = [];
        $params[] = '%' . $search . '%';  // For title
        $params[] = '%' . $search . '%';  // For description

        // Add lot number parameter if needed
        if (is_numeric($search)) {
            $params[] = 'LOT-' . sprintf("%03d", (int) $search);
        }

        // Add auction_id parameter if needed
        if ($auction_id) {
            $params[] = $auction_id;
        }

        // Execute with parameters array
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate total current price of all lots in an auction
     */
    public function calculateAuctionTotal($auction_id)
    {
        $sql = "SELECT SUM(current_price) as total FROM lots WHERE auction_id = :auction_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':auction_id', $auction_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Get all lots with their auction titles
     */
    public function getAllWithAuctionTitle($limit = null, $offset = 0)
    {
        $sql = "SELECT l.*, a.title as auction_title 
                FROM lots l
                JOIN auctions a ON l.auction_id = a.id
                ORDER BY l.created_at DESC";

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
}