<?php
class AuthController {
    public function showLoginForm() {
        if(isset($_SESSION["user_id"])) { header("Location: /"); exit; }
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Вход</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:500px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea}
            .form-container{background:white;padding:30px;border-radius:10px}
            .form-group{margin-bottom:15px}
            label{display:block;margin-bottom:5px;font-weight:bold}
            input{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px}
            .btn{width:100%;padding:12px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer}
            .error{color:#e53e3e;background:#fff5f5;padding:10px;border-radius:5px;margin-bottom:15px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\"><a href=\"/\">Главная</a><a href=\"/tours\">Туры</a><a href=\"/register\">Регистрация</a></div>
        <div class=\"form-container\"><h1>🔐 Вход</h1>";
        if(isset($_SESSION["login_error"])){echo "<div class=\"error\">".$_SESSION["login_error"]."</div>"; unset($_SESSION["login_error"]);}
        echo "<form method=\"POST\" action=\"/login\">
            <div class=\"form-group\"><label>Логин</label><input type=\"text\" name=\"login\" required></div>
            <div class=\"form-group\"><label>Пароль</label><input type=\"password\" name=\"password\" required></div>
            <button type=\"submit\" class=\"btn\">Войти</button>
        </form>
        <p style=\"text-align:center;margin-top:20px\">Нет аккаунта? <a href=\"/register\">Регистрация</a></p>
        <p style=\"text-align:center;font-size:12px;color:#999\">Тест: admin/admin123 или user/user123</p>
        </div></div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа</p></div>
        </body></html>";
    }
    
    public function login() {
        $login = $_POST["login"] ?? "";
        $password = $_POST["password"] ?? "";
        if($login === "admin" && $password === "admin123") {
            $_SESSION["user_id"] = 1;
            $_SESSION["user_login"] = "admin";
            $_SESSION["user_role"] = "admin";
            $_SESSION["user_name"] = "Администратор";
            header("Location: /"); exit;
        } elseif($login === "user" && $password === "user123") {
            $_SESSION["user_id"] = 2;
            $_SESSION["user_login"] = "user";
            $_SESSION["user_role"] = "client";
            $_SESSION["user_name"] = "Пользователь";
            header("Location: /"); exit;
        } else {
            $_SESSION["login_error"] = "Неверный логин или пароль";
            header("Location: /login"); exit;
        }
    }
    
    public function showRegisterForm() {
        if(isset($_SESSION["user_id"])) { header("Location: /"); exit; }
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Регистрация</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:500px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea}
            .form-container{background:white;padding:30px;border-radius:10px}
            .form-group{margin-bottom:15px}
            label{display:block;margin-bottom:5px;font-weight:bold}
            input{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px}
            .btn{width:100%;padding:12px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer}
            .error{color:#e53e3e;background:#fff5f5;padding:10px;border-radius:5px;margin-bottom:15px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\"><a href=\"/\">Главная</a><a href=\"/tours\">Туры</a><a href=\"/login\">Вход</a></div>
        <div class=\"form-container\"><h1>📝 Регистрация</h1>";
        if(isset($_SESSION["register_error"])){echo "<div class=\"error\">".$_SESSION["register_error"]."</div>"; unset($_SESSION["register_error"]);}
        echo "<form method=\"POST\" action=\"/register\">
            <div class=\"form-group\"><label>Логин</label><input type=\"text\" name=\"login\" required></div>
            <div class=\"form-group\"><label>Email</label><input type=\"email\" name=\"email\" required></div>
            <div class=\"form-group\"><label>Пароль</label><input type=\"password\" name=\"password\" required></div>
            <div class=\"form-group\"><label>Подтвердите пароль</label><input type=\"password\" name=\"confirm_password\" required></div>
            <button type=\"submit\" class=\"btn\">Зарегистрироваться</button>
        </form>
        <p style=\"text-align:center;margin-top:20px\">Уже есть аккаунт? <a href=\"/login\">Войти</a></p>
        </div></div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа</p></div>
        </body></html>";
    }
    
    public function register() {
        $login = $_POST["login"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";
        $confirm = $_POST["confirm_password"] ?? "";
        if(empty($login) || empty($email) || empty($password)) {
            $_SESSION["register_error"] = "Заполните все поля";
        } elseif($password !== $confirm) {
            $_SESSION["register_error"] = "Пароли не совпадают";
        } elseif(strlen($password) < 4) {
            $_SESSION["register_error"] = "Пароль должен быть не менее 4 символов";
        } else {
            $_SESSION["user_id"] = time();
            $_SESSION["user_login"] = $login;
            $_SESSION["user_role"] = "client";
            $_SESSION["user_name"] = $login;
            header("Location: /"); exit;
        }
        header("Location: /register"); exit;
    }
    
    public function logout() {
        session_destroy();
        header("Location: /"); exit;
    }
}
?>