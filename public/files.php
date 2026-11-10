<?php
require_once __DIR__ . '/../src/core.php';
require_once __DIR__ . '/../src/files.php';
$u = currentUser();
if (!$u) { header('Location: login.php'); exit; }
$list = listFiles((int)$u['id']);
$pageTitle = 'Мои файлы';
require_once __DIR__ . '/head.php';
?>
<header class="topbar">
    <a href="index.php" class="brand"><span class="brand-badge">M</span><span>Marketplace</span></a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="settings.php">Настройки</a>
        <a href="upload.php">Загрузить</a>
        <a href="files.php" class="active">Файлы</a>
        <a href="logout.php" class="logout">Выйти</a>
    </nav>
</header>
<main class="container">
    <div class="card">
        <legend>Мои файлы</legend>
        <?php if (empty($list)): ?>
            <p class="muted">Пока нет файлов. <a href="upload.php" style="color:#9cc1ff;">Загрузите первый -></a></p>
        <?php else: ?>
            <table>
                <thead><tr><th>Файл</th><th>Размер</th><th>Дата</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($list as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['filename']) ?></td>
                        <td><?= round($f['size']/1024, 1) ?> КБ</td>
                        <td><?= htmlspecialchars($f['uploaded_at'] ?? '') ?></td>
                        <td><a href="download.php?id=<?= $f['id'] ?>" class="btn" style="padding:6px 14px;font-size:.85rem;">Скачать</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div style="text-align:center;margin-top:16px;">
            <a href="upload.php" class="btn btn-secondary">+ Загрузить ещё</a>
        </div>
    </div>
</main>
</body>
</html>