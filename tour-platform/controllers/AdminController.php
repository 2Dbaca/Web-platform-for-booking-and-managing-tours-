<?php


class AdminController {
    
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Ошибка подключения будет обработана в методах
        }
    }
    
    // ==================== ДАШБОРД ====================
    public function dashboard() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM tours");
            $toursCount = $stmt->fetch()['total'];
            
            $stmt = $this->pdo->query("SELECT COUNT(*) as total, SUM(total_price) as revenue FROM orders");
            $ordersStats = $stmt->fetch();
            
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
            $usersCount = $stmt->fetch()['total'];
            
            $stmt = $this->pdo->query("SELECT COUNT(*) as pending FROM orders WHERE status = 'pending'");
            $pendingCount = $stmt->fetch()['pending'];
            
        } catch(PDOException $e) {
            $toursCount = 0;
            $ordersStats = ['total' => 0, 'revenue' => 0];
            $usersCount = 0;
            $pendingCount = 0;
        }
        
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Админ-панель</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:30px}
            .stat-card{background:white;padding:20px;border-radius:10px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .stat-number{font-size:36px;font-weight:bold;color:#667eea}
            .admin-menu{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
            .menu-card{background:white;padding:20px;border-radius:10px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin-top:10px}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px;color:#333}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 Админ-панель</h1></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/tours\">Туры</a>
            <a href=\"/admin/tours\">Управление турами</a>
            <a href=\"/admin/orders\">Управление заказами</a>
            <a href=\"/admin/reports\">Отчёты</a>
            <a href=\"/logout\">Выйти</a>
        </div>
        <h1>🔧 Панель управления</h1>
        <div class=\"stats-grid\">
            <div class=\"stat-card\"><div class=\"stat-number\">{$toursCount}</div><p>Туров</p></div>
            <div class=\"stat-card\"><div class=\"stat-number\">{$ordersStats['total']}</div><p>Заказов</p><p>{$ordersStats['revenue']} ₽</p></div>
            <div class=\"stat-card\"><div class=\"stat-number\">{$usersCount}</div><p>Пользователей</p></div>
            <div class=\"stat-card\"><div class=\"stat-number\">{$pendingCount}</div><p>В обработке</p></div>
        </div>
        <div class=\"admin-menu\">
            <div class=\"menu-card\"><h3>📋 Управление турами</h3><p>Добавление, редактирование, удаление туров</p><a href=\"/admin/tours\" class=\"btn\">Управлять</a></div>
            <div class=\"menu-card\"><h3>📦 Управление заказами</h3><p>Просмотр и изменение статусов заказов</p><a href=\"/admin/orders\" class=\"btn\">Управлять</a></div>
            <div class=\"menu-card\"><h3>📊 Отчёты и аналитика</h3><p>Статистика и выгрузка отчётов</p><a href=\"/admin/reports\" class=\"btn\">Смотреть</a></div>
        </div>
        </div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div>
        </body></html>";
    }
    
    // ==================== УПРАВЛЕНИЕ ТУРАМИ ====================
    public function manageTours() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        try {
            $stmt = $this->pdo->query("SELECT * FROM tours ORDER BY id ASC");
            $tours = $stmt->fetchAll();
        } catch(PDOException $e) {
            $tours = [];
        }
        
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Управление турами</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .btn-add{display:inline-block;padding:10px 20px;background:#48bb78;color:white;text-decoration:none;border-radius:5px}
            .btn-edit{display:inline-block;padding:5px 12px;background:#ffc107;color:#333;text-decoration:none;border-radius:5px;margin:2px}
            .btn-delete{display:inline-block;padding:5px 12px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;margin:2px}
            .tours-table{width:100%;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .tours-table th,.tours-table td{padding:12px;text-align:left;border-bottom:1px solid #eee}
            .tours-table th{background:#667eea;color:white}
            .tours-table tr:hover{background:#f5f5f5}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px;color:#333}
            .empty{text-align:center;padding:40px;background:white;border-radius:10px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 Админ-панель - Управление турами</h1></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/admin\">Панель</a>
            <a href=\"/admin/tours/add\" class=\"btn-add\">+ Добавить тур</a>
            <a href=\"/admin/orders\">Заказы</a>
            <a href=\"/admin/reports\">Отчёты</a>
            <a href=\"/logout\">Выйти</a>
        </div>
        <h1>📋 Список туров</h1>";
        
        if(empty($tours)) {
            echo "<div class='empty'>
                    <p>Нет добавленных туров</p>
                    <a href='/admin/tours/add' class='btn-add'>+ Добавить первый тур</a>
                  </div>";
        } else {
            echo "<table class='tours-table'>
                <thead>
                    <tr><th>ID</th><th>Название</th><th>Страна</th><th>Цена</th><th>Доступно</th><th>Даты</th><th>Действия</th></tr>
                </thead>
                <tbody>";
            
            foreach($tours as $tour) {
                $dateRange = date('d.m', strtotime($tour['start_date'])) . " - " . date('d.m', strtotime($tour['end_date']));
                echo "<tr>
                        <td>{$tour['id']}</td>
                        <td><strong>{$tour['name']}</strong></td>
                        <td>{$tour['country']}</td>
                        <td>{$tour['price']} ₽</td>
                        <td>{$tour['available_count']}</td>
                        <td>{$dateRange}</td>
                        <td>
                            <a href='/admin/tours/edit/{$tour['id']}' class='btn-edit'>✏️ Редактировать</a>
                            <a href='/admin/tours/delete/{$tour['id']}' class='btn-delete' onclick='return confirm(\"Удалить тур \\\"{$tour['name']}\\\"?\")'>🗑️ Удалить</a>
                        </td>
                      </tr>";
            }
            echo "</tbody></table></div>";
        }
        
        echo "</div>
        <div class='footer'><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div>
        </body></html>";
    }
    
    public function addTour() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"] ?? "";
            $country = $_POST["country"] ?? "";
            $price = (float)($_POST["price"] ?? 0);
            $available_count = (int)($_POST["available_count"] ?? 10);
            $start_date = $_POST["start_date"] ?? "";
            $end_date = $_POST["end_date"] ?? "";
            $description = $_POST["description"] ?? "";
            
            try {
                $stmt = $this->pdo->prepare("INSERT INTO tours (name, country, price, available_count, start_date, end_date, description) 
                                       VALUES (:name, :country, :price, :available_count, :start_date, :end_date, :description)");
                $stmt->execute([
                    ':name' => $name,
                    ':country' => $country,
                    ':price' => $price,
                    ':available_count' => $available_count,
                    ':start_date' => $start_date,
                    ':end_date' => $end_date,
                    ':description' => $description
                ]);
                $_SESSION["success"] = "Тур добавлен";
                header("Location: /admin/tours");
                exit;
            } catch(PDOException $e) {
                $_SESSION["error"] = "Ошибка добавления";
            }
        }
        
        $this->showTourForm(null, 'Добавить тур');
    }
    
    public function editTour($id) {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        try {
            if($_SERVER["REQUEST_METHOD"] === "POST") {
                $name = $_POST["name"] ?? "";
                $country = $_POST["country"] ?? "";
                $price = (float)($_POST["price"] ?? 0);
                $available_count = (int)($_POST["available_count"] ?? 10);
                $start_date = $_POST["start_date"] ?? "";
                $end_date = $_POST["end_date"] ?? "";
                $description = $_POST["description"] ?? "";
                
                $stmt = $this->pdo->prepare("UPDATE tours SET name = :name, country = :country, price = :price, 
                                       available_count = :available_count, start_date = :start_date, 
                                       end_date = :end_date, description = :description WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':name' => $name,
                    ':country' => $country,
                    ':price' => $price,
                    ':available_count' => $available_count,
                    ':start_date' => $start_date,
                    ':end_date' => $end_date,
                    ':description' => $description
                ]);
                $_SESSION["success"] = "Тур обновлён";
                header("Location: /admin/tours");
                exit;
            }
            
            $stmt = $this->pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $tour = $stmt->fetch();
            
            if(!$tour) {
                $_SESSION["error"] = "Тур не найден";
                header("Location: /admin/tours");
                exit;
            }
            
            $this->showTourForm($tour, 'Редактировать тур');
            
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка";
            header("Location: /admin/tours");
            exit;
        }
    }
    
    private function showTourForm($tour, $title) {
        $name = $tour ? $tour['name'] : '';
        $country = $tour ? $tour['country'] : '';
        $price = $tour ? $tour['price'] : '';
        $available_count = $tour ? $tour['available_count'] : 10;
        $start_date = $tour ? $tour['start_date'] : date('Y-m-d');
        $end_date = $tour ? $tour['end_date'] : date('Y-m-d', strtotime('+7 days'));
        $description = $tour ? $tour['description'] : '';
        $action = $tour ? "/admin/tours/edit/{$tour['id']}" : "/admin/tours/add";
        
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>$title</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:600px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .form-card{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
            .form-group{margin-bottom:15px}
            label{display:block;margin-bottom:5px;font-weight:bold}
            input,textarea,select{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px}
            .btn{width:100%;padding:12px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 Админ-панель</h1></div>
        <div class=\"container\">
        <div class=\"nav\"><a href=\"/admin/tours\">← Назад к списку</a></div>
        <div class=\"form-card\">
            <h1>$title</h1>
            <form method=\"POST\" action=\"$action\">
                <div class=\"form-group\"><label>Название тура</label><input type=\"text\" name=\"name\" value=\"$name\" required></div>
                <div class=\"form-group\"><label>Страна</label><input type=\"text\" name=\"country\" value=\"$country\" required></div>
                <div class=\"form-group\"><label>Цена (₽)</label><input type=\"number\" name=\"price\" value=\"$price\" required></div>
                <div class=\"form-group\"><label>Доступно мест</label><input type=\"number\" name=\"available_count\" value=\"$available_count\" required></div>
                <div class=\"form-group\"><label>Дата начала</label><input type=\"date\" name=\"start_date\" value=\"$start_date\" required></div>
                <div class=\"form-group\"><label>Дата окончания</label><input type=\"date\" name=\"end_date\" value=\"$end_date\" required></div>
                <div class=\"form-group\"><label>Описание</label><textarea name=\"description\" rows=\"4\">$description</textarea></div>
                <button type=\"submit\" class=\"btn\">Сохранить</button>
            </form>
        </div>
        </div>
        <div class=\"footer\"><p>© 2024 ТурПлатформа</p></div>
        </body></html>";
    }
    
    public function deleteTour($id) {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tours WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION["success"] = "Тур удалён";
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка удаления";
        }
        header("Location: /admin/tours");
        exit;
    }
    
    // ==================== УПРАВЛЕНИЕ ЗАКАЗАМИ ====================
    public function manageOrders() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        try {
            $stmt = $this->pdo->query("SELECT o.*, u.login as user_name, t.name as tour_name 
                                    FROM orders o 
                                    JOIN users u ON o.user_id = u.id 
                                    JOIN tours t ON o.tour_id = t.id 
                                    ORDER BY o.order_date DESC");
            $orders = $stmt->fetchAll();
        } catch(PDOException $e) {
            $orders = [];
        }
        
        echo "<!DOCTYPE html>
        <html>
        <head><meta charset=\"UTF-8\"><title>Управление заказами</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:Arial;background:#f0f2f5}
            .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
            .container{max-width:1200px;margin:0 auto;padding:20px}
            .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
            .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
            .orders-table{width:100%;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
            .orders-table th,.orders-table td{padding:12px;text-align:left;border-bottom:1px solid #eee}
            .orders-table th{background:#667eea;color:white}
            .orders-table tr:hover{background:#f5f5f5}
            select{padding:5px;border-radius:5px;border:1px solid #ddd}
            .btn{display:inline-block;padding:5px 15px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer}
            .status-pending{background:#ffc107;color:#333;padding:3px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .status-confirmed{background:#28a745;color:white;padding:3px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .status-cancelled{background:#dc3545;color:white;padding:3px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .status-completed{background:#17a2b8;color:white;padding:3px 10px;border-radius:20px;font-size:12px;display:inline-block}
            .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
            h1{text-align:center;margin-bottom:20px;color:#333}
            .empty{text-align:center;padding:50px;background:white;border-radius:10px}
            .search-box{margin-bottom:20px;text-align:right}
            .search-box input{padding:8px;border:1px solid #ddd;border-radius:5px;width:250px}
        </style>
        </head>
        <body>
        <div class=\"header\"><h1>🌍 Админ-панель - Управление заказами</h1></div>
        <div class=\"container\">
        <div class=\"nav\">
            <a href=\"/\">Главная</a>
            <a href=\"/admin\">Панель</a>
            <a href=\"/admin/tours\">Туры</a>
            <a href=\"/admin/reports\">Отчёты</a>
            <a href=\"/logout\">Выйти</a>
        </div>
        <h1>📦 Список заказов</h1>";
        
        if(empty($orders)) {
            echo "<div class='empty'>
                    <p>Нет заказов</p>
                    <a href='/tours' class='btn'>✈️ Перейти к турам</a>
                </div>";
        } else {
            echo "<div class='search-box'>
                    <input type='text' id='searchInput' placeholder='🔍 Поиск по туру или клиенту...' onkeyup='filterTable()'>
                </div>
                <table class='orders-table' id='ordersTable'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Тур</th>
                            <th>Клиент</th>
                            <th>Дата</th>
                            <th>Участников</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Действие</th>
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
                echo "<td><strong>" . htmlspecialchars($order['tour_name']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($order['user_name']) . "</td>";
                echo "<td>" . date('d.m.Y H:i', strtotime($order['order_date'])) . "</td>";
                echo "<td>" . htmlspecialchars($order['participants']) . "</td>";
                echo "<td>" . number_format($order['total_price'], 0, '', ' ') . " ₽</td>";
                echo "<td><span class='$statusClass'>$statusText</span></td>";
                echo "<td>
                        <form method='POST' action='/admin/order/{$order['id']}/status' style='display:flex;gap:5px;flex-wrap:wrap'>
                            <select name='status' style='padding:5px'>
                                <option value='pending' " . ($order['status'] == 'pending' ? 'selected' : '') . ">Ожидает</option>
                                <option value='confirmed' " . ($order['status'] == 'confirmed' ? 'selected' : '') . ">Подтверждён</option>
                                <option value='completed' " . ($order['status'] == 'completed' ? 'selected' : '') . ">Завершён</option>
                                <option value='cancelled' " . ($order['status'] == 'cancelled' ? 'selected' : '') . ">Отменён</option>
                            </select>
                            <button type='submit' class='btn'>Обновить</button>
                        </form>
                    </td>";
                echo "</tr>";
            }
            
            echo "</tbody>
                </table>
                <script>
                function filterTable() {
                    let input = document.getElementById('searchInput');
                    let filter = input.value.toLowerCase();
                    let table = document.getElementById('ordersTable');
                    let tr = table.getElementsByTagName('tr');
                    for (let i = 1; i < tr.length; i++) {
                        let td = tr[i].getElementsByTagName('td');
                        let found = false;
                        for (let j = 0; j < td.length; j++) {
                            if (td[j] && td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                                found = true;
                                break;
                            }
                        }
                        tr[i].style.display = found ? '' : 'none';
                    }
                }
                </script>";
        }
        
        echo "</div>
        <div class='footer'><p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p></div>
        </body></html>";
    }
    
    public function updateOrderStatus($id) {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        $status = $_POST["status"] ?? "";
        
        try {
            $stmt = $this->pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            $_SESSION["success"] = "Статус заказа обновлён";
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка обновления";
        }
        header("Location: /admin/orders");
        exit;
    }
    
    // ==================== ОТЧЁТЫ ====================
    public function reports() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        $reportType = $_GET['type'] ?? 'dashboard';
        
        // Отключаем проблемный режим SQL
        $this->pdo->exec("SET sql_mode = ''");
        
        try {
            // ========== ОБЩАЯ СТАТИСТИКА ==========
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM tours");
            $totalTours = $stmt->fetch()['total'];
            
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM orders");
            $totalOrders = $stmt->fetch()['total'];
            
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
            $totalUsers = $stmt->fetch()['total'];
            
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_price), 0) as revenue FROM orders WHERE status != 'cancelled'");
            $totalRevenue = $stmt->fetch()['revenue'];
            
            // ========== СТАТУСЫ ЗАКАЗОВ ==========
            $stmt = $this->pdo->query("SELECT status, COUNT(*) as count, COALESCE(SUM(total_price), 0) as amount FROM orders GROUP BY status");
            $statusRows = $stmt->fetchAll();
            
            // ========== ТУРЫ ПО СТРАНАМ ==========
            $stmt = $this->pdo->query("SELECT country, COUNT(*) as count FROM tours GROUP BY country");
            $countryRows = $stmt->fetchAll();
            
            // ========== ВСЕ ТУРЫ ==========
            $stmt = $this->pdo->query("SELECT * FROM tours ORDER BY id");
            $allTours = $stmt->fetchAll();
            
            // ========== ВСЕ ЗАКАЗЫ ==========
            $stmt = $this->pdo->query("SELECT o.*, u.login as user_name, t.name as tour_name 
                                       FROM orders o 
                                       JOIN users u ON o.user_id = u.id 
                                       JOIN tours t ON o.tour_id = t.id 
                                       ORDER BY o.order_date DESC");
            $allOrders = $stmt->fetchAll();
            
            // ========== ПОПУЛЯРНЫЕ ТУРЫ ==========
            $stmt = $this->pdo->query("SELECT t.name, t.country, COUNT(o.id) as bookings, COALESCE(SUM(o.total_price), 0) as revenue 
                                       FROM tours t 
                                       LEFT JOIN orders o ON t.id = o.tour_id 
                                       GROUP BY t.id, t.name, t.country 
                                       ORDER BY bookings DESC");
            $popularTours = $stmt->fetchAll();
            
            // ========== ЗАКАЗЫ ПО МЕСЯЦАМ ==========
            $stmt = $this->pdo->query("SELECT 
                                        DATE_FORMAT(order_date, '%M %Y') as month, 
                                        COUNT(*) as count, 
                                        COALESCE(SUM(total_price), 0) as revenue 
                                      FROM orders 
                                      GROUP BY DATE_FORMAT(order_date, '%Y-%m'), DATE_FORMAT(order_date, '%M %Y')
                                      ORDER BY MIN(order_date) DESC");
            $ordersByMonth = $stmt->fetchAll();
            
            // ========== ТУРЫ С НИЗКИМ КОЛИЧЕСТВОМ МЕСТ ==========
            $stmt = $this->pdo->query("SELECT * FROM tours WHERE available_count <= 5 ORDER BY available_count ASC");
            $lowStockTours = $stmt->fetchAll();
            
        } catch(PDOException $e) {
            $totalTours = 0;
            $totalOrders = 0;
            $totalUsers = 0;
            $totalRevenue = 0;
            $statusRows = [];
            $countryRows = [];
            $allTours = [];
            $allOrders = [];
            $popularTours = [];
            $ordersByMonth = [];
            $lowStockTours = [];
        }
        
        $statusNames = [
            'pending' => '⏳ Ожидает',
            'confirmed' => '✅ Подтверждён',
            'cancelled' => '❌ Отменён',
            'completed' => '✔️ Завершён'
        ];
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Отчёты - Админ-панель</title>
            <style>
                *{margin:0;padding:0;box-sizing:border-box}
                body{font-family:Arial;background:#f0f2f5;padding:20px}
                .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center;margin-bottom:20px;border-radius:10px}
                .container{max-width:1400px;margin:0 auto}
                .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
                .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
                .tabs{display:flex;gap:5px;margin-bottom:20px;flex-wrap:wrap}
                .tab{background:#e0e0e0;padding:10px 20px;border-radius:10px;text-decoration:none;color:#333;font-weight:bold}
                .tab-active{background:#667eea;color:white}
                .btn{display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;margin:5px;cursor:pointer}
                .btn-green{background:#48bb78}
                .btn-green:hover{background:#38a169}
                .btn-orange{background:#ed8936}
                .btn-orange:hover{background:#dd7a2b}
                .export-buttons{text-align:right;margin-bottom:20px;padding:10px;background:white;border-radius:10px}
                .report-content{background:white;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
                .stats{display:flex;gap:20px;margin-bottom:20px;flex-wrap:wrap}
                .stat{background:#667eea;color:white;padding:20px;border-radius:10px;flex:1;min-width:150px;text-align:center}
                .stat-number{font-size:32px;font-weight:bold}
                .table-wrapper{overflow-x:auto;margin-top:20px}
                table{width:100%;border-collapse:collapse}
                th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd}
                th{background:#667eea;color:white}
                tr:hover{background:#f5f5f5}
                .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px;border-radius:10px}
                h2{color:#333;margin:20px 0 10px 0}
            </style>
        </head>
        <body>
        <div class=\"container\">
            <div class=\"header\">
                <h1>🌍 Админ-панель - Отчёты и аналитика</h1>
            </div>
            
            <div class=\"nav\">
                <a href=\"/\">Главная</a>
                <a href=\"/admin\">Панель</a>
                <a href=\"/admin/tours\">Туры</a>
                <a href=\"/admin/orders\">Заказы</a>
                <a href=\"/logout\">Выйти</a>
            </div>
            
            <div class=\"tabs\">
                <a href=\"?type=dashboard\" class=\"tab " . ($reportType == 'dashboard' ? 'tab-active' : '') . "\">📊 Дашборд</a>
                <a href=\"?type=tours\" class=\"tab " . ($reportType == 'tours' ? 'tab-active' : '') . "\">🏖️ Туры</a>
                <a href=\"?type=orders\" class=\"tab " . ($reportType == 'orders' ? 'tab-active' : '') . "\">📦 Заказы</a>
                <a href=\"?type=popular\" class=\"tab " . ($reportType == 'popular' ? 'tab-active' : '') . "\">🔥 Популярное</a>
                <a href=\"?type=monthly\" class=\"tab " . ($reportType == 'monthly' ? 'tab-active' : '') . "\">📅 По месяцам</a>
                <a href=\"?type=stock\" class=\"tab " . ($reportType == 'stock' ? 'tab-active' : '') . "\">⚠️ Остатки</a>
            </div>
            
            <div class=\"export-buttons\">
                <a href=\"/admin/export/excel?type={$reportType}\" class=\"btn btn-green\">📊 Экспорт в Excel (XLS)</a>
                <a href=\"/admin/export/word?type={$reportType}\" class=\"btn btn-orange\">📄 Экспорт в Word (DOC)</a>
            </div>
            
            <div class=\"report-content\">";
        
        // ========== ДАШБОРД ==========
        if($reportType == 'dashboard') {
            echo "
            <div class='stats'>
                <div class='stat'><div class='stat-number'>{$totalTours}</div><div>Туров</div></div>
                <div class='stat'><div class='stat-number'>{$totalOrders}</div><div>Заказов</div></div>
                <div class='stat'><div class='stat-number'>{$totalUsers}</div><div>Пользователей</div></div>
                <div class='stat'><div class='stat-number'>" . number_format($totalRevenue, 0, '', ' ') . " ₽</div><div>Выручка</div></div>
            </div>
            
            <h2>📊 Статусы заказов</h2>
            <div class='table-wrapper'>
                <table>
                    <thead><tr><th>Статус</th><th>Количество</th><th>Сумма</th></tr></thead>
                    <tbody>";
            foreach($statusRows as $row) {
                $statusName = $statusNames[$row['status']] ?? $row['status'];
                echo "<tr>
                        <td>{$statusName}</td>
                        <td>{$row['count']}</td>
                        <td>" . number_format($row['amount'], 0, '', ' ') . " ₽
                      </tr>";
            }
            echo "</tbody></table></div>
            
            <h2>📍 Туры по странам</h2>
            <div class='table-wrapper'>
                <table><thead><tr><th>Страна</th><th>Количество туров</th></tr></thead><tbody>";
            foreach($countryRows as $row) {
                echo "<tr><td>{$row['country']}</td><td>{$row['count']}</td></tr>";
            }
            echo "</tbody></table></div>";
        }
        
        // ========== ТУРЫ ==========
        elseif($reportType == 'tours') {
            echo "<h2>🏖️ Список всех туров</h2>
            <div class='table-wrapper'>
                <table>
                    <thead><tr><th>ID</th><th>Название</th><th>Страна</th><th>Цена</th><th>Доступно</th><th>Даты</th></tr></thead>
                    <tbody>";
            foreach($allTours as $tour) {
                $dates = date('d.m', strtotime($tour['start_date'])) . " - " . date('d.m', strtotime($tour['end_date']));
                echo "<tr>
                        <td>{$tour['id']}</td>
                        <td><strong>{$tour['name']}</strong></td>
                        <td>{$tour['country']}</td>
                        <td>" . number_format($tour['price'], 0, '', ' ') . " ₽</td>
                        <td>{$tour['available_count']}</td>
                        <td>{$dates}</td>
                      </tr>";
            }
            echo "</tbody></table></div>";
        }
        
        // ========== ЗАКАЗЫ ==========
        elseif($reportType == 'orders') {
            echo "<h2>📋 Все заказы</h2>
            <div class='table-wrapper'>
                <table>
                    <thead><tr><th>ID</th><th>Тур</th><th>Клиент</th><th>Дата</th><th>Участников</th><th>Сумма</th><th>Статус</th></tr></thead>
                    <tbody>";
            foreach($allOrders as $order) {
                $statusName = $statusNames[$order['status']] ?? $order['status'];
                echo "<tr>
                        <td>{$order['id']}</td>
                        <td>{$order['tour_name']}</td>
                        <td>{$order['user_name']}</td>
                        <td>" . date('d.m.Y H:i', strtotime($order['order_date'])) . "</td>
                        <td>{$order['participants']}</td>
                        <td>" . number_format($order['total_price'], 0, '', ' ') . " ₽</td>
                        <td>{$statusName}</td>
                      </tr>";
            }
            echo "</tbody></table></div>";
        }
        
        // ========== ПОПУЛЯРНЫЕ ТУРЫ ==========
        elseif($reportType == 'popular') {
            echo "<h2>🔥 Рейтинг популярности туров</h2>
            <div class='table-wrapper'>
                <table>
                    <thead><tr><th>#</th><th>Тур</th><th>Страна</th><th>Бронирований</th><th>Выручка</th></tr></thead>
                    <tbody>";
            $i = 1;
            foreach($popularTours as $tour) {
                echo "<tr>
                        <td><strong>{$i}</strong></td>
                        <td>{$tour['name']}</td>
                        <td>{$tour['country']}</td>
                        <td>{$tour['bookings']}</td>
                        <td>" . number_format($tour['revenue'], 0, '', ' ') . " ₽
                      </tr>";
                $i++;
            }
            echo "</tbody></table></div>";
        }
        
        // ========== ПО МЕСЯЦАМ ==========
        elseif($reportType == 'monthly') {
            echo "<h2>📅 Ежемесячная статистика</h2>
            <div class='table-wrapper'>
                <table>
                    <thead><tr><th>Месяц</th><th>Количество заказов</th><th>Выручка</th></tr></thead>
                    <tbody>";
            foreach($ordersByMonth as $month) {
                echo "<tr>
                        <td><strong>{$month['month']}</strong></td>
                        <td>{$month['count']}</td>
                        <td>" . number_format($month['revenue'], 0, '', ' ') . " ₽
                      </tr>";
            }
            echo "</tbody></table></div>";
        }
        
        // ========== ОСТАТКИ ==========
        elseif($reportType == 'stock') {
            echo "<h2>⚠️ Туры с низким количеством мест (≤5)</h2>
            <div class='table-wrapper'>
                <table>
                    <thead><tr><th>Тур</th><th>Страна</th><th>Цена</th><th>Доступно мест</th></tr></thead>
                    <tbody>";
            foreach($lowStockTours as $tour) {
                $warning = $tour['available_count'] <= 2 ? '🔴 Критически мало' : '🟡 Мало';
                echo "<tr>
                        <td>{$tour['name']}</td>
                        <td>{$tour['country']}</td>
                        <td>" . number_format($tour['price'], 0, '', ' ') . " ₽</td>
                        <td><strong>{$tour['available_count']}</strong> {$warning}</td>
                      </tr>";
            }
            echo "</tbody><td></div>";
        }
        
        echo "
            </div>
            <div class='footer'>
                <p>© 2024 ТурПлатформа | Разработано: Кучеев Е.С., Гончар К.А. (ПИб-242)</p>
            </div>
        </div>
        </body>
        </html>";
    }
    
    // ==================== ЭКСПОРТ ОТЧЁТОВ ====================
    
    public function exportExcel() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        $type = $_GET['type'] ?? 'dashboard';
        $this->exportSelectedReport($type, 'excel');
    }
    
    public function exportWord() {
        if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
            header("Location: /"); 
            exit; 
        }
        
        $type = $_GET['type'] ?? 'dashboard';
        $this->exportSelectedReport($type, 'word');
    }
    
    public function exportSelectedReport($reportType, $format) {
    if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") { 
        header("Location: /"); 
        exit; 
    }
    
    // Отключаем проблемный режим SQL
    $this->pdo->exec("SET sql_mode = ''");
    
        try {
            $data = [];
            $title = "";
            
            switch($reportType) {
                case 'tours':
                    // Убираем description, чтобы избежать дублирования
                    $stmt = $this->pdo->query("SELECT id, name, country, price, available_count, DATE_FORMAT(start_date, '%d.%m.%Y') as start_date, DATE_FORMAT(end_date, '%d.%m.%Y') as end_date FROM tours ORDER BY id");
                    $data = $stmt->fetchAll();
                    $title = "Список туров";
                    break;
                    
                case 'orders':
                    $stmt = $this->pdo->query("SELECT o.id, DATE_FORMAT(o.order_date, '%d.%m.%Y %H:%i') as order_date, o.status, o.total_price, o.participants,
                                                u.login as user_login, u.email, u.full_name,
                                                t.name as tour_name, t.country
                                        FROM orders o
                                        JOIN users u ON o.user_id = u.id
                                        JOIN tours t ON o.tour_id = t.id
                                        ORDER BY o.order_date DESC");
                    $data = $stmt->fetchAll();
                    $title = "Список заказов";
                    break;
                    
                case 'popular':
                    $stmt = $this->pdo->query("SELECT t.name, t.country, COUNT(o.id) as bookings, COALESCE(SUM(o.total_price), 0) as revenue
                                            FROM tours t
                                            LEFT JOIN orders o ON t.id = o.tour_id
                                            GROUP BY t.id, t.name, t.country
                                            ORDER BY bookings DESC");
                    $data = $stmt->fetchAll();
                    $title = "Популярные туры";
                    break;
                    
                case 'monthly':
                    $stmt = $this->pdo->query("SELECT DATE_FORMAT(order_date, '%M %Y') as month, 
                                                    COUNT(*) as count, 
                                                    COALESCE(SUM(total_price), 0) as revenue
                                            FROM orders 
                                            GROUP BY DATE_FORMAT(order_date, '%Y-%m'), DATE_FORMAT(order_date, '%M %Y')
                                            ORDER BY MIN(order_date) DESC");
                    $data = $stmt->fetchAll();
                    $title = "Ежемесячная статистика";
                    break;
                    
                case 'stock':
                    $stmt = $this->pdo->query("SELECT id, name, country, price, available_count FROM tours WHERE available_count <= 5 ORDER BY available_count ASC");
                    $data = $stmt->fetchAll();
                    $title = "Туры с низким количеством мест";
                    break;
                    
                case 'dashboard':
                default:
                    $stmt = $this->pdo->query("SELECT 
                                                (SELECT COUNT(*) FROM tours) as total_tours,
                                                (SELECT COUNT(*) FROM orders) as total_orders,
                                                (SELECT COUNT(*) FROM users) as total_users,
                                                (SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE status != 'cancelled') as total_revenue
                                            ");
                    $data = $stmt->fetchAll();
                    $title = "Общая статистика";
                    break;
            }
            
            if($format == 'excel') {
                $this->exportToExcel($data, $title, $reportType);
            } else {
                $this->exportToWord($data, $title, $reportType);
            }
            
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка экспорта: " . $e->getMessage();
            header("Location: /admin/reports?type=" . $reportType);
            exit;
        }
    }
    
    private function exportToExcel($data, $title, $reportType) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $reportType . '_' . date('Y-m-d') . '.xls"');
        echo "\xEF\xBB\xBF";
        
        echo "<html><head><meta charset='UTF-8'></head><body>";
        echo "<h1 style='color:#667eea'>🌍 ТурПлатформа - {$title}</h1>";
        echo "<p><strong>Дата формирования:</strong> " . date('d.m.Y H:i:s') . "</p>";
        echo "<p><strong>Автор:</strong> " . htmlspecialchars($_SESSION["user_login"]) . "</p>";
        echo "<hr>";
        
        if(empty($data)) {
            echo "<p>Нет данных для отображения</p>";
        } else {
            echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
            
            // Заголовки - создаём вручную, чтобы не было дублей
            echo "<tr bgcolor='#667eea' style='color:white'>";
            
            if($reportType == 'tours') {
                echo "<th>ID</th>";
                echo "<th>Название</th>";
                echo "<th>Страна</th>";
                echo "<th>Цена</th>";
                echo "<th>Доступно</th>";
                echo "<th>Дата начала</th>";
                echo "<th>Дата окончания</th>";
            } elseif($reportType == 'orders') {
                echo "<th>ID</th>";
                echo "<th>Тур</th>";
                echo "<th>Страна</th>";
                echo "<th>Клиент</th>";
                echo "<th>Email</th>";
                echo "<th>Дата заказа</th>";
                echo "<th>Статус</th>";
                echo "<th>Участников</th>";
                echo "<th>Сумма</th>";
            } elseif($reportType == 'popular') {
                echo "<th>#</th>";
                echo "<th>Тур</th>";
                echo "<th>Страна</th>";
                echo "<th>Бронирований</th>";
                echo "<th>Выручка</th>";
            } elseif($reportType == 'monthly') {
                echo "<th>Месяц</th>";
                echo "<th>Количество заказов</th>";
                echo "<th>Выручка</th>";
            } elseif($reportType == 'stock') {
                echo "<th>ID</th>";
                echo "<th>Название</th>";
                echo "<th>Страна</th>";
                echo "<th>Цена</th>";
                echo "<th>Доступно</th>";
            } else {
                // dashboard
                echo "<th>Показатель</th>";
                echo "<th>Значение</th>";
            }
            echo "</tr>";
            
            // Данные
            if($reportType == 'tours') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . number_format($row['price'], 0, '', ' ') . " ₽</td>";
                    echo "<td>" . htmlspecialchars($row['available_count']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'orders') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tour_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['full_name'] ?: $row['user_login']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['participants']) . "</td>";
                    echo "<td>" . number_format($row['total_price'], 0, '', ' ') . " ₽</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'popular') {
                $i = 1;
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . $i++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['bookings']) . "</td>";
                    echo "<td>" . number_format($row['revenue'], 0, '', ' ') . " ₽</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'monthly') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['count']) . "</td>";
                    echo "<td>" . number_format($row['revenue'], 0, '', ' ') . " ₽</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'stock') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . number_format($row['price'], 0, '', ' ') . " ₽</td>";
                    echo "<td>" . htmlspecialchars($row['available_count']) . "</td>";
                    echo "</tr>";
                }
            } else {
                // dashboard
                $firstRow = (array)$data[0];
                foreach($firstRow as $key => $value) {
                    $label = $this->getColumnName($key);
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($label) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
        }
        
        echo "<hr>";
        echo "<p style='margin-top:50px'>© ТурПлатформа, " . date('Y') . "</p>";
        echo "</body></html>";
        exit;
    }
    
    private function exportToWord($data, $title, $reportType) {
        header('Content-Type: application/msword');
        header('Content-Disposition: attachment; filename="' . $reportType . '_' . date('Y-m-d') . '.doc"');
        
        echo "<html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #667eea; text-align: center; }
                table { border-collapse: collapse; width: 100%; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #667eea; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .header { margin-bottom: 20px; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; }
            </style>
        </head>
        <body>";
        
        echo "<div class='header'>";
        echo "<h1>🌍 ТурПлатформа - {$title}</h1>";
        echo "<p><strong>Дата формирования:</strong> " . date('d.m.Y H:i:s') . "</p>";
        echo "<p><strong>Автор:</strong> " . htmlspecialchars($_SESSION["user_login"]) . "</p>";
        echo "<hr>";
        echo "</div>";
        
        if(empty($data)) {
            echo "<p>Нет данных для отображения</p>";
        } else {
            echo "<table>";
            
            // Заголовки
            echo "<tr>";
            
            if($reportType == 'tours') {
                echo "<th>ID</th>";
                echo "<th>Название</th>";
                echo "<th>Страна</th>";
                echo "<th>Цена</th>";
                echo "<th>Доступно</th>";
                echo "<th>Дата начала</th>";
                echo "<th>Дата окончания</th>";
            } elseif($reportType == 'orders') {
                echo "<th>ID</th>";
                echo "<th>Тур</th>";
                echo "<th>Страна</th>";
                echo "<th>Клиент</th>";
                echo "<th>Email</th>";
                echo "<th>Дата заказа</th>";
                echo "<th>Статус</th>";
                echo "<th>Участников</th>";
                echo "<th>Сумма</th>";
            } elseif($reportType == 'popular') {
                echo "<th>#</th>";
                echo "<th>Тур</th>";
                echo "<th>Страна</th>";
                echo "<th>Бронирований</th>";
                echo "<th>Выручка</th>";
            } elseif($reportType == 'monthly') {
                echo "<th>Месяц</th>";
                echo "<th>Количество заказов</th>";
                echo "<th>Выручка</th>";
            } elseif($reportType == 'stock') {
                echo "<th>ID</th>";
                echo "<th>Название</th>";
                echo "<th>Страна</th>";
                echo "<th>Цена</th>";
                echo "<th>Доступно</th>";
            } else {
                echo "<th>Показатель</th>";
                echo "<th>Значение</th>";
            }
            echo "</tr>";
            
            // Данные
            if($reportType == 'tours') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . number_format($row['price'], 0, '', ' ') . " ₽</td>";
                    echo "<td>" . htmlspecialchars($row['available_count']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'orders') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tour_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['full_name'] ?: $row['user_login']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['participants']) . "</td>";
                    echo "<td>" . number_format($row['total_price'], 0, '', ' ') . " ₽</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'popular') {
                $i = 1;
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . $i++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['bookings']) . "</td>";
                    echo "<td>" . number_format($row['revenue'], 0, '', ' ') . " ₽</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'monthly') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['count']) . "</td>";
                    echo "<td>" . number_format($row['revenue'], 0, '', ' ') . " ₽</td>";
                    echo "</tr>";
                }
            } elseif($reportType == 'stock') {
                foreach($data as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . number_format($row['price'], 0, '', ' ') . " ₽</td>";
                    echo "<td>" . htmlspecialchars($row['available_count']) . "</td>";
                    echo "</tr>";
                }
            } else {
                // dashboard
                $firstRow = (array)$data[0];
                foreach($firstRow as $key => $value) {
                    $label = $this->getColumnName($key);
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($label) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
        }
        
        echo "<div class='footer'>";
        echo "<hr>";
        echo "<p>© ТурПлатформа, " . date('Y') . "</p>";
        echo "</div>";
        
        echo "</body></html>";
        exit;
    }
    
    private function getColumnName($column) {
        $names = [
            'id' => 'ID',
            'name' => 'Название',
            'country' => 'Страна',
            'price' => 'Цена',
            'available_count' => 'Доступно',
            'start_date' => 'Дата начала',
            'end_date' => 'Дата окончания',
            'description' => 'Описание',
            'order_date' => 'Дата заказа',
            'status' => 'Статус',
            'total_price' => 'Сумма',
            'participants' => 'Участников',
            'user_login' => 'Логин клиента',
            'email' => 'Email',
            'full_name' => 'ФИО',
            'tour_name' => 'Тур',
            'role' => 'Роль',
            'phone' => 'Телефон',
            'created_at' => 'Дата регистрации',
            'bookings' => 'Бронирований',
            'revenue' => 'Выручка',
            'month' => 'Месяц',
            'count' => 'Количество',
            'total_tours' => 'Всего туров',
            'total_orders' => 'Всего заказов',
            'total_users' => 'Всего пользователей',
            'total_revenue' => 'Общая выручка'
        ];
        return $names[$column] ?? $column;
    }
}
?>