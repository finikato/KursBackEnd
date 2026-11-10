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

$lang = $u['language'];
$pageTitle = t('settings', $lang);
require_once __DIR__ . '/head.php';
?>
<header class="topbar">
    <a href="index.php" class="brand">
        <img src="uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Marketplace</span>
    </a>
    <nav>
        <a href="index.php"><?= t('main', $lang) ?></a>
        <a href="settings.php" class="active"><?= t('settings', $lang) ?></a>
        <a href="upload.php"><?= t('upload', $lang) ?></a>
        <a href="files.php"><?= t('files', $lang) ?></a>
        <a href="logout.php" class="logout"><?= t('logout', $lang) ?></a>
    </nav>
</header>
<main class="container">
    <div class="form-container auth-wrap">
        <legend><?= t('settings', $lang) ?></legend>
        <?php if ($saved): ?><div class="alert alert-success"><?= $lang === 'en' ? 'Saved' : 'Сохранено' ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-section">
                <label><?= t('theme', $lang) ?></label>
                <select name="theme">
                    <option value="light" <?= $u['theme']==='light'?'selected':'' ?>><?= t('light', $lang) ?></option>
                    <option value="dark" <?= $u['theme']==='dark'?'selected':'' ?>><?= t('dark', $lang) ?></option>
                </select>
                <label><?= t('language', $lang) ?></label>
                <select name="language">
                    <option value="ru" <?= $u['language']==='ru'?'selected':'' ?>>Русский</option>
                    <option value="en" <?= $u['language']==='en'?'selected':'' ?>>English</option>
                </select>
            </div>
            <button type="submit"><?= t('save', $lang) ?></button>
        </form>
    </div>
</main>
</body>
</html>