<?php
if (isset($_GET['css'])) {
    header('Content-Type: text/css; charset=utf-8');
    readfile(__DIR__ . '/style.css');
    exit;
}
$pageTitle = $pageTitle ?? 'Игровая почта';
$p = contentParams();
$theme = $p['theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/mail/') !== false) ? '../head.php?css=1&v=106' : 'head.php?css=1&v=106' ?>">
</head>
<body class="<?= themeClass($theme) ?>">