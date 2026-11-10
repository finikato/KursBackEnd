<?php
require_once __DIR__ . '/core.php';

function uploadPdf(int $uid, array $f): array {
    if (($f['error'] ?? 1) !== UPLOAD_ERR_OK) return ['error'=>'Ошибка'];
    if ($f['type'] !== 'application/pdf') return ['error'=>'Только PDF'];
    $content = file_get_contents($f['tmp_name']);
    $s = getDB()->prepare("INSERT INTO files(user_id,filename,mime_type,content,size) VALUES(?,?,'application/pdf',?,?)");
    $null = null;
    $s->bind_param('isbi', $uid, $f['name'], $null, $f['size']);
    $s->send_long_data(2, $content); $s->execute();
    return ['id'=>getDB()->insert_id, 'name'=>$f['name']];
}

function listFiles(int $uid): array {
    $s = getDB()->prepare("SELECT id, filename, size, uploaded_at FROM files WHERE user_id=? ORDER BY id DESC");
    $s->bind_param('i', $uid); $s->execute();
    return $s->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getFile(int $id, int $uid): ?array {
    $s = getDB()->prepare("SELECT * FROM files WHERE id=? AND user_id=?");
    $s->bind_param('ii', $id, $uid); $s->execute();
    return $s->get_result()->fetch_assoc() ?: null;
}