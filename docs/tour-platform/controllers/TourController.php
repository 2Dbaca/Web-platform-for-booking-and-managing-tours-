<?php
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
            .nav a{margin:0 15px;text-decoration:none;color:#667eea}
            .tours-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:20px}
            .tour-card{background:white;border-radius:10px;padding:20px;text-align:center}
            .tour-price{color:#48bb78;font-size:24px;font-weight:bold;margin:10px 0}
            .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\"><a href=\"/\">Главная</a><a href=\"/tours\">Туры</a>";
        if(isset($_SESSION["user_id"])){echo "<a href=\"/profile\">Профиль</a><a href=\"/logout\">Выйти</a>";}
        else{echo "<a href=\"/login\">Вход</a><a href=\"/register\">Регистрация</a>";}
        echo "</div><h1>✈️ Доступные туры</h1>
        <div class=\"tours-grid\">
            <div class=\"tour-card\"><h3>🏖️ Турция</h3><p>Анталья, 7 ночей</p><div class=\"tour-price\">45 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🗼 Париж</h3><p>Франция, 5 ночей</p><div class=\"tour-price\">65 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🏛️ Италия</h3><p>10 ночей</p><div class=\"tour-price\">89 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🇬🇷 Греция</h3><p>о.Крит, 7 ночей</p><div class=\"tour-price\">78 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🇪🇸 Испания</h3><p>Барселона, 8 ночей</p><div class=\"tour-price\">71 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
            <div class=\"tour-card\"><h3>🇦🇪 ОАЭ</h3><p>Дубай, 6 ночей</p><div class=\"tour-price\">98 000 ₽</div><a href=\"/login\" class=\"btn\">Забронировать</a></div>
        </div></div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа</p></div>
        </body></html>";
    }
    public function details($params) { echo "<h1>Детали тура #".($params["id"]??1)."</h1><a href=\"/tours\">Назад</a>"; }
}
?>