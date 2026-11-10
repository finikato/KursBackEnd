<?php
require_once __DIR__ . '/../src/core.php';
require_once __DIR__ . '/../src/files.php';

$u = currentUser();
if (!$u) { header('Location: login.php'); exit; }

$p = contentParams();
$lang = $p['language'];
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $res = uploadPdf((int)$u['id'], $_FILES['pdf']);
    if (isset($res['error'])) {
        $error = $res['error'];
    } else {
        $msg = ($lang === 'en' ? 'File "' : 'Файл "') . htmlspecialchars($res['name']) . ($lang === 'en' ? '" uploaded successfully!' : '" успешно загружен!');
    }
}

$pageTitle = $lang === 'en' ? 'Upload PDF' : 'Загрузка PDF';
require_once __DIR__ . '/head.php';
?>

<header class="topbar">
    <a href="index.php" class="brand">
        <img src="uploads/logo.jpg" alt="Logo" class="brand-logo">
        <span>Fin&Co</span>
    </a>
    <nav>
        <a href="index.php"><?= t('home', $lang) ?></a>
        <a href="settings.php"><?= t('settings', $lang) ?></a>
        <a href="upload.php" class="active"><?= t('upload', $lang) ?></a>
        <a href="files.php"><?= t('files', $lang) ?></a>
        <a href="logout.php" class="logout"><?= t('logout', $lang) ?></a>
    </nav>
</header>

<main class="container">
    <div class="form-container auth-wrap" style="max-width: 600px; margin: 40px auto;">
        <legend><?= $lang === 'en' ? 'Upload PDF Document' : 'Загрузка PDF документа' ?></legend>
        
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= $msg ?> · <a href="files.php" style="color:#8ef3b5; text-decoration:underline;"><?= $lang === 'en' ? 'Go to files' : 'Перейти к файлам' ?> →</a></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <div class="upload-dropzone" style="border-color: #5e5a5a;">
                    <input type="file" name="pdf" accept="application/pdf" required onchange="document.getElementById('file-name').innerText = this.files[0].name">
                    <div class="upload-text" style="color: #5e5a5a;"><?= $lang === 'en' ? 'Click here to select PDF' : 'Нажмите сюда, чтобы выбрать PDF' ?></div>
                    <div id="file-name" style="margin-top: 10px; font-weight: 700; color: #5e5a5a"></div>
                </div>
                <p class="muted" style="text-align: center;"><?= $lang === 'en' ? 'Maximum file size: 10 MB' : 'Максимальный размер файла: 10 МБ' ?></p>
            </div>
            
            <div class="form-section" style="text-align:center; border:none; margin-bottom: 0;">
                <button type="submit" class="btn" style="width: 100%;"><?= $lang === 'en' ? 'Upload File' : 'Загрузить файл' ?></button>
            </div>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="files.php" style="color:#9cc1ff;"><- <?= $lang === 'en' ? 'Back to files list' : 'Вернуться к списку файлов' ?></a>
        </p>
    </div>
</main>

</body>
</html>