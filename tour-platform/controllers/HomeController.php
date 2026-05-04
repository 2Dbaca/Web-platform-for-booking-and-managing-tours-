<?php
// controllers/HomeController.php

class HomeController {
    public function index() {
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>ТурПлатформа</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .hero{background:white;border-radius:10px;padding:40px;text-align:center;margin-bottom:20px;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .btn{display:inline-block;padding:12px 24px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin:5px}
            .btn-secondary{background:#48bb78}
            .tours-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:20px}
            .tour-card{background:white;border-radius:10px;padding:20px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .tour-price{color:#48bb78;font-size:24px;font-weight:bold;margin:10px 0}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h2{text-align:center;margin-bottom:20px;color:#333}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1><p>Путешествуйте с нами!</p></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/tours\">Туры</a>";
            
            // ПРОВЕРКА АВТОРИЗАЦИИ ДЛЯ МЕНЮ
            if(isset($_SESSION["user_id"])) {
                echo "<a href=\"/profile\">Личный кабинет</a>";
                // ПРОВЕРКА РОЛИ ДЛЯ АДМИН-ПАНЕЛИ
                if(isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") {
                    echo "<a href=\"/admin\">Админ-панель</a>";
                }
                echo "<a href=\"/logout\">Выйти (" . htmlspecialchars($_SESSION["user_login"]) . ")</a>";
            } else {
                echo "<a href=\"/login\">Вход</a>";
                echo "<a href=\"/register\">Регистрация</a>";
            }
            
        echo "</div>
        <div class=\"hero\">
            <h2>Откройте для себя лучшие туры</h2>
            <p>Более 100 направлений по всему миру</p>
            <a href=\"/tours\" class=\"btn\">Найти тур</a>";
            if(!isset($_SESSION["user_id"])) {
                echo "<a href=\"/register\" class=\"btn btn-secondary\">Зарегистрироваться</a>";
            }
        echo "</div>
        <h2>🔥 Популярные туры</h2>
        <div class=\"tours-grid\">
            <div class=\"tour-card\"><h3>🏖️ Турция</h3><p>Анталья, 7 ночей</p><div class=\"tour-price\">45 000 ₽</div><a href=\"/tours\" class=\"btn\">Подробнее</a></div>
            <div class=\"tour-card\"><h3>🗼 Париж</h3><p>Франция, 5 ночей</p><div class=\"tour-price\">65 000 ₽</div><a href=\"/tours\" class=\"btn\">Подробнее</a></div>
            <div class=\"tour-card\"><h3>🏛️ Италия</h3><p>Рим-Флоренция-Венеция</p><div class=\"tour-price\">89 000 ₽</div><a href=\"/tours\" class=\"btn\">Подробнее</a></div>
        </div>
        </div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div>
        </body></html>";
    }
}
?>