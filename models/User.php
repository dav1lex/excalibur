<?php
require_once 'models/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function create($name, $email, $password, $role = 'user') {
        // Generate a confirmation token
        $confirmationToken = $this->generateConfirmationToken();
        
        $query = "INSERT INTO users (name, email, password, role, confirmation_token) VALUES (:name, :email, :password, :role, :token)";
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':token', $confirmationToken);
        
        if ($stmt->execute()) {
            return [
                'user_id' => $this->conn->lastInsertId(),
                'confirmation_token' => $confirmationToken
            ];
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }
        
        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function getByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function validateLogin($email, $password) {
        $user = $this->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Check if email is confirmed
            if ($user['is_confirmed'] == 0) {
                return 'unconfirmed';
            }
            return $user;
        }
        
        return false;
    }
    
    public function confirmEmail($token) {
        $query = "UPDATE users SET is_confirmed = 1, confirmation_token = NULL WHERE confirmation_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
    
    public function getUserByToken($token) {
        $query = "SELECT * FROM users WHERE confirmation_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function regenerateToken($email) {
        $token = $this->generateConfirmationToken();
        
        $query = "UPDATE users SET confirmation_token = :token WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            return $token;
        }
        
        return false;
    }
    
    private function generateConfirmationToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role) {
        $query = "SELECT * FROM users WHERE role = :role ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create a password reset token for a user
     */
    public function createPasswordResetToken($email) {
        $token = $this->generateConfirmationToken();
        $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $query = "UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Get user by reset token
     */
    public function getUserByResetToken($token) {
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > :now";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Reset user password
     */
    public function resetPassword($token, $password) {
        $now = date('Y-m-d H:i:s');
        
        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL 
                  WHERE reset_token = :token AND reset_token_expiry > :now";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':now', $now);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
} 