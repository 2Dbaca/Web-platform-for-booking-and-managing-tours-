<?php
session_start();

$url = $_SERVER["REQUEST_URI"];
$url = parse_url($url, PHP_URL_PATH);
$url = rtrim($url, "/");
if ($url === "") $url = "/";

$method = $_SERVER["REQUEST_METHOD"];

// Функция для шапки
function header_html() {
    echo "<!DOCTYPE html>
    <html>
    <head><meta charset=\"UTF-8\"><title>ТурПлатформа</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Arial;background:#f0f2f5}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
        .container{max-width:1200px;margin:0 auto;padding:20px}
        .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
        .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
        .hero{background:white;border-radius:10px;padding:40px;text-align:center;margin-bottom:20px}
        .btn{display:inline-block;padding:12px 24px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin:5px}
        .btn-secondary{background:#48bb78}
        .tours-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:20px}
        .tour-card{background:white;border-radius:10px;padding:20px;text-align:center}
        .tour-price{color:#48bb78;font-size:24px;font-weight:bold;margin:10px 0}
        .form-container{max-width:500px;margin:0 auto;background:white;padding:30px;border-radius:10px}
        .form-group{margin-bottom:15px}
        label{display:block;margin-bottom:5px;font-weight:bold}
        input{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px}
        .error{color:#e53e3e;background:#fff5f5;padding:10px;border-radius:5px;margin-bottom:15px}
        .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
        h1,h2{text-align:center;margin-bottom:20px}
    </style>
    </head>
    <body>
    <div class=\"header\"><h1>🌍 ТурПлатформа</h1><p>Путешествуйте с нами!</p></div>
    <div class=\"container\">
    <div class=\"nav\">
        <a href=\"/\">Главная</a>
        <a href=\"/tours\">Туры</a>";
        if(isset($_SESSION["user_id"])) {
            echo "<a href=\"/profile\">Личный кабинет</a>";
            if(isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") echo "<a href=\"/admin\">Админ-панель</a>";
            echo "<a href=\"/logout\">Выйти (" . htmlspecialchars($_SESSION["user_login"]) . ")</a>";
        } else {
            echo "<a href=\"/login\">Вход</a>";
            echo "<a href=\"/register\">Регистрация</a>";
        }
    echo "</div>";
}

function footer_html() {
    echo "</div><div class=\"footer\"><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div></body></html>";
}

// Роутинг
switch($url) {
    case "/":
        header_html();
        echo "<div class=\"hero\">
            <h2>Откройте для себя лучшие туры</h2>
            <p>Более 100 направлений по всему миру</p>
            <a href=\"/tours\" class=\"btn\">Найти тур</a>";
            if(!isset($_SESSION["user_id"])) echo "<a href=\"/register\" class=\"btn btn-secondary\">Зарегистрироваться</a>";
        echo "</div>
        <h2>🔥 Популярные туры</h2>
        <div class=\"tours-grid\">
            <div class=\"tour-card\"><h3>🏖️ Турция</h3><p>Анталья, 7 ночей</p><div class=\"tour-price\">45 000 ₽</div><a href=\"/tours\" class=\"btn\">Подробнее</a></div>
            <div class=\"tour-card\"><h3>🗼 Париж</h3><p>Франция, 5 ночей</p><div class=\"tour-price\">65 000 ₽</div><a href=\"/tours\" class=\"btn\">Подробнее</a></div>
            <div class=\"tour-card\"><h3>🏛️ Италия</h3><p>Рим-Флоренция-Венеция</p><div class=\"tour-price\">89 000 ₽</div><a href=\"/tours\" class=\"btn\">Подробнее</a></div>
        </div>";
        footer_html();
        break;
        
    case "/login":
        if($method === "POST") {
            $login = $_POST["login"] ?? "";
            $password = $_POST["password"] ?? "";
            if($login === "admin" && $password === "admin123") {
                $_SESSION["user_id"] = 1;
                $_SESSION["user_login"] = "admin";
                $_SESSION["user_role"] = "admin";
                $_SESSION["user_name"] = "Администратор";
                header("Location: /");
                exit;
            } elseif($login === "user" && $password === "user123") {
                $_SESSION["user_id"] = 2;
                $_SESSION["user_login"] = "user";
                $_SESSION["user_role"] = "client";
                $_SESSION["user_name"] = "Пользователь";
                header("Location: /");
                exit;
            } else {
                $error = "Неверный логин или пароль";
            }
        }
        header_html();
        echo "<div class=\"form-container\">
            <h1>🔐 Вход</h1>";
            if(isset($error)) echo "<div class=\"error\">$error</div>";
            echo "<form method=\"POST\">
                <div class=\"form-group\"><label>Логин</label><input type=\"text\" name=\"login\" required></div>
                <div class=\"form-group\"><label>Пароль</label><input type=\"password\" name=\"password\" required></div>
                <button type=\"submit\" class=\"btn\" style=\"width:100%\">Войти</button>
            </form>
            <p style=\"text-align:center;margin-top:20px\">Нет аккаунта? <a href=\"/register\">Регистрация</a></p>
            <p style=\"text-align:center;font-size:12px;color:#999\">Тест: admin/admin123 или user/user123</p>
        </div>";
        footer_html();
        break;
        
    case "/register":
        if($method === "POST") {
            $login = $_POST["login"] ?? "";
            $email = $_POST["email"] ?? "";
            $password = $_POST["password"] ?? "";
            $confirm = $_POST["confirm_password"] ?? "";
            if(empty($login) || empty($email) || empty($password)) {
                $error = "Заполните все поля";
            } elseif($password !== $confirm) {
                $error = "Пароли не совпадают";
            } elseif(strlen($password) < 4) {
                $error = "Пароль должен быть не менее 4 символов";
            } else {
                $_SESSION["user_id"] = time();
                $_SESSION["user_login"] = $login;
                $_SESSION["user_role"] = "client";
                $_SESSION["user_name"] = $login;
                header("Location: /");
                exit;
            }
        }
        header_html();
        echo "<div class=\"form-container\">
            <h1>📝 Регистрация</h1>";
            if(isset($error)) echo "<div class=\"error\">$error</div>";
            echo "<form method=\"POST\">
                <div class=\"form-group\"><label>Логин</label><input type=\"text\" name=\"login\" required></div>
                <div class=\"form-group\"><label>Email</label><input type=\"email\" name=\"email\" required></div>
                <div class=\"form-group\"><label>Пароль</label><input type=\"password\" name=\"password\" required></div>
                <div class=\"form-group\"><label>Подтвердите пароль</label><input type=\"password\" name=\"confirm_password\" required></div>
                <button type=\"submit\" class=\"btn\" style=\"width:100%\">Зарегистрироваться</button>
            </form>
            <p style=\"text-align:center;margin-top:20px\">Уже есть аккаунт? <a href=\"/login\">Войти</a></p>
        </div>";
        footer_html();
        break;
        
    case "/logout":
        session_destroy();
        header("Location: /");
        exit;
        break;
        
    case "/profile":
        if(!isset($_SESSION["user_id"])) { header("Location: /login"); exit; }
        header_html();
        echo "<div class=\"form-container\">
            <h1>👤 Личный кабинет</h1>
            <div class=\"form-group\"><label>Логин</label><input type=\"text\" value=\"".htmlspecialchars($_SESSION["user_login"])."\" disabled></div>
            <div class=\"form-group\"><label>Имя</label><input type=\"text\" value=\"".htmlspecialchars($_SESSION["user_name"])."\" disabled></div>
            <div class=\"form-group\"><label>Роль</label><input type=\"text\" value=\"".htmlspecialchars($_SESSION["user_role"])."\" disabled></div>
            <a href=\"/\" class=\"btn\">На главную</a>
        </div>";
        footer_html();
        break;
        
    case "/tours":
        header_html();
        echo "<h1>✈️ Доступные туры</h1>
        <div class=\"tours-grid\">
            <div class=\"tour-card\"><h3>🏖️ Турция</h3><p>Анталья, 7 ночей, 5*</p><div class=\"tour-price\">45 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🗼 Париж</h3><p>Франция, 5 ночей, 4*</p><div class=\"tour-price\">65 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🏛️ Италия</h3><p>10 ночей</p><div class=\"tour-price\">89 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🇬🇷 Греция</h3><p>о.Крит, 7 ночей</p><div class=\"tour-price\">78 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🇪🇸 Испания</h3><p>Барселона, 8 ночей</p><div class=\"tour-price\">71 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🇦🇪 ОАЭ</h3><p>Дубай, 6 ночей</p><div class=\"tour-price\">98 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
        </div>";
        footer_html();
        break;
        
    case "/admin":
    case "/admin/dashboard":
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { header("Location: /"); exit; }
        header_html();
        echo "<h1>🔧 Админ-панель</h1>
        <div class=\"tours-grid\" style=\"grid-template-columns:1fr\">
            <div class=\"tour-card\"><h3>📊 Статистика</h3><p>Туров: 6 | Пользователей: 2</p></div>
            <div class=\"tour-card\"><h3>📋 Управление турами</h3><a href=\"/tours\" class=\"btn\">Управлять</a></div>
            <div class=\"tour-card\"><h3>📦 Управление заказами</h3><a href=\"/tours\" class=\"btn\">Управлять</a></div>
        </div>";
        footer_html();
        break;
        
    default:
        header_html();
        echo "<div class=\"form-container\" style=\"text-align:center\"><h1>404</h1><p>Страница не найдена: $url</p><a href=\"/\" class=\"btn\">На главную</a></div>";
        footer_html();
        break;
}
?>