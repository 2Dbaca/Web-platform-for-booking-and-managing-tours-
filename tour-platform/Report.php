<?php
// models/Report.php
class Report {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Статистика по турам
    public function getToursStats() {
        $sql = "SELECT COUNT(*) as total, 
                       SUM(available_count) as total_places,
                       AVG(price) as avg_price
                FROM tours";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    // Статистика по заказам
    public function getOrdersStats() {
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                       SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                       SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                       SUM(total_price) as total_revenue
                FROM orders";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    // Заказы по месяцам
    public function getOrdersByMonth() {
        $sql = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month,
                       COUNT(*) as count,
                       SUM(total_price) as revenue
                FROM orders
                GROUP BY DATE_FORMAT(order_date, '%Y-%m')
                ORDER BY month DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Популярные туры
    public function getPopularTours($limit = 5) {
        $sql = "SELECT t.name, t.country, COUNT(o.id) as bookings, SUM(o.total_price) as revenue
                FROM tours t
                LEFT JOIN orders o ON t.id = o.tour_id
                GROUP BY t.id
                ORDER BY bookings DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Статистика по пользователям
    public function getUsersStats() {
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                       SUM(CASE WHEN role = 'client' THEN 1 ELSE 0 END) as clients
                FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    // Все заказы для отчёта
    public function getAllOrdersForReport() {
        $sql = "SELECT o.id, o.order_date, o.status, o.total_price, o.participants,
                       u.login, u.email, u.full_name,
                       t.name as tour_name, t.country
                FROM orders o
                JOIN users u ON o.user_id = u.id
                JOIN tours t ON o.tour_id = t.id
                ORDER BY o.order_date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
?>