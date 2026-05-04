<?php
// controllers/TourController.php

class TourController {
    public function index() {
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Туры</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .tours-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:20px}
            .tour-card{background:white;border-radius:10px;padding:20px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .tour-price{color:#48bb78;font-size:24px;font-weight:bold;margin:10px 0}
            .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .btn-book{background:#48bb78}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px;color:#333}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/tours\">Туры</a>";
            if(isset($_SESSION["user_id"])) {
                echo "<a href=\"/profile\">Профиль</a>";
                if(isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") {
                    echo "<a href=\"/admin\">Админ-панель</a>";
                }
                echo "<a href=\"/logout\">Выйти</a>";
            } else {
                echo "<a href=\"/login\">Вход</a><a href=\"/register\">Регистрация</a>";
            }
        echo "</div><h1>✈️ Доступные туры</h1>
        <div class=\"tours-grid\">";
        
        // Список туров
        $tours = [
            1 => ['name' => '🏖️ Турция', 'desc' => 'Анталья, 7 ночей, 5*', 'price' => '45 000 ₽'],
            2 => ['name' => '🗼 Париж', 'desc' => 'Франция, 5 ночей, 4*', 'price' => '65 000 ₽'],
            3 => ['name' => '🏛️ Италия', 'desc' => 'Рим-Флоренция-Венеция, 10 ночей', 'price' => '89 000 ₽'],
            4 => ['name' => '🇬🇷 Греция', 'desc' => 'о.Крит, 7 ночей, 5*', 'price' => '78 000 ₽'],
            5 => ['name' => '🇪🇸 Испания', 'desc' => 'Барселона, 8 ночей, 4*', 'price' => '71 000 ₽'],
            6 => ['name' => '🇦🇪 ОАЭ', 'desc' => 'Дубай, 6 ночей, 5*', 'price' => '98 000 ₽']
        ];
        
        foreach($tours as $id => $tour) {
            echo "<div class=\"tour-card\">
                    <h3>{$tour['name']}</h3>
                    <p>{$tour['desc']}</p>
                    <div class=\"tour-price\">{$tour['price']}</div>";
            
            // ПРОВЕРКА АВТОРИЗАЦИИ - кнопка бронирования только для залогиненных
            if(isset($_SESSION["user_id"])){
                echo "<a href=\"/booking/{$id}\" class=\"btn btn-book\">Забронировать</a>";
            } else {
                echo "<a href=\"/login\" class=\"btn\">Войдите чтобы бронировать</a>";
            }
            echo "</div>";
        }
        
        echo "</div></div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div>
        </body></html>";
    }
}
?>