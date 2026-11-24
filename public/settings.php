<?php
require_once __DIR__ . '/../src/core.php';
$u = currentUser();
if (!$u) { header('Location: login.php'); exit; }

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = in_array($_POST['theme'] ?? '', ['light','dark']) ? $_POST['theme'] : 'dark';
    $stmt = getDB()->prepare("UPDATE users SET theme = ? WHERE id = ?");
    $stmt->bind_param('si', $theme, $u['id']);
    $stmt->execute();
    setcookie('theme', $theme, time() + 86400 * 30, '/', '', false, true);
    $u['theme'] = $theme;
    $saved = true;
}

$pageTitle = 'Настройки';
require_once __DIR__ . '/head.php';
?>
<header class="topbar">
    <a href="index.php" class="brand">
        <img src="uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Fin&Co</span>
    </a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="settings.php" class="active">Настройки</a>
        <a href="mail/index.php">Почта</a>
        <a href="logout.php" class="logout">Выйти</a>
    </nav>
</header>
<main class="container">
    <div class="form-container auth-wrap">
        <legend>Настройки</legend>
        <?php if ($saved): ?><div class="alert alert-success">Сохранено</div><?php endif; ?>
        <form method="POST">
            <div class="form-section">
                <label>Тема</label>
                <select name="theme">
                    <option value="light" <?= $u['theme']==='light'?'selected':'' ?>>Светлая</option>
                    <option value="dark" <?= $u['theme']==='dark'?'selected':'' ?>>Тёмная</option>
                </select>
            </div>
            <button type="submit">Сохранить</button>
        </form>
    </div>
</main>
</body>
</html>