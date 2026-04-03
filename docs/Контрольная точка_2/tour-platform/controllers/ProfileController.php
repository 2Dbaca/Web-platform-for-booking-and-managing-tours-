<?php
// controllers/ProfileController.php

require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/Order.php';
require_once BASE_PATH . 'core/Session.php';

class ProfileController {
    private $userModel;
    private $orderModel;

    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
        Session::start();

        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $user = $this->userModel->getUserById(Session::get('user_id'));
        $recentOrders = $this->orderModel->getUserOrders(Session::get('user_id'));
        $recentOrders = array_slice($recentOrders, 0, 3);

        require_once BASE_PATH . 'views/profile/index.php';
    }

    public function orders() {
        $orders = $this->orderModel->getUserOrders(Session::get('user_id'));

        require_once BASE_PATH . 'views/profile/orders.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            return;
        }

        $data = [
            'full_name' => htmlspecialchars($_POST['full_name'] ?? ''),
            'phone' => htmlspecialchars($_POST['phone'] ?? '')
        ];

        $result = $this->userModel->updateProfile(Session::get('user_id'), $data);

        if ($result) {
            Session::setFlash('success', 'Профиль успешно обновлен');
        } else {
            Session::setFlash('error', 'Ошибка при обновлении профиля');
        }

        header('Location: /profile');
    }

    public function cancelOrder($params) {
        $orderId = $params['id'] ?? 0;

        $order = $this->orderModel->getOrderById($orderId);

        if (!$order || $order['user_id'] != Session::get('user_id')) {
            Session::setFlash('error', 'Заказ не найден');
            header('Location: /profile/orders');
            return;
        }

        if ($order['status'] === 'cancelled') {
            Session::setFlash('error', 'Заказ уже отменен');
            header('Location: /profile/orders');
            return;
        }

        $result = $this->orderModel->cancelOrder($orderId);

        if ($result) {
            Session::setFlash('success', 'Заказ успешно отменен');
        } else {
            Session::setFlash('error', 'Ошибка при отмене заказа');
        }

        header('Location: /profile/orders');
    }
}