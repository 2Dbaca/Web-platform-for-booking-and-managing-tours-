<?php
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'core/Session.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        Session::start();
    }
    
    public function showLoginForm() {
        // Если уже авторизован, перенаправляем
        if (Session::isLoggedIn()) {
            header('Location: /profile');
            return;
        }
        
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Вход - ТурПлатформа</title>
            <style>
                body { font-family: Arial; margin: 50px; background: #f5f5f5; }
                .form-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { text-align: center; color: #333; }
                input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
                button { width: 100%; padding: 10px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
                button:hover { background: #5a67d8; }
                .error { color: red; text-align: center; margin-bottom: 10px; }
                .link { text-align: center; margin-top: 15px; }
                a { color: #667eea; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="form-container">
                <h1>🔐 Вход в систему</h1>';
                
        $error = Session::getFlash('error');
        if ($error) {
            echo '<div class="error">' . $error . '</div>';
        }
        
        echo '
                <form method="POST" action="/login">
                    <input type="text" name="login" placeholder="Логин или Email" required>
                    <input type="password" name="password" placeholder="Пароль" required>
                    <button type="submit">Войти</button>
                </form>
                <div class="link">
                    Нет аккаунта? <a href="/register">Зарегистрироваться</a>
                </div>
                <div class="link">
                    <a href="/">← На главную</a>
                </div>
            </div>
        </body>
        </html>';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            return;
        }
        
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->login($login, $password);
        
        if ($user) {
            Session::setUser($user);
            $this->userModel->logAction($user['id'], 'login', 'Успешный вход');
            
            if ($user['role'] === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /profile');
            }
        } else {
            Session::setFlash('error', 'Неверный логин или пароль');
            header('Location: /login');
        }
    }
    
    public function showRegisterForm() {
        if (Session::isLoggedIn()) {
            header('Location: /profile');
            return;
        }
        
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Регистрация - ТурПлатформа</title>
            <style>
                body { font-family: Arial; margin: 50px; background: #f5f5f5; }
                .form-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { text-align: center; color: #333; }
                input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
                button { width: 100%; padding: 10px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
                button:hover { background: #5a67d8; }
                .error { color: red; text-align: center; margin-bottom: 10px; }
                .link { text-align: center; margin-top: 15px; }
                a { color: #667eea; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="form-container">
                <h1>📝 Регистрация</h1>';
                
        $error = Session::getFlash('error');
        if ($error) {
            echo '<div class="error">' . $error . '</div>';
        }
        
        echo '
                <form method="POST" action="/register">
                    <input type="text" name="login" placeholder="Логин" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Пароль (мин. 6 символов)" required>
                    <input type="password" name="confirm_password" placeholder="Подтвердите пароль" required>
                    <input type="text" name="full_name" placeholder="Ваше имя">
                    <input type="text" name="phone" placeholder="Телефон">
                    <button type="submit">Зарегистрироваться</button>
                </form>
                <div class="link">
                    Уже есть аккаунт? <a href="/login">Войти</a>
                </div>
                <div class="link">
                    <a href="/">← На главную</a>
                </div>
            </div>
        </body>
        </html>';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            return;
        }
        
        $errors = [];
        
        if (empty($_POST['login'])) {
            $errors[] = 'Логин обязателен';
        }
        
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email';
        }
        
        if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
            $errors[] = 'Пароль должен быть не менее 6 символов';
        }
        
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $errors[] = 'Пароли не совпадают';
        }
        
        if (!empty($errors)) {
            Session::setFlash('error', implode('<br>', $errors));
            header('Location: /register');
            return;
        }
        
        $result = $this->userModel->register([
            'login' => htmlspecialchars($_POST['login']),
            'email' => htmlspecialchars($_POST['email']),
            'password' => $_POST['password'],
            'full_name' => htmlspecialchars($_POST['full_name'] ?? ''),
            'phone' => htmlspecialchars($_POST['phone'] ?? '')
        ]);
        
        if ($result) {
            Session::setFlash('success', 'Регистрация успешна! Теперь вы можете войти.');
            header('Location: /login');
        } else {
            Session::setFlash('error', 'Ошибка при регистрации. Возможно, логин или email уже заняты.');
            header('Location: /register');
        }
    }
    
    public function logout() {
        Session::start();
        
        if (Session::isLoggedIn()) {
            $userId = Session::get('user_id');
            $this->userModel->logAction($userId, 'logout', 'Выход из системы');
        }
        
        Session::destroy();
        header('Location: /');
    }
}
?>