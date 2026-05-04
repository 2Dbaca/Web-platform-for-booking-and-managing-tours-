<?php
// controllers/BookingController.php

class BookingController {
    
    // Страница бронирования (GET)
    public function showBookingForm($tourId) {
        if(!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }
        
        try {
            $pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute([':id' => $tourId]);
            $tour = $stmt->fetch();
            
            if(!$tour) {
                $_SESSION["error"] = "Тур не найден";
                header("Location: /tours");
                exit;
            }
            
            echo "<!DOCTYPE html>
            <html>
            <head><meta charset=\"UTF-8\"><title>Бронирование тура</title>
            <style>
                *{margin:0;padding:0;box-sizing:border-box}
                body{font-family:Arial;background:#f0f2f5}
                .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;text-align:center}
                .container{max-width:600px;margin:0 auto;padding:20px}
                .nav{background:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center}
                .nav a{margin:0 15px;text-decoration:none;color:#667eea;font-weight:bold}
                .booking-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
                .tour-info{background:#f8f9fa;padding:15px;border-radius:8px;margin-bottom:20px}
                .tour-info h2{color:#333;margin-bottom:10px}
                .tour-price{font-size:28px;color:#48bb78;font-weight:bold}
                .form-group{margin-bottom:20px}
                label{display:block;margin-bottom:8px;font-weight:bold;color:#333}
                input,select{width:100%;padding:12px;border:1px solid #ddd;border-radius:5px;font-size:16px}
                input:focus,select:focus{outline:none;border-color:#667eea}
                .btn{width:100%;padding:14px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer;font-size:16px;font-weight:bold}
                .btn:hover{background:#5a67d8}
                .btn-cancel{background:#6c757d;margin-top:10px;display:block;text-align:center;text-decoration:none}
                .btn-cancel:hover{background:#5a6268}
                .total-price{background:#e8f4f8;padding:15px;border-radius:8px;margin-top:20px;text-align:center}
                .total-price span{font-size:24px;color:#667eea;font-weight:bold}
                .footer{background:#333;color:white;text-align:center;padding:20px;margin-top:40px}
                h1{text-align:center;margin-bottom:20px;color:#333}
            </style>
            <script>
                function updateTotal() {
                    let participants = document.getElementById('participants').value;
                    let price = " . $tour['price'] . ";
                    let total = participants * price;
                    document.getElementById('total').innerHTML = total.toLocaleString() + ' ₽';
                }
            </script>
            </head>
            <body>
            <div class=\"header\"><h1>🌍 ТурПлатформа</h1></div>
            <div class=\"container\">
            <div class=\"nav\">
                <a href=\"/\">Главная</a>
                <a href=\"/tours\">Туры</a>
                <a href=\"/profile\">Профиль</a>
                <a href=\"/logout\">Выйти</a>
            </div>
            <div class=\"booking-card\">
                <h1>📝 Оформление бронирования</h1>
                <div class=\"tour-info\">
                    <h2>{$tour['name']}</h2>
                    <p>📍 {$tour['country']}</p>
                    <p>📅 " . date('d.m.Y', strtotime($tour['start_date'])) . " - " . date('d.m.Y', strtotime($tour['end_date'])) . "</p>
                    <div class=\"tour-price\">{$tour['price']} ₽ за человека</div>
                    <p>✅ Доступно мест: {$tour['available_count']}</p>
                </div>
                
                <form method=\"POST\" action=\"/booking/{$tourId}/confirm\">
                    <div class=\"form-group\">
                        <label>Количество участников</label>
                        <input type=\"number\" id=\"participants\" name=\"participants\" min=\"1\" max=\"{$tour['available_count']}\" value=\"1\" onchange=\"updateTotal()\" required>
                    </div>
                    <div class=\"form-group\">
                        <label>Комментарий к заказу</label>
                        <textarea name=\"comment\" rows=\"3\" style=\"width:100%;padding:10px;border:1px solid #ddd;border-radius:5px\" placeholder=\"Особые пожелания...\"></textarea>
                    </div>
                    <div class=\"total-price\">
                        <p>Итого к оплате:</p>
                        <span id=\"total\">{$tour['price']} ₽</span>
                    </div>
                    <button type=\"submit\" class=\"btn\">✅ Подтвердить бронирование</button>
                    <a href=\"/tours\" class=\"btn-cancel\">← Отмена</a>
                </form>
            </div>
            </div>
            <div class=\"footer\"><p>© 2024 ТурПлатформа</p></div>
            </body></html>";
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка загрузки тура";
            header("Location: /tours");
            exit;
        }
    }
    
    // Обработка бронирования (POST)
    public function confirmBooking($tourId) {
        if(!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }
        
        $userId = $_SESSION["user_id"];
        $participants = (int)($_POST["participants"] ?? 1);
        $comment = $_POST["comment"] ?? "";
        
        try {
            $pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Проверяем существование таблицы orders
            $stmt = $pdo->query("SHOW TABLES LIKE 'orders'");
            if($stmt->rowCount() == 0) {
                // Создаём таблицу orders если её нет
                $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    tour_id INT NOT NULL,
                    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
                    total_price DECIMAL(10,2) NOT NULL,
                    participants INT DEFAULT 1,
                    comment TEXT,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
                )");
            }
            
            // Получаем тур
            $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute([':id' => $tourId]);
            $tour = $stmt->fetch();
            
            if(!$tour) {
                $_SESSION["error"] = "Тур не найден";
                header("Location: /tours");
                exit;
            }
            
            if($tour['available_count'] < $participants) {
                $_SESSION["error"] = "Недостаточно свободных мест";
                header("Location: /tours");
                exit;
            }
            
            $totalPrice = $tour['price'] * $participants;
            
            // Создаём заказ
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, tour_id, participants, total_price, status, comment) 
                                   VALUES (:user_id, :tour_id, :participants, :total_price, 'pending', :comment)");
            $stmt->execute([
                ':user_id' => $userId,
                ':tour_id' => $tourId,
                ':participants' => $participants,
                ':total_price' => $totalPrice,
                ':comment' => $comment
            ]);
            
            // Уменьшаем количество мест
            $stmt = $pdo->prepare("UPDATE tours SET available_count = available_count - :participants WHERE id = :id");
            $stmt->execute([
                ':participants' => $participants,
                ':id' => $tourId
            ]);
            
            $_SESSION["success"] = "Тур успешно забронирован!";
            header("Location: /profile/orders");
            exit;
            
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка бронирования: " . $e->getMessage();
            header("Location: /tours");
            exit;
        }
    }
    
    // Метод book для обратной совместимости
    public function book($tourId) {
        // Простое бронирование без формы
        if(!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }
        
        try {
            $pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Проверяем существование таблицы orders
            $stmt = $pdo->query("SHOW TABLES LIKE 'orders'");
            if($stmt->rowCount() == 0) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    tour_id INT NOT NULL,
                    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
                    total_price DECIMAL(10,2) NOT NULL,
                    participants INT DEFAULT 1,
                    comment TEXT,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
                )");
            }
            
            // Получаем тур
            $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute([':id' => $tourId]);
            $tour = $stmt->fetch();
            
            if(!$tour) {
                $_SESSION["error"] = "Тур не найден";
                header("Location: /tours");
                exit;
            }
            
            if($tour['available_count'] < 1) {
                $_SESSION["error"] = "Нет свободных мест";
                header("Location: /tours");
                exit;
            }
            
            // Создаём заказ
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, tour_id, participants, total_price, status) 
                                   VALUES (:user_id, :tour_id, 1, :total_price, 'pending')");
            $stmt->execute([
                ':user_id' => $_SESSION["user_id"],
                ':tour_id' => $tourId,
                ':total_price' => $tour['price']
            ]);
            
            // Уменьшаем количество мест
            $stmt = $pdo->prepare("UPDATE tours SET available_count = available_count - 1 WHERE id = :id");
            $stmt->execute([':id' => $tourId]);
            
            $_SESSION["success"] = "Тур успешно забронирован!";
            header("Location: /profile/orders");
            exit;
            
        } catch(PDOException $e) {
            $_SESSION["error"] = "Ошибка бронирования";
            header("Location: /tours");
            exit;
        }
    }
}
?>