<?php
class ProfileController {
    public function index() {
        if(!isset($_SESSION["user_id"])) { header("Location: /login"); exit; }
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Личный кабинет</title>
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
            input{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px;background:#f9f9f9}
            .btn{display:inline-block;padding:12px 24px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\"><a href=\"/\">Главная</a><a href=\"/tours\">Туры</a><a href=\"/logout\">Выйти</a></div>
        <div class=\"form-container\"><h1>👤 Личный кабинет</h1>
            <div class=\"form-group\"><label>Логин</label><input type=\"text\" value=\"".htmlspecialchars($_SESSION["user_login"])."\" disabled></div>
            <div class=\"form-group\"><label>Имя</label><input type=\"text\" value=\"".htmlspecialchars($_SESSION["user_name"])."\" disabled></div>
            <div class=\"form-group\"><label>Роль</label><input type=\"text\" value=\"".htmlspecialchars($_SESSION["user_role"])."\" disabled></div>
            <a href=\"/\" class=\"btn\">На главную</a>
        </div></div>
        <div class=\"footer\"><p>© 2026 ТурПлатформа</p></div>
        </body></html>";
    }
    public function orders() { echo "<h1>Мои заказы</h1><p>У вас пока нет заказов</p><a href=\"/tours\">Выбрать тур</a>"; }
}
?>