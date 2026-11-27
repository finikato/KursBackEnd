<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Session.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/GameMailSystem.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/MailController.php';
require_once __DIR__ . '/../src/Controllers/ProfileController.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Core\Session;
Session::start();

$url = $_GET['url'] ?? 'mail';
$url = rtrim($url, '/');
$parts = explode('/', $url);

$controllerName = ucfirst($parts[0] ?? 'mail') . 'Controller';
$methodName = $parts[1] ?? 'index';
$params = array_slice($parts, 2);

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        call_user_func_array([$controller, $methodName], $params);
    } else {
        http_response_code(404);
        echo "404 - Метод не найден";
    }
} else {
    http_response_code(404);
    echo "404 - Страница не найдена";
}