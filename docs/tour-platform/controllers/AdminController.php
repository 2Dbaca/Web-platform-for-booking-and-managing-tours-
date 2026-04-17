<?php
class AdminController {
    public function dashboard() {
        session_start();
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role']!=='admin'){ header('Location: /'); exit; }
        echo '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>Админ-панель</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea}
            .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:30px}
            .stat-card{background:white;padding:20px;border-radius:10px;text-align:center}
            .stat-number{font-size:36px;font-weight:bold;color:#667eea}
            .admin-menu{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
            .menu-card{background:white;padding:20px;border-radius:10px;text-align:center}
            .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px}
        </style>
        </head>
        <body>
        <div class="header"><h1>🌍 Админ-панель</h1></div>
        <div class="container">
        <div class="nav"><a href="/">Главная</a><a href="/tours">Туры</a><a href="/profile">Профиль</a><a href="/logout">Выйти</a></div>
        <h1>🔧 Панель управления</h1>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-number">6</div><p>Туров</p></div>
            <div class="stat-card"><div class="stat-number">0</div><p>Заказов</p></div>
            <div class="stat-card"><div class="stat-number">2</div><p>Пользователей</p></div>
        </div>
        <div class="admin-menu">
            <div class="menu-card"><h3>📋 Управление турами</h3><a href="/tours" class="btn">Управлять</a></div>
            <div class="menu-card"><h3>📦 Управление заказами</h3><a href="/tours" class="btn">Управлять</a></div>
        </div>
        </div>
        <div class="footer"><p>© 2026 ТурПлатформа</p></div>
        </body></html>';
    }
}
?>