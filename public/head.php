<?php
if (isset($_GET['css'])) {
    header('Content-Type: text/css; charset=utf-8');
    readfile(__DIR__ . '/style.css');
    exit;
}
$pageTitle = $pageTitle ?? 'Marketplace';
$p = contentParams();
?>
<!DOCTYPE html>
<html lang="<?= $p['language'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="head.php?css=1&v=105">
</head>
<body class="<?= themeClass($p['theme']) ?>">