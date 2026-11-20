<?php
require_once __DIR__ . '/../src/core.php';

$u = currentUser();
$p = contentParams();
$lang = $p['language'];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$pageTitle = t('home', $lang);
require_once __DIR__ . '/head.php';
?>

<header class="topbar">
    <a href="index.php" class="brand">
        <img src="uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Fin&Co</span>
    </a>
    <nav>
        <?php if ($u): ?>
            <a href="index.php" class="active"><?= t('home', $lang) ?></a>
            <a href="settings.php"><?= t('settings', $lang) ?></a>
            <a href="mail/profile.php"><?= t('profile', $lang) ?></a>
            <a href="mail/index.php"><?= t('mail', $lang) ?></a>
            <a href="logout.php" class="logout"><?= t('logout', $lang) ?></a>
        <?php else: ?>
            <a href="index.php" class="active"><?= t('home', $lang) ?></a>
            <a href="login.php"><?= t('login', $lang) ?></a>
            <a href="register.php"><?= t('register', $lang) ?></a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <section class="hero" style="margin-bottom: 60px; margin-top: 40px;">
        <h1><?= t('welcome', $lang) ?>, <span class="accent"><?= e($p['login']) ?></span></h1>
        <p class="hero-subtitle">
            <?= $lang === 'en' ? 'Welcome to your personal portal' : 'Добро пожаловать в ваш личный портал' ?>
        </p>
    </section>

    <section class="card" style="text-align: center; padding: 80px 20px;">
        <h2 class="hero-title">
            <?= $lang === 'en' ? 'Check your in-game mail!' : 'Проверьте свою игровую почту!' ?>
        </h2>
        <p class="hero-text">
            <?= $lang === 'en' 
                ? 'You have rewards, experience points and messages waiting for you. Claim your gifts now!' 
                : 'Вас ждут награды, опыт и сообщения. Заберите свои подарки прямо сейчас!' ?>
        </p>
        <a href="mail/index.php" class="btn" style="font-size: 1.3rem; padding: 20px 60px; border-radius: 40px;">
            <?= $lang === 'en' ? 'Go to Mail' : 'Перейти в почту' ?> ->
        </a>
    </section>
</main>
</body>
</html>