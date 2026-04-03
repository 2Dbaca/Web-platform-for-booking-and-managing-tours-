<?php
require_once 'config/database.php';
require_once 'core/Router.php';
require_once 'core/Session.php';

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . 'controllers/' . $class . '.php',
        BASE_PATH . 'models/' . $class . '.php',
        BASE_PATH . 'core/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

Session::start();

$router = new Router();

// Главная страница
$router->add('/', 'HomeController', 'index', 'GET');

// Аутентификация - ЭТИ СТРОКИ ОБЯЗАТЕЛЬНО ДОЛЖНЫ БЫТЬ
$router->add('/login', 'AuthController', 'showLoginForm', 'GET');
$router->add('/login', 'AuthController', 'login', 'POST');
$router->add('/register', 'AuthController', 'showRegisterForm', 'GET');
$router->add('/register', 'AuthController', 'register', 'POST');
$router->add('/logout', 'AuthController', 'logout', 'GET');

// Профиль
$router->add('/profile', 'ProfileController', 'index', 'GET');
$router->add('/profile/orders', 'ProfileController', 'orders', 'GET');

// Туры
$router->add('/tours', 'TourController', 'index', 'GET');
$router->add('/tours/{id}', 'TourController', 'details', 'GET');

// Админ
$router->add('/admin', 'AdminController', 'dashboard', 'GET');
$router->add('/admin/dashboard', 'AdminController', 'dashboard', 'GET');

$url = $_SERVER['REQUEST_URI'];
$baseUrl = parse_url(BASE_URL, PHP_URL_PATH);
if ($baseUrl && strpos($url, $baseUrl) === 0) {
    $url = substr($url, strlen($baseUrl));
}
$url = ltrim($url, '/');

$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);