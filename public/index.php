<?php
require_once __DIR__ . '/../src/core.php';
require_once __DIR__ . '/../src/files.php';

$u = currentUser();
$p = contentParams();
$lang = $p['language']; // Получаем текущий язык
$filesList = $u ? listFiles((int)$u['id']) : [];

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
            <a href="upload.php"><?= t('upload', $lang) ?></a>
            <a href="files.php"><?= t('files', $lang) ?></a>
            <a href="logout.php" class="logout"><?= t('logout', $lang) ?></a>
        <?php else: ?>
            <a href="index.php" class="active"><?= t('home', $lang) ?></a>
            <a href="login.php"><?= t('login', $lang) ?></a>
            <a href="register.php"><?= t('register', $lang) ?></a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <section class="hero">
        <h1>
            <?= t('welcome', $lang) ?>, 
            <span class="accent"><?= e($p['login']) ?></span>
        </h1>
        <p style="color:#cfc8dc;font-size:1.05rem;">
            <?= $lang === 'en' ? 'Services available on this server' : 'Сервисы, доступные на этом сервере' ?>
        </p>
    </section>

    <section id="services">
        <div class="services">
            <a href="legacy.php?service=drawer&num=2" class="service-card sc-orange">
                <div class="sc-emoji">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 3v9l6.5 6.5"></path>
                    </svg>
                </div>
                <div class="sc-body">
                    <div class="sc-title"><?= $lang === 'en' ? 'SVG Drawer' : 'Рисовалка SVG' ?></div>
                    <div class="sc-desc"><?= $lang === 'en' ? 'Circle or square by code 0–7' : 'Круг или квадрат по коду 0–7' ?></div>
                </div>
                <div class="sc-arrow">→</div>
            </a>
            
            <a href="legacy.php?service=sort&array=5,3,8,1,9,2" class="service-card sc-blue">
                <div class="sc-emoji">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h12M3 12h8M3 18h4"></path>
                        <path d="M18 4v16M18 20l-3-3M18 20l3-3"></path>
                    </svg>
                </div>
                <div class="sc-body">
                    <div class="sc-title"><?= $lang === 'en' ? 'Sorting' : 'Сортировка' ?></div>
                    <div class="sc-desc"><?= $lang === 'en' ? 'Selection sort' : 'Сортировка выбором' ?></div>
                </div>
                <div class="sc-arrow">→</div>
            </a>

            <a href="legacy.php?service=server" class="service-card sc-green">
                <div class="sc-emoji">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="6" rx="2"></rect>
                        <rect x="3" y="14" width="18" height="6" rx="2"></rect>
                        <circle cx="7" cy="7" r="1"></circle>
                        <circle cx="7" cy="17" r="1"></circle>
                    </svg>
                </div>
                <div class="sc-body">
                    <div class="sc-title"><?= $lang === 'en' ? 'Server' : 'Сервер' ?></div>
                    <div class="sc-desc"><?= $lang === 'en' ? 'PHP, whoami, ls, ps' : 'PHP, whoami, ls, ps' ?></div>
                </div>
                <div class="sc-arrow">→</div>
            </a>

            <a href="mail/index.php" class="service-card sc-purple">
                <div class="sc-emoji">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16v16H4z"></path>
                        <path d="M4 6l8 7 8-7"></path>
                    </svg>
                </div>
                <div class="sc-body">
                    <div class="sc-title"><?= $lang === 'en' ? 'Game Mail' : 'Игровая почта' ?></div>
                    <div class="sc-desc"><?= $lang === 'en' ? 'Rewards and attachments' : 'Награды и вложения' ?></div>
                </div>
                <div class="sc-arrow">→</div>
            </a>
        </div>
    </section>

    <div class="grid-2">
        <section class="card">
            <legend><?= $lang === 'en' ? 'My Files (PDF)' : 'Мои файлы (PDF)' ?></legend>
            <?php if (!$u): ?>
                <p class="muted"><?= $lang === 'en' ? 'Log in to see your files.' : 'Войдите, чтобы увидеть свои файлы.' ?></p>
            <?php elseif (empty($filesList)): ?>
                <p class="muted"><?= $lang === 'en' ? 'No files yet.' : 'Пока нет файлов.' ?> <a href="upload.php" style="color:#9cc1ff;"><?= $lang === 'en' ? 'Upload the first one →' : 'Загрузите первый →' ?></a></p>
            <?php else: ?>
                <table>
                    <thead><tr><th><?= $lang === 'en' ? 'File' : 'Файл' ?></th><th><?= $lang === 'en' ? 'Size' : 'Размер' ?></th><th><?= $lang === 'en' ? 'Date' : 'Дата' ?></th></tr></thead>
                    <tbody>
                        <?php foreach ($filesList as $f): ?>
                        <tr>
                            <td><a href="download.php?id=<?= $f['id'] ?>" style="color:#9cc1ff;"><?= e($f['filename']) ?></a></td>
                            <td><?= round($f['size']/1024, 1) ?> KB</td>
                            <td><?= e($f['uploaded_at'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="card">
            <legend><?= $lang === 'en' ? 'Recent Requests (demo)' : 'Последние запросы (демо)' ?></legend>
            <table>
                <thead><tr><th>ID</th><th><?= $lang === 'en' ? 'Service' : 'Сервис' ?></th><th><?= $lang === 'en' ? 'Status' : 'Статус' ?></th></tr></thead>
                <tbody>
                    <tr><td>#1042</td><td><?= $lang === 'en' ? 'Drawer' : 'Рисовалка' ?></td><td><span style="color:#8ef3b5;"><?= $lang === 'en' ? 'Completed' : 'Завершено' ?></span></td></tr>
                    <tr><td>#1041</td><td><?= $lang === 'en' ? 'Sorting' : 'Сортировка' ?></td><td><span style="color:#ffd28a;"><?= $lang === 'en' ? 'In progress' : 'В работе' ?></span></td></tr>
                    <tr><td>#1040</td><td><?= $lang === 'en' ? 'Server' : 'Сервер' ?></td><td><span style="color:#8ef3b5;"><?= $lang === 'en' ? 'Completed' : 'Завершено' ?></span></td></tr>
                </tbody>
            </table>
        </section>
    </div>
</main>
</body>
</html>