<?php
require_once __DIR__ . '/../src/core.php';

$u = currentUser();
$p = contentParams();

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$pageTitle = 'Главная';
require_once __DIR__ . '/head.php';
?>

<header class="topbar">
    <a href="index.php" class="brand">
        <img src="uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Fin&Co</span>
    </a>
    <nav>
        <?php if ($u): ?>
            <a href="index.php" class="active">Главная</a>
            <a href="settings.php">Настройки</a>
            <a href="mail/profile.php">Профиль</a>
            <a href="mail/index.php">Почта</a>
            <a href="logout.php" class="logout">Выйти</a>
        <?php else: ?>
            <a href="index.php" class="active">Главная</a>
            <a href="login.php">Войти</a>
            <a href="register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <section class="hero" style="margin-bottom: 60px; margin-top: 40px;">
        <h1>Добро пожаловать, <span class="accent"><?= e($p['login']) ?></span></h1>
        <p class="hero-subtitle">Добро пожаловать в ваш личный портал</p>
    </section>

    <section class="card" style="text-align: center; padding: 80px 20px;">
        <h2 class="hero-title">Проверьте свою игровую почту!</h2>
        <p class="hero-text">
            Вас ждут награды, опыт и сообщения. Заберите свои подарки прямо сейчас!
        </p>
        <a href="mail/index.php" class="btn" style="font-size: 1.3rem; padding: 20px 60px; border-radius: 40px;">
            Перейти в почту ->
        </a>
    </section>
</main>
</body>
</html>