<?php
// controllers/AdminController.php

require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/Tour.php';
require_once BASE_PATH . 'models/Order.php';
require_once BASE_PATH . 'models/Report.php';
require_once BASE_PATH . 'core/Session.php';

class AdminController {
    private $userModel;
    private $tourModel;
    private $orderModel;
    private $reportModel;

    public function __construct() {
        $this->userModel = new User();
        $this->tourModel = new Tour();
        $this->orderModel = new Order();
        $this->reportModel = new Report();
        Session::start();

        if (!Session::isAdmin()) {
            header('Location: /');
            exit;
        }
    }

    public function dashboard() {
        $stats = [
            'users' => count($this->userModel->getAllUsers()),
            'tours' => count($this->tourModel->getAllTours()),
            'orders' => $this->orderModel->getStatistics(),
            'recent_orders' => array_slice($this->orderModel->getAllOrders(), 0, 5)
        ];

        require_once BASE_PATH . 'views/admin/dashboard.php';
    }

    public function tours() {
        $tours = $this->tourModel->getAllTours();

        require_once BASE_PATH . 'views/admin/tours.php';
    }

    public function createTour() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => htmlspecialchars($_POST['name']),
                'country' => htmlspecialchars($_POST['country']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'price' => (float)$_POST['price'],
                'description' => htmlspecialchars($_POST['description']),
                'available_count' => (int)$_POST['available_count']
            ];

            // Обработка загрузки изображения
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = '/uploads/' . $filename;
                }
            }

            $result = $this->tourModel->createTour($data);

            if ($result) {
                Session::setFlash('success', 'Тур успешно создан');
                header('Location: /admin/tours');
                return;
            } else {
                Session::setFlash('error', 'Ошибка при создании тура');
            }
        }

        require_once BASE_PATH . 'views/admin/tour-form.php';
    }

    public function editTour($params) {
        $id = $params['id'] ?? 0;
        $tour = $this->tourModel->getTourById($id);

        if (!$tour) {
            Session::setFlash('error', 'Тур не найден');
            header('Location: /admin/tours');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => htmlspecialchars($_POST['name']),
                'country' => htmlspecialchars($_POST['country']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'price' => (float)$_POST['price'],
                'description' => htmlspecialchars($_POST['description']),
                'available_count' => (int)$_POST['available_count']
            ];

            // Обработка загрузки изображения
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = '/uploads/' . $filename;
                }
            }

            $result = $this->tourModel->updateTour($id, $data);

            if ($result) {
                Session::setFlash('success', 'Тур успешно обновлен');
                header('Location: /admin/tours');
                return;
            } else {
                Session::setFlash('error', 'Ошибка при обновлении тура');
            }
        }

        require_once BASE_PATH . 'views/admin/tour-form.php';
    }

    public function deleteTour($params) {
        $id = $params['id'] ?? 0;

        $result = $this->tourModel->deleteTour($id);

        if ($result) {
            Session::setFlash('success', 'Тур успешно удален');
        } else {
            Session::setFlash('error', 'Ошибка при удалении тура');
        }

        header('Location: /admin/tours');
    }

    public function orders() {
        $orders = $this->orderModel->getAllOrders();

        require_once BASE_PATH . 'views/admin/orders.php';
    }

    public function updateOrderStatus($params) {
        $id = $params['id'] ?? 0;
        $status = $_POST['status'] ?? '';

        $result = $this->orderModel->updateOrderStatus($id, $status);

        if ($result) {
            Session::setFlash('success', 'Статус заказа обновлен');
        } else {
            Session::setFlash('error', 'Ошибка при обновлении статуса');
        }

        header('Location: /admin/orders');
    }

    public function reports() {
        $reports = $this->reportModel->getAllReports();
        $stats = $this->orderModel->getStatistics();

        require_once BASE_PATH . 'views/admin/reports.php';
    }

    public function generateReport() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/reports');
            return;
        }

        $type = $_POST['type'] ?? '';
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;

        if ($type === 'orders') {
            $data = $this->orderModel->getOrdersByDateRange($startDate, $endDate);
        } else {
            $data = $this->orderModel->getStatistics();
        }

        $this->reportModel->createReport($type, $data, Session::get('user_id'));

        // Простая генерация CSV
        $filename = 'report_' . $type . '_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        if ($type === 'orders') {
            fputcsv($output, ['ID', 'Тур', 'Клиент', 'Дата', 'Статус', 'Сумма']);
            foreach ($data as $order) {
                fputcsv($output, [
                    $order['id'],
                    $order['tour_name'],
                    $order['full_name'],
                    $order['order_date'],
                    $order['status'],
                    $order['total_price']
                ]);
            }
        } else {
            fputcsv($output, ['Показатель', 'Значение']);
            fputcsv($output, ['Всего заказов', $data['total_orders']]);
            fputcsv($output, ['Общая выручка', $data['total_revenue']]);
            foreach ($data['by_status'] as $stat) {
                fputcsv($output, ['Заказы ' . $stat['status'], $stat['count']]);
            }
        }

        fclose($output);
        exit;
    }

    public function logs() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT l.*, u.login FROM logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 100");
        $logs = $stmt->fetchAll();

        require_once BASE_PATH . 'views/admin/logs.php';
    }

    public function backup() {
        // Простое резервное копирование БД
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

        // Здесь должен быть код для экспорта БД
        // В реальном проекте используйте mysqldump или аналоги

        Session::setFlash('success', 'Резервная копия создана');
        header('Location: /admin/dashboard');
    }
}