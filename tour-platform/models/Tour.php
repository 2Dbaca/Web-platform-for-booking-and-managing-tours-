<?php
require_once BASE_PATH . 'core/Database.php';

class Tour {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllTours($filters = []) {
        $sql = "SELECT * FROM tours WHERE 1=1";
        $params = [];
        
        if (!empty($filters['country'])) {
            $sql .= " AND country LIKE :country";
            $params[':country'] = '%' . $filters['country'] . '%';
        }
        
        $sql .= " ORDER BY start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function getTourById($id) {
        $sql = "SELECT * FROM tours WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }
}
?>