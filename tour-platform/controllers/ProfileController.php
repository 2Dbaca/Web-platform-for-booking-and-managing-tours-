<?php
// controllers/ProfileController.php

class ProfileController {
    
    public function index() {
        if(!isset($_SESSION["user_id"])) { 
            header("Location: /login"); 
            exit; 
        }
        
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Личный кабинет</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:'Segoe UI', Arial, sans-serif;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .profile-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:20px}
            .info-row{display:flex;padding:10px 0;border-bottom:1px solid #eee}
            .info-label{width:150px;font-weight:bold;color:#555}
            .info-value{flex:1}
            .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px;color:#333}
            .success{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:15px}
            .error{background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin-bottom:15px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/tours\">Туры</a>
            <a href=\"/profile\">Профиль</a>
            <a href=\"/profile/orders\">Мои заказы</a>";
            if(isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") {
                echo "<a href=\"/admin\">Админ-панель</a>";
            }
            echo "<a href=\"/logout\">Выйти</a>
        </div>";
        
        if(isset($_SESSION["success"])) {
            echo "<div class='success'>" . $_SESSION["success"] . "</div>";
            unset($_SESSION["success"]);
        }
        if(isset($_SESSION["error"])) {
            echo "<div class='error'>" . $_SESSION["error"] . "</div>";
            unset($_SESSION["error"]);
        }
        
        echo "<div class='profile-card'>
            <h1>👤 Личный кабинет</h1>
            <div class='info-row'><div class='info-label'>Логин:</div><div class='info-value'>" . htmlspecialchars($_SESSION["user_login"]) . "</div></div>
            <div class='info-row'><div class='info-label'>Имя:</div><div class='info-value'>" . htmlspecialchars($_SESSION["user_name"]) . "</div></div>
            <div class='info-row'><div class='info-label'>Роль:</div><div class='info-value'>" . htmlspecialchars($_SESSION["user_role"]) . "</div></div>
            <a href='/profile/orders' class='btn'>📋 Мои заказы</a>
            <a href='/tours' class='btn'>✈️ Поиск туров</a>
        </div>
        </div>
        <div class='footer'><p>© 2024 ТурПлатформа</p></div>
        </body></html>";
    }
    
    public function orders() {
        if(!isset($_SESSION["user_id"])) { 
            header("Location: /login"); 
            exit; 
        }
        
        $userId = $_SESSION["user_id"];
        
        try {
            $pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET NAMES utf8mb4");
            
            $stmt = $pdo->prepare("SELECT o.*, t.name as tour_name, t.country, t.start_date, t.end_date 
                                   FROM orders o 
                                   JOIN tours t ON o.tour_id = t.id 
                                   WHERE o.user_id = :user_id 
                                   ORDER BY o.order_date DESC");
            $stmt->execute([':user_id' => $userId]);
            $orders = $stmt->fetchAll();
            
        } catch(PDOException $e) {
            $orders = [];
        }
        
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Мои заказы</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:'Segoe UI', Arial, sans-serif;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .orders-table{width:100%;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
            .orders-table th,.orders-table td{padding:15px;text-align:left;border-bottom:1px solid #eee}
            .orders-table th{background:#667eea;color:white}
            .orders-table tr:hover{background:#f5f5f5}
            .status-pending{background:#ffc107;color:#333;padding:5px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .status-confirmed{background:#28a745;color:white;padding:5px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .status-cancelled{background:#dc3545;color:white;padding:5px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .status-completed{background:#17a2b8;color:white;padding:5px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .btn-cancel{background:#dc3545;color:white;padding:5px 12px;border-radius:5px;text-decoration:none;font-size:12px}
            .btn-cancel:hover{background:#c82333}
            .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px;color:#333}
            .empty{text-align:center;padding:50px;background:white;border-radius:10px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/tours\">Туры</a>
            <a href=\"/profile\">Профиль</a>
            <a href=\"/profile/orders\">Мои заказы</a>
            <a href=\"/logout\">Выйти</a>
        </div>
        <h1>📋 Мои заказы</h1>";
        
        if(empty($orders)) {
            echo "<div class='empty'>
                    <p>📭 У вас пока нет заказов</p>
                    <a href='/tours' class='btn'>✈️ Выбрать тур</a>
                  </div>";
        } else {
            echo "<table class='orders-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Тур</th>
                            <th>Страна</th>
                            <th>Дата</th>
                            <th>Участников</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach($orders as $order) {
                $statusClass = "status-{$order['status']}";
                $statusText = [
                    'pending' => 'Ожидает',
                    'confirmed' => 'Подтверждён',
                    'cancelled' => 'Отменён',
                    'completed' => 'Завершён'
                ][$order['status']] ?? $order['status'];
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($order['id']) . "</td>";
                echo "<td>" . htmlspecialchars($order['tour_name']) . "</td>";
                echo "<td>" . htmlspecialchars($order['country']) . "</td>";
                echo "<td>" . date('d.m.Y', strtotime($order['order_date'])) . "</td>";
                echo "<td>" . htmlspecialchars($order['participants']) . "</td>";
                echo "<td>" . number_format($order['total_price'], 0, '', ' ') . " ₽</td>";
                echo "<td><span class='$statusClass'>$statusText</span></td>";
                echo "<td>";
                if($order['status'] === 'pending') {
                    echo "<a href='/order/{$order['id']}/cancel' class='btn-cancel' onclick='return confirm(\"Отменить бронирование?\")'>Отменить</a>";
                } else {
                    echo "—";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>
            </table>";
        }
        
        echo "</div>
        <div class='footer'><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div>
        </body></html>";
    }
    
    public function cancelOrder($orderId) {
        if(!isset($_SESSION["user_id"])) { 
            header("Location: /login"); 
            exit; 
        }
        
        try {
            $pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $orderId, ':user_id' => $_SESSION["user_id"]]);
            $order = $stmt->fetch();
            
            if(!$order) {
                $_SESSION["error"] = "Заказ не найден";
                header("Location: /profile/orders");
                exit;
            }
            
            if($order['status'] !== 'pending') {
                $_SESSION["error"] = "Нельзя отменить этот заказ";
                header("Location: /profile/orders");
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE tours SET available_count = available_count + :participants WHERE id = :tour_id");
            $stmt->execute([
                ':participants' => $order['participants'],
                ':tour_id' => $order['tour_id']
            ]);
            
            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = :id");
            $stmt->execute([':id' => $orderId]);
            
            $_SESSION["success"] = "Заказ успешно отменён";
            header("Location: /profile/orders");
            exit;
            
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка отмены заказа";
            header("Location: /profile/orders");
            exit;
        }
    }
}
?>