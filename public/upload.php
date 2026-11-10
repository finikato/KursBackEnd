<?php
require_once __DIR__ . '/../src/core.php';
require_once __DIR__ . '/../src/files.php';

$u = currentUser();
if (!$u) { header('Location: login.php'); exit; }

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $res = uploadPdf((int)$u['id'], $_FILES['pdf']);
    if (isset($res['error'])) {
        $error = $res['error'];
    } else {
        $msg = 'Файл "' . htmlspecialchars($res['name']) . '" успешно загружен!';
    }
}

$pageTitle = 'Загрузка PDF';
require_once __DIR__ . '/head.php';
?>

<header class="topbar">
    <a href="index.php" class="brand">
        <span class="brand-badge">M</span>
        <span>Marketplace</span>
    </a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="settings.php">Настройки</a>
        <a href="upload.php" class="active">Загрузить</a>
        <a href="files.php">Файлы</a>
        <a href="logout.php" class="logout">Выйти</a>
    </nav>
</header>

<main class="container">
    <div class="form-container auth-wrap" style="max-width: 600px; margin: 40px auto;">
        <legend>Загрузка PDF документа</legend>
        
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= $msg ?> · <a href="files.php" style="color:#8ef3b5; text-decoration:underline;">Перейти к файлам →</a></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <div class="upload-dropzone">
                    <input type="file" name="pdf" accept="application/pdf" required onchange="document.getElementById('file-name').innerText = this.files[0].name">
                    <div class="upload-text">Нажмите сюда, чтобы выбрать PDF</div>
                    <div id="file-name" style="margin-top: 10px; font-weight: 700; color: #ffd700;"></div>
                </div>
                <p class="muted" style="text-align: center;">Максимальный размер файла: 10 МБ</p>
            </div>
            
            <div class="form-section" style="text-align:center; border:none; margin-bottom: 0;">
                <button type="submit" class="btn" style="width: 100%;">Загрузить файл</button>
            </div>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="files.php" style="color:#9cc1ff;">← Вернуться к списку файлов</a>
        </p>
    </div>
</main>

</body>
</html>