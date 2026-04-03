<?php
// controllers/TourController.php

require_once BASE_PATH . 'models/Tour.php';
require_once BASE_PATH . 'models/Order.php';
require_once BASE_PATH . 'core/Session.php';

class TourController {
    private $tourModel;
    private $orderModel;

    public function __construct() {
        $this->tourModel = new Tour();
        $this->orderModel = new Order();
        Session::start();
    }

    public function index() {
        $filters = [];

        if (isset($_GET['country']) && !empty($_GET['country'])) {
            $filters['country'] = htmlspecialchars($_GET['country']);
        }

        if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
            $filters['start_date'] = htmlspecialchars($_GET['start_date']);
        }

        if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
            $filters['end_date'] = htmlspecialchars($_GET['end_date']);
        }

        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $filters['max_price'] = (float)$_GET['max_price'];
        }

        $tours = $this->tourModel->getAllTours($filters);
        $popular = $this->tourModel->getPopularDestinations(3);

        require_once BASE_PATH . 'views/tours/index.php';
    }

    public function details($params) {
        $id = $params['id'] ?? 0;
        $tour = $this->tourModel->getTourById($id);

        if (!$tour) {
            header("HTTP/1.0 404 Not Found");
            echo "Тур не найден";
            return;
        }

        $relatedTours = $this->tourModel->getToursByCountry($tour['country']);

        require_once BASE_PATH . 'views/tours/details.php';
    }

    public function book($params) {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Для бронирования необходимо войти в систему');
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tours/' . $params['id']);
            return;
        }

        $tourId = $params['id'];
        $participants = (int)($_POST['participants'] ?? 1);

        $tour = $this->tourModel->getTourById($tourId);

        if (!$tour) {
            Session::setFlash('error', 'Тур не найден');
            header('Location: /tours');
            return;
        }

        if ($tour['available_count'] < $participants) {
            Session::setFlash('error', 'Недостаточно свободных мест');
            header('Location: /tours/' . $tourId);
            return;
        }

        $result = $this->orderModel->createOrder(
            Session::get('user_id'),
            $tourId,
            $participants
        );

        if ($result) {
            $userModel = new User();
            $userModel->logAction(Session::get('user_id'), 'booking', 'Забронирован тур: ' . $tour['name']);

            Session::setFlash('success', 'Тур успешно забронирован!');
            header('Location: /profile/orders');
        } else {
            Session::setFlash('error', 'Ошибка при бронировании');
            header('Location: /tours/' . $tourId);
        }
    }

    public function search() {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            header('Location: /tours');
            return;
        }

        $filters = ['country' => $query];
        $tours = $this->tourModel->getAllTours($filters);

        require_once BASE_PATH . 'views/tours/search.php';
    }
}