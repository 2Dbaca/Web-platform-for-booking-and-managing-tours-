<?php
// views/tours/index.php
require_once BASE_PATH . 'views/layouts/header.php';
?>

<div class="page-header">
    <h1>Все туры</h1>

    <form action="/tours" method="GET" class="filter-form">
        <div class="filter-group">
            <input type="text" name="country" placeholder="Страна" value="<?php echo htmlspecialchars($_GET['country'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <input type="date" name="start_date" placeholder="От" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <input type="date" name="end_date" placeholder="До" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <input type="number" name="max_price" placeholder="Макс. цена" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Фильтровать</button>
        <a href="/tours" class="btn btn-secondary">Сбросить</a>
    </form>
</div>

<?php if (empty($tours)): ?>
    <div class="empty-state">
        <p>Туры не найдены</p>
        <a href="/tours" class="btn btn-primary">Сбросить фильтры</a>
    </div>
<?php else: ?>
    <div class="tours-grid">
        <?php foreach ($tours as $tour): ?>
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
<?php endif; ?>

<?php
require_once BASE_PATH . 'views/layouts/footer.php';
?>