<?php
session_start();

// Автозагрузка контроллеров
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . "/controllers/" . $class . ".php",
        __DIR__ . "/core/" . $class . ".php"
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Получаем URL
$requestUri = $_SERVER["REQUEST_URI"];
$url = parse_url($requestUri, PHP_URL_PATH);

// Обработка POST запросов
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($url === "/login") {
        $controller = new AuthController();
        $controller->login();
        exit;
    }
    if ($url === "/register") {
        $controller = new AuthController();
        $controller->register();
        exit;
    }
    if (preg_match('/^\/booking\/(\d+)\/confirm$/', $url, $matches)) {
        $controller = new BookingController();
        $controller->confirmBooking($matches[1]);
        exit;
    }
    if (preg_match('/^\/admin\/order\/(\d+)\/status$/', $url, $matches)) {
        $controller = new AdminController();
        $controller->updateOrderStatus($matches[1]);
        exit;
    }
}

// Обработка GET запросов
if (preg_match('/^\/booking\/(\d+)$/', $url, $matches)) {
    $controller = new BookingController();
    $controller->showBookingForm($matches[1]);
    exit;
}
if (preg_match('/^\/order\/(\d+)\/cancel$/', $url, $matches)) {
    $controller = new ProfileController();
    $controller->cancelOrder($matches[1]);
    exit;
}
if (preg_match('/^\/admin\/tours\/edit\/(\d+)$/', $url, $matches)) {
    $controller = new AdminController();
    $controller->editTour($matches[1]);
    exit;
}
if (preg_match('/^\/admin\/tours\/delete\/(\d+)$/', $url, $matches)) {
    $controller = new AdminController();
    $controller->deleteTour($matches[1]);
    exit;
}

// Маршруты
switch ($url) {
    case "/":
        $controller = new HomeController();
        $controller->index();
        break;
    case "/login":
        $controller = new AuthController();
        $controller->showLoginForm();
        break;
    case "/register":
        $controller = new AuthController();
        $controller->showRegisterForm();
        break;
    case "/logout":
        $controller = new AuthController();
        $controller->logout();
        break;
    case "/profile":
        $controller = new ProfileController();
        $controller->index();
        break;
    case "/profile/orders":
        $controller = new ProfileController();
        $controller->orders();
        break;
    case "/tours":
        $controller = new TourController();
        $controller->index();
        break;
    case "/admin":
    case "/admin/dashboard":
        $controller = new AdminController();
        $controller->dashboard();
        break;
    case "/admin/tours":
        $controller = new AdminController();
        $controller->manageTours();
        break;
    case "/admin/tours/add":
        $controller = new AdminController();
        $controller->addTour();
        break;
    case "/admin/orders":
        $controller = new AdminController();
        $controller->manageOrders();
        break;
    case "/admin/reports":
        $controller = new AdminController();
        $controller->reports();
        break;
    case "/admin/export/excel":
        $controller = new AdminController();
        $controller->exportExcel();
        break;
    case "/admin/export/word":
        $controller = new AdminController();
        $controller->exportWord();
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Страница не найдена</h1><p>URL: " . htmlspecialchars($url) . "</p><a href='/'>На главную</a>";
        break;
}
?>