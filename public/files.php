<?php
require_once __DIR__ . '/../src/core.php';
require_once __DIR__ . '/../src/files.php';

$u = currentUser();
if (!$u) { header('Location: login.php'); exit; }

$p = contentParams();
$lang = $p['language'];
$list = listFiles((int)$u['id']);

$pageTitle = $lang === 'en' ? 'My Files' : 'Мои файлы';
require_once __DIR__ . '/head.php';
?>

<header class="topbar">
    <a href="index.php" class="brand">
        <img src="uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Marketplace</span>
    </a>
    <nav>
        <a href="index.php"><?= t('home', $lang) ?></a>
        <a href="settings.php"><?= t('settings', $lang) ?></a>
        <a href="upload.php"><?= t('upload', $lang) ?></a>
        <a href="files.php" class="active"><?= t('files', $lang) ?></a>
        <a href="logout.php" class="logout"><?= t('logout', $lang) ?></a>
    </nav>
</header>

<main class="container">
    <div class="card">
        <legend><?= $lang === 'en' ? 'My Files' : 'Мои файлы' ?></legend>
        
        <?php if (empty($list)): ?>
            <p class="muted">
                <?= $lang === 'en' ? 'No files yet.' : 'Пока нет файлов.' ?> 
                <a href="upload.php" style="color:#9cc1ff;"><?= $lang === 'en' ? 'Upload the first one →' : 'Загрузите первый →' ?></a>
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th><?= $lang === 'en' ? 'File' : 'Файл' ?></th>
                        <th><?= $lang === 'en' ? 'Size' : 'Размер' ?></th>
                        <th><?= $lang === 'en' ? 'Date' : 'Дата' ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['filename']) ?></td>
                        <td><?= round($f['size']/1024, 1) ?> KB</td>
                        <td>
                            <?php 
                            $dateOnly = !empty($f['uploaded_at']) ? substr($f['uploaded_at'], 0, 10) : '';
                            echo htmlspecialchars($dateOnly); 
                            ?>
                        </td>
                        <td>
                            <a href="download.php?id=<?= $f['id'] ?>" class="btn" style="padding:6px 14px;font-size:.85rem;">
                                <?= $lang === 'en' ? 'Download' : 'Скачать' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div style="text-align:center;margin-top:16px;">
            <a href="upload.php" class="btn btn-secondary">
                + <?= $lang === 'en' ? 'Upload more' : 'Загрузить ещё' ?>
            </a>
        </div>
    </div>
</main>

</body>
</html>