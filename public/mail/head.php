<?php
// Если в URL есть ?css=1 — отдаём CSS из этой же папки
if (isset($_GET['css'])) {
    header('Content-Type: text/css; charset=utf-8');
    readfile(__DIR__ . '/style.css');
    exit;
}

$pageTitle = $pageTitle ?? 'Игровая почта';
$p = contentParams();
?>
<!DOCTYPE html>
<html lang="<?= $p['language'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Подключаем CSS через PHP -->
    <link rel="stylesheet" href="head.php?css=1&v=1">
</head>
<body></body>