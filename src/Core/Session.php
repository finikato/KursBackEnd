<?php
namespace Core;

use SessionHandlerInterface;

class DBSessionHandler implements SessionHandlerInterface {
    public function open(string $p, string $n): bool { return true; }
    public function close(): bool { return true; }
    public function read(string $id): string|false {
        $db = Database::getConnection();
        $s = $db->prepare("SELECT data FROM sessions WHERE id=?");
        $s->bind_param('s', $id); $s->execute();
        $row = $s->get_result()->fetch_assoc();
        return $row['data'] ?? '';
    }
    public function write(string $id, string $data): bool {
        $db = Database::getConnection();
        $uid = $_SESSION['user_id'] ?? null;
        $s = $db->prepare("INSERT INTO sessions(id,user_id,data) VALUES(?,?,?) ON DUPLICATE KEY UPDATE user_id=VALUES(user_id),data=VALUES(data)");
        $s->bind_param('sis', $id, $uid, $data);
        return $s->execute();
    }
    public function destroy(string $id): bool {
        $db = Database::getConnection();
        $s = $db->prepare("DELETE FROM sessions WHERE id=?");
        $s->bind_param('s', $id); return $s->execute();
    }
    public function gc(int $t): int|false {
        $db = Database::getConnection();
        $s = $db->prepare("DELETE FROM sessions WHERE updated_at < NOW()-INTERVAL ? SECOND");
        $s->bind_param('i', $t); $s->execute(); return $s->affected_rows;
    }
}

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        session_set_save_handler(new DBSessionHandler(), true);
        session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'path'=>'/']);
        session_name('APPSESSID');
        session_start();
    }

    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function destroy(): void {
        $_SESSION = [];
        session_destroy();
    }
}