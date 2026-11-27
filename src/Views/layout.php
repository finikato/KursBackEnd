<?php use Core\Session; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Игровая почта') ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="theme-dark">
<header class="topbar">
    <a href="/" class="brand">
        <img src="/uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Fin&Co</span>
    </a>
    <nav>
        <?php if (\Core\Session::get('user_id')): ?>
            <a href="/mail">Почта</a>
            <a href="/profile">Профиль</a>
            <a href="/auth/logout" class="logout">Выйти</a>
        <?php else: ?>
            <a href="/auth/login">Вход</a>
            <a href="/auth/register">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>
<div class="container">
    <?= $content ?>
</div>
</body>
</html>