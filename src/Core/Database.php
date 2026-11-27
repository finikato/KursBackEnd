<?php
namespace Core;

use mysqli;

class Database {
    private static ?mysqli $instance = null;

    public static function getConnection(): mysqli {
        if (self::$instance === null) {
            self::$instance = new mysqli(
                getenv('DB_HOST') ?: 'db',
                getenv('DB_USER') ?: 'user',
                getenv('DB_PASS') ?: 'password',
                getenv('DB_NAME') ?: 'appDB'
            );
            if (self::$instance->connect_error) {
                die('DB connection failed: ' . self::$instance->connect_error);
            }
            self::$instance->set_charset('utf8mb4');
        }
        return self::$instance;
    }
}