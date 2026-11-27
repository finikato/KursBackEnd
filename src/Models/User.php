<?php
namespace Models;

use Core\Database;

class User {
    public static function getById(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT id, name, email, level, gold, exp, avatar FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function authenticate(string $email, string $pass): ?array {
        $stmt = Database::getConnection()->prepare("SELECT id, password FROM users WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        if ($u && password_verify($pass, $u['password'])) {
            return $u;
        }
        return null;
    }

    public static function create(string $name, string $email, string $password): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = Database::getConnection()->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hash);
        return $stmt->execute();
    }
}