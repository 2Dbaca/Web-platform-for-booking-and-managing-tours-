<?php
define('DB_HOST', 'db');
define('DB_NAME', 'tour_platform');
define('DB_USER', 'root');
define('DB_PASS', 'rootpassword');
define('DB_CHARSET', 'utf8mb4');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

define('BASE_PATH', '/var/www/html/');
define('BASE_URL', 'http://localhost/');

define('APP_NAME', 'ТурПлатформа');
define('APP_VERSION', '1.0.0');
?>