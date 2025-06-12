    <?php
    require_once 'config/Database.php';

    abstract class BaseModel
    {
        protected $conn;
        protected $table;

        public function __construct()
        {
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        // get all
        public function getAll($limit = null, $offset = 0)
        {
            $query = "SELECT * FROM {$this->table}";

            if ($limit) {
                $query .= " LIMIT :limit OFFSET :offset";
            }

            $stmt = $this->conn->prepare($query);

            if ($limit) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function getById($id)
        {
            $query = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        }

        public function delete($id)
        {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }