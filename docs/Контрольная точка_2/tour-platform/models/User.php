<?php
require_once BASE_PATH . 'core/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($data) {
        // Проверка на существование
        $checkSql = "SELECT id FROM users WHERE login = :login OR email = :email";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':login' => $data['login'],
            ':email' => $data['email']
        ]);
        
        if ($checkStmt->fetch()) {
            return false;
        }
        
        $sql = "INSERT INTO users (login, email, password, full_name, phone, role) 
                VALUES (:login, :email, :password, :full_name, :phone, 'client')";
        
        $stmt = $this->db->prepare($sql);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        return $stmt->execute([
            ':login' => $data['login'],
            ':email' => $data['email'],
            ':password' => $hashedPassword,
            ':full_name' => $data['full_name'] ?? null,
            ':phone' => $data['phone'] ?? null
        ]);
    }
    
    public function login($login, $password) {
        $sql = "SELECT * FROM users WHERE login = :login OR email = :login";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':login' => $login]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, login, email, full_name, phone, role, created_at 
                FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }
    
    public function logAction($userId, $action, $details = null) {
        $sql = "INSERT INTO logs (user_id, action, details, ip_address) 
                VALUES (:user_id, :action, :details, :ip_address)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':details' => $details,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
}
?>