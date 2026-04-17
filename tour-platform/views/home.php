<?php
// views/home.php
require_once 'views/layouts/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1>Путешествуйте с нами</h1>
        <p>Откройте для себя лучшие направления по всему миру</p>
        <form action="/search" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Поиск туров по странам..." required>
            <button type="submit" class="btn btn-primary">Найти</button>
        </form>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2>Почему выбирают нас</h2>
        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon">🌍</div>
                <h3>Широкий выбор</h3>
                <p>Более 100 направлений по всему миру</p>
            </div>
            <div class="feature">
                <div class="feature-icon">💰</div>
                <h3>Лучшие цены</h3>
                <p>Гарантия низких цен на все туры</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔒</div>
                <h3>Безопасность</h3>
                <p>Безопасное бронирование и оплата</p>
            </div>
            <div class="feature">
                <div class="feature-icon">24/7</div>
                <h3>Поддержка</h3>
                <p>Круглосуточная поддержка клиентов</p>
            </div>
        </div>
    </div>
</section>

<section class="featured-tours">
    <div class="container">
        <h2>Популярные туры</h2>
        <div class="tours-grid">
            <?php foreach ($featuredTours as $tour): ?>
                <div class="tour-card">
                    <?php if ($tour['image']): ?>
                        <img src="<?php echo $tour['image']; ?>" alt="<?php echo htmlspecialchars($tour['name']); ?>" class="tour-image">
                    <?php else: ?>
                        <div class="tour-image-placeholder">🏝️</div>
                    <?php endif; ?>
                    <div class="tour-info">
                        <h3><?php echo htmlspecialchars($tour['name']); ?></h3>
                        <p class="tour-country"><?php echo htmlspecialchars($tour['country']); ?></p>
                        <p class="tour-dates">
                            <?php echo date('d.m.Y', strtotime($tour['start_date'])); ?> -
                            <?php echo date('d.m.Y', strtotime($tour['end_date'])); ?>
                        </p>
                        <p class="tour-price"><?php echo number_format($tour['price'], 0, '', ' '); ?> ₽</p>
                        <p class="tour-available">Доступно: <?php echo $tour['available_count']; ?> мест</p>
                        <a href="/tours/<?php echo $tour['id']; ?>" class="btn btn-secondary">Подробнее</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="/tours" class="btn btn-primary">Все туры</a>
        </div>
    </div>
</section>

<section class="popular-destinations">
    <div class="container">
        <h2>Популярные направления</h2>
        <div class="destinations-grid">
            <?php foreach ($popularDestinations as $dest): ?>
                <a href="/tours?country=<?php echo urlencode($dest['country']); ?>" class="destination-card">
                    <h3><?php echo htmlspecialchars($dest['country']); ?></h3>
                    <p><?php echo $dest['bookings']; ?> бронирований</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
require_once 'views/layouts/footer.php';
?>