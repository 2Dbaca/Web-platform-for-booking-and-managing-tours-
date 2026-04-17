<?php
// models/Order.php

require_once BASE_PATH . 'core/Database.php';

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createOrder($userId, $tourId, $participants = 1) {
        // Получаем информацию о туре
        $tourModel = new Tour();
        $tour = $tourModel->getTourById($tourId);

        if (!$tour || $tour['available_count'] < $participants) {
            return false;
        }

        $totalPrice = $tour['price'] * $participants;

        $sql = "INSERT INTO orders (user_id, tour_id, total_price, participants, status)
                VALUES (:user_id, :tour_id, :total_price, :participants, 'pending')";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':user_id' => $userId,
            ':tour_id' => $tourId,
            ':total_price' => $totalPrice,
            ':participants' => $participants
        ]);

        if ($result) {
            // Уменьшаем количество доступных мест
            $updateSql = "UPDATE tours SET available_count = available_count - :participants WHERE id = :tour_id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                ':participants' => $participants,
                ':tour_id' => $tourId
            ]);
        }

        return $result;
    }

    public function getUserOrders($userId) {
        $sql = "SELECT o.*, t.name as tour_name, t.country, t.start_date, t.end_date
                FROM orders o
                JOIN tours t ON o.tour_id = t.id
                WHERE o.user_id = :user_id
                ORDER BY o.order_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function getOrderById($id) {
        $sql = "SELECT o.*, t.name as tour_name, t.country, t.start_date, t.end_date,
                       u.login, u.email, u.full_name
                FROM orders o
                JOIN tours t ON o.tour_id = t.id
                JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function updateOrderStatus($id, $status) {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }

    public function cancelOrder($id) {
        $order = $this->getOrderById($id);

        if (!$order) {
            return false;
        }

        // Возвращаем места
        $updateSql = "UPDATE tours SET available_count = available_count + :participants WHERE id = :tour_id";
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute([
            ':participants' => $order['participants'],
            ':tour_id' => $order['tour_id']
        ]);

        // Обновляем статус заказа
        return $this->updateOrderStatus($id, 'cancelled');
    }

    public function getAllOrders() {
        $sql = "SELECT o.*, t.name as tour_name, t.country, u.login, u.full_name
                FROM orders o
                JOIN tours t ON o.tour_id = t.id
                JOIN users u ON o.user_id = u.id
                ORDER BY o.order_date DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getOrdersByDateRange($startDate, $endDate) {
        $sql = "SELECT o.*, t.name as tour_name, u.full_name
                FROM orders o
                JOIN tours t ON o.tour_id = t.id
                JOIN users u ON o.user_id = u.id
                WHERE o.order_date BETWEEN :start_date AND :end_date
                ORDER BY o.order_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);

        return $stmt->fetchAll();
    }

    public function getStatistics() {
        $stats = [];

        // Общее количество заказов
        $sql = "SELECT COUNT(*) as total FROM orders";
        $stmt = $this->db->query($sql);
        $stats['total_orders'] = $stmt->fetch()['total'];

        // Заказы по статусам
        $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $stmt = $this->db->query($sql);
        $stats['by_status'] = $stmt->fetchAll();

        // Общая выручка
        $sql = "SELECT SUM(total_price) as total_revenue FROM orders WHERE status != 'cancelled'";
        $stmt = $this->db->query($sql);
        $stats['total_revenue'] = $stmt->fetch()['total_revenue'] ?? 0;

        return $stats;
    }
}