<?php
require_once __DIR__ . '/../src/core.php';
require_once __DIR__ . '/../src/files.php';

$u = requireAuth();
$file = getFile((int)($_GET['id'] ?? 0), (int)$u['id']);

if (!$file) {
    http_response_code(404);
    echo 'Файл не найден';
    exit;
}

header('Content-Type: ' . $file['mime_type']);
header('Content-Disposition: attachment; filename="' . rawurlencode($file['filename']) . '"');
header('Content-Length: ' . $file['size']);
echo $file['content'];