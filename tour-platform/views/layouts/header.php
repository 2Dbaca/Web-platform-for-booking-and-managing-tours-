<?php
// views/layouts/header.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/"><?php echo APP_NAME; ?></a>
                </div>

                <nav class="nav">
                    <ul>
                        <li><a href="/">Главная</a></li>
                        <li><a href="/tours">Туры</a></li>
                        <?php if (Session::isLoggedIn()): ?>
                            <li><a href="/profile">Личный кабинет</a></li>
                            <?php if (Session::isAdmin()): ?>
                                <li><a href="/admin/dashboard">Админ-панель</a></li>
                            <?php endif; ?>
                            <li><a href="/logout">Выйти</a></li>
                        <?php else: ?>
                            <li><a href="/login">Вход</a></li>
                            <li><a href="/register">Регистрация</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php
            $success = Session::getFlash('success');
            $error = Session::getFlash('error');
            $errors = Session::getFlash('errors');

            if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($errors && is_array($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo $err; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>