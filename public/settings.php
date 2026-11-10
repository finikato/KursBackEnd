<?php
require_once __DIR__ . '/../src/core.php';
$u = currentUser();
if (!$u) { header('Location: login.php'); exit; }

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = in_array($_POST['theme'] ?? '', ['light','dark','colorblind']) ? $_POST['theme'] : 'light';
    $lang  = in_array($_POST['language'] ?? '', ['ru','en']) ? $_POST['language'] : 'ru';
    $stmt = getDB()->prepare("UPDATE users SET theme = ?, language = ? WHERE id = ?");
    $stmt->bind_param('ssi', $theme, $lang, $u['id']);
    $stmt->execute();
    setcookie('theme', $theme, time() + 86400 * 30, '/', '', false, true);
    setcookie('language', $lang, time() + 86400 * 30, '/', '', false, true);
    $u['theme'] = $theme; $u['language'] = $lang;
    $saved = true;
}
$pageTitle = 'Настройки';
require_once __DIR__ . '/head.php';
?>
<header class="topbar">
    <a href="index.php" class="brand"><span class="brand-badge">M</span><span>Marketplace</span></a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="settings.php" class="active">Настройки</a>
        <a href="upload.php">Загрузить</a>
        <a href="files.php">Файлы</a>
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
                    <option value="colorblind" <?= $u['theme']==='colorblind'?'selected':'' ?>>Цветовая слепота</option>
                </select>
                <label>Язык</label>
                <select name="language">
                    <option value="ru" <?= $u['language']==='ru'?'selected':'' ?>>Русский</option>
                    <option value="en" <?= $u['language']==='en'?'selected':'' ?>>English</option>
                </select>
            </div>
            <button type="submit">Сохранить</button>
        </form>
    </div>
</main>
</body>
</html>