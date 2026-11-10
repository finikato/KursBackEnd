<?php
require_once __DIR__ . '/../src/core.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
        header('Location: index.php'); exit;
    }
    $error = 'Неверный email или пароль';
}
$p = contentParams();
$pageTitle = 'Вход';
require_once __DIR__ . '/head.php'; // <--- ПОДКЛЮЧАЕМ ШАПКУ
?>

<header class="topbar">
    <a href="index.php" class="brand"><span class="brand-badge">M</span><span>Marketplace</span></a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="login.php" class="active">Войти</a>
        <a href="register.php">Регистрация</a>
    </nav>
</header>

<main class="container">
    <div class="form-container auth-wrap">
        <legend>Вход</legend>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-section">
                <label>Email</label>
                <input name="email" type="email" placeholder="example@mail.ru" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <label>Пароль</label>
                <input name="password" type="password" placeholder="Введите пароль" required>
            </div>
            <button type="submit">Войти</button>
        </form>
        <p style="margin-top:16px;">Нет аккаунта? <a href="register.php" style="color:#9cc1ff;">Зарегистрироваться</a></p>
    </div>
</main>
</body>
</html>