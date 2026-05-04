<?php
// init_db.php - запустите один раз для заполнения базы данных

try {
    $pdo = new PDO("mysql:host=db;dbname=tour_platform;charset=utf8mb4", "root", "rootpassword");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Инициализация базы данных...</h2>";
    
    // Отключаем проверку внешних ключей
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Очищаем таблицы
    $pdo->exec("DELETE FROM orders");
    $pdo->exec("DELETE FROM tours");
    $pdo->exec("DELETE FROM users WHERE login != 'admin'");
    
    echo "<p>✅ Таблицы очищены</p>";
    
    // Создаём пользователей
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (login, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['user', 'user@example.com', $hashedPassword, 'client', 'Обычный пользователь']);
    $stmt->execute(['ivan', 'ivan@example.com', $hashedPassword, 'client', 'Иван Петров']);
    $stmt->execute(['maria', 'maria@example.com', $hashedPassword, 'client', 'Мария Сидорова']);
    echo "<p>✅ Пользователи созданы</p>";
    
    // Получаем ID пользователей
    $userIds = [];
    $stmt = $pdo->query("SELECT id, login FROM users");
    while($row = $stmt->fetch()) {
        $userIds[$row['login']] = $row['id'];
    }
    
    // Создаём туры с явными ID
    $pdo->exec("ALTER TABLE tours AUTO_INCREMENT = 1");
    
    $tours = [
        [1, 'Райский отдых в Турции', 'Турция', 45000, 15, '2024-06-01', '2024-06-10', 'Отличный отдых на побережье Средиземного моря. Отель 5* all inclusive.'],
        [2, 'Романтический Париж', 'Франция', 65000, 8, '2024-07-05', '2024-07-12', 'Путешествие в столицу любви. Эйфелева башня, Лувр, дегустация вин.'],
        [3, 'Итальянское путешествие', 'Италия', 89000, 10, '2024-08-10', '2024-08-20', 'Посещение Рима, Флоренции и Венеции.'],
        [4, 'Греческие каникулы', 'Греция', 78000, 12, '2024-07-15', '2024-07-25', 'Отдых на острове Крит. Песчаные пляжи.'],
        [5, 'Барселона и Коста-Брава', 'Испания', 71000, 6, '2024-08-20', '2024-08-28', 'Архитектура Гауди, прогулки по старому городу.'],
        [6, 'Дубай - город мечты', 'ОАЭ', 98000, 5, '2024-09-01', '2024-09-09', 'Роскошный отдых в Дубае.'],
        [7, 'Путешествие по Бали', 'Индонезия', 125000, 7, '2024-10-01', '2024-10-14', 'Экзотический отдых на острове богов.'],
        [8, 'Золотое кольцо России', 'Россия', 35000, 20, '2024-07-01', '2024-07-07', 'Путешествие по древним городам России.']
    ];
    
    foreach($tours as $tour) {
        $stmt = $pdo->prepare("INSERT INTO tours (id, name, country, price, available_count, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($tour);
    }
    echo "<p>✅ Туры созданы</p>";
    
    // Создаём заказы
    $orders = [
        [$userIds['user'], 1, 2, 90000, 'confirmed', '2024-05-01 10:30:00'],
        [$userIds['user'], 2, 1, 65000, 'pending', '2024-05-02 14:20:00'],
        [$userIds['ivan'], 3, 2, 178000, 'confirmed', '2024-05-03 09:15:00'],
        [$userIds['ivan'], 1, 1, 45000, 'completed', '2024-05-04 16:45:00'],
        [$userIds['maria'], 4, 2, 156000, 'pending', '2024-05-05 11:00:00'],
        [$userIds['maria'], 5, 1, 71000, 'confirmed', '2024-05-06 13:30:00'],
        [$userIds['user'], 6, 1, 98000, 'cancelled', '2024-05-07 08:20:00'],
        [$userIds['ivan'], 2, 2, 130000, 'confirmed', '2024-05-08 15:10:00'],
        [$userIds['maria'], 8, 3, 105000, 'pending', '2024-05-09 12:45:00'],
        [$userIds['user'], 4, 1, 78000, 'completed', '2024-05-10 10:00:00'],
        [$userIds['user'], 3, 1, 89000, 'confirmed', '2024-05-11 09:30:00'],
        [$userIds['ivan'], 5, 2, 142000, 'pending', '2024-05-12 14:00:00'],
        [$userIds['maria'], 1, 3, 135000, 'confirmed', '2024-05-13 11:15:00'],
        [$userIds['user'], 7, 1, 125000, 'pending', '2024-05-14 16:30:00'],
        [$userIds['ivan'], 6, 2, 196000, 'confirmed', '2024-05-15 10:45:00']
    ];
    
    foreach($orders as $order) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, tour_id, participants, total_price, status, order_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute($order);
    }
    echo "<p>✅ Заказы созданы</p>";
    
    // Обновляем количество доступных мест
    $pdo->exec("UPDATE tours SET available_count = 15 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 1 AND status != 'cancelled') WHERE id = 1");
    $pdo->exec("UPDATE tours SET available_count = 8 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 2 AND status != 'cancelled') WHERE id = 2");
    $pdo->exec("UPDATE tours SET available_count = 10 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 3 AND status != 'cancelled') WHERE id = 3");
    $pdo->exec("UPDATE tours SET available_count = 12 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 4 AND status != 'cancelled') WHERE id = 4");
    $pdo->exec("UPDATE tours SET available_count = 6 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 5 AND status != 'cancelled') WHERE id = 5");
    $pdo->exec("UPDATE tours SET available_count = 5 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 6 AND status != 'cancelled') WHERE id = 6");
    $pdo->exec("UPDATE tours SET available_count = 7 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 7 AND status != 'cancelled') WHERE id = 7");
    $pdo->exec("UPDATE tours SET available_count = 20 - (SELECT COALESCE(SUM(participants),0) FROM orders WHERE tour_id = 8 AND status != 'cancelled') WHERE id = 8");
    
    echo "<p>✅ Количество мест обновлено</p>";
    
    // Включаем проверку внешних ключей
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Проверка
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tours");
    $toursCount = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
    $ordersCount = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $usersCount = $stmt->fetch()['count'];
    
    echo "<hr>";
    echo "<h3>📊 Результат:</h3>";
    echo "<ul>";
    echo "<li>Пользователей: <strong>$usersCount</strong></li>";
    echo "<li>Туров: <strong>$toursCount</strong></li>";
    echo "<li>Заказов: <strong>$ordersCount</strong></li>";
    echo "</ul>";
    
    // Показываем список туров
    echo "<h3>🏖️ Список туров:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Название</th><th>Страна</th><th>Цена</th><th>Доступно</th></tr>";
    $stmt = $pdo->query("SELECT id, name, country, price, available_count FROM tours");
    while($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['country']}</td>";
        echo "<td>{$row['price']} ₽</td>";
        echo "<td>{$row['available_count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p>✅ Инициализация завершена!</p>";
    echo "<p><a href='/admin/reports'>📊 Перейти к отчётам</a> | <a href='/tours'>✈️ Перейти к турам</a> | <a href='/login'>🔐 Войти</a></p>";
    
} catch(PDOException $e) {
    echo "<h2 style='color:red'>Ошибка: " . $e->getMessage() . "</h2>";
}
?>