<?php

function getDB(): mysqli {
    static $c = null;
    if (!$c) {
        $c = new mysqli(
            getenv('DB_HOST') ?: 'db',
            getenv('DB_USER') ?: 'user',
            getenv('DB_PASS') ?: 'password',
            getenv('DB_NAME') ?: 'appDB'
        );
        if ($c->connect_error) {
            die('DB connection failed: ' . $c->connect_error);
        }
        $c->set_charset('utf8mb4');
    }
    return $c;
}

class DBSessionHandler implements SessionHandlerInterface {
    private mysqli $db;
    public function __construct(mysqli $db) { $this->db = $db; }
    public function open(string $p, string $n): bool { return true; }
    public function close(): bool { return true; }
    public function read(string $id): string|false {
        $s = $this->db->prepare("SELECT data FROM sessions WHERE id=?");
        $s->bind_param('s', $id); $s->execute();
        $row = $s->get_result()->fetch_assoc();
        return $row['data'] ?? '';
    }
    public function write(string $id, string $data): bool {
        $uid = $_SESSION['user_id'] ?? null;
        $s = $this->db->prepare("INSERT INTO sessions(id,user_id,data) VALUES(?,?,?)
                                 ON DUPLICATE KEY UPDATE user_id=VALUES(user_id),data=VALUES(data)");
        $s->bind_param('sis', $id, $uid, $data);
        return $s->execute();
    }
    public function destroy(string $id): bool {
        $s = $this->db->prepare("DELETE FROM sessions WHERE id=?");
        $s->bind_param('s', $id); return $s->execute();
    }
    public function gc(int $t): int|false {
        $s = $this->db->prepare("DELETE FROM sessions WHERE updated_at < NOW()-INTERVAL ? SECOND");
        $s->bind_param('i', $t); $s->execute(); return $s->affected_rows;
    }
}

function startSession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_set_save_handler(new DBSessionHandler(getDB()), true);
    session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'path'=>'/']);
    session_name('APPSESSID');
    session_start();
}

function currentUser(): ?array {
    startSession();
    if (empty($_SESSION['user_id'])) return null;
    $s = getDB()->prepare("SELECT id, name, email, theme, language, level, gold, exp, avatar FROM users WHERE id=?");
    $s->bind_param('i', $_SESSION['user_id']); $s->execute();
    $row = $s->get_result()->fetch_assoc();
    return $row ?: null;
}

function login(string $email, string $pass): bool {
    $s = getDB()->prepare("SELECT id,password FROM users WHERE email=?");
    $s->bind_param('s', $email); $s->execute();
    $u = $s->get_result()->fetch_assoc();
    if ($u && password_verify($pass, $u['password'])) {
        startSession(); session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$u['id'];
        return true;
    }
    return false;
}

function logout(): void {
    startSession(); $_SESSION = []; session_destroy();
}

function contentParams(): array {
    $u = currentUser();
    if ($u) return ['login'=>$u['name'], 'theme'=>$u['theme'], 'language'=>$u['language']];
    return ['login'=>$_COOKIE['guest_name'] ?? 'Гость',
            'theme'=>$_COOKIE['theme'] ?? 'dark',
            'language'=>$_COOKIE['language'] ?? 'ru'];
}

function t(string $k, string $lang): string {
    $d = [
        'ru' => [
            'welcome' => 'Добро пожаловать', 'logout' => 'Выйти', 'login' => 'Войти',
            'settings' => 'Настройки', 'upload' => 'Загрузить', 'files' => 'Файлы',
            'register' => 'Регистрация', 'save' => 'Сохранить', 'main' => 'На главную',
            'home' => 'Главная',
            'theme' => 'Тема', 'language' => 'Язык', 'light' => 'Светлая', 'dark' => 'Тёмная',
            'profile' => 'Профиль', 'mail' => 'Почта'
        ],
        'en' => [
            'welcome' => 'Welcome', 'logout' => 'Logout', 'login' => 'Login',
            'settings' => 'Settings', 'upload' => 'Upload', 'files' => 'Files',
            'register' => 'Register', 'save' => 'Save', 'main' => 'Home',
            'home' => 'Home',
            'theme' => 'Theme', 'language' => 'Language', 'light' => 'Light', 'dark' => 'Dark',
            'profile' => 'Profile', 'mail' => 'Mail'
        ]
    ];
    return $d[$lang][$k] ?? $k;
}

function themeClass(string $t): string {
    return match($t) { 'dark'=>'theme-dark', default=>'theme-light' };
}

function requireAuth(): array {
    $u = currentUser();
    if (!$u) {
        header('Location: login.php');
        exit;
    }
    return $u;
}