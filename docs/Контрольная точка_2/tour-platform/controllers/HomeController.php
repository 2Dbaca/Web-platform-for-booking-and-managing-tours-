<?php
class HomeController {
    public function index() {
        echo "<h1>Добро пожаловать на ТурПлатформу!</h1>";
        echo "<p>✅ Сайт работает!</p>";
        echo "<ul>";
        echo "<li><a href='/tours'>Туры</a></li>";
        echo "<li><a href='/login'>Вход</a></li>";
        echo "<li><a href='/register'>Регистрация</a></li>";
        echo "</ul>";
    }
}
?>