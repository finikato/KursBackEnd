<?php
require_once __DIR__ . '/../src/core.php';
$error = ''; $success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (!$name || !$email || !$pass) {
        $error = 'Заполните все поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email';
    } else {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = getDB()->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $name, $email, $hash);
            $stmt->execute();
            $success = true;
        } catch (mysqli_sql_exception $e) {
            $error = ($e->getCode() === 1062) ? 'Этот email уже зарегистрирован' : 'Ошибка: ' . $e->getMessage();
        }
    }
}
$p = contentParams();
$pageTitle = 'Регистрация';
require_once __DIR__ . '/head.php';
?>
<header class="topbar">
    <a href="index.php" class="brand"><span class="brand-badge">M</span><span>Marketplace</span></a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="login.php">Войти</a>
        <a href="register.php" class="active">Регистрация</a>
    </nav>
</header>
<main class="container">
    <div class="form-container auth-wrap">
        <legend>Регистрация</legend>
        <?php if ($success): ?>
            <div class="alert alert-success">Аккаунт создан. <a href="login.php" style="color:#8ef3b5;">Войти →</a></div>
        <?php else: ?>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-section">
                    <label>Имя</label>
                    <input name="name" type="text" placeholder="Иван" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    <label>Email</label>
                    <input name="email" type="email" placeholder="you@mail.ru" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <label>Пароль</label>
                    <input name="password" type="password" placeholder="Пароль" required>
                </div>
                <button type="submit">Создать аккаунт</button>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>