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

$url = $_GET['url'] ?? 'auth/login';
$url = trim($url, '/'); 

$parts = explode('/', $url);

$controllerName = ucfirst($parts[0] ?? 'mail') . 'Controller';
$methodName = $parts[1] ?? 'index';
$params = array_slice($parts, 2);

$controllerClassWithNs = "Controllers\\" . $controllerName;
$controllerClassWithoutNs = $controllerName;

if (class_exists($controllerClassWithNs)) {
    $controller = new $controllerClassWithNs();
} elseif (class_exists($controllerClassWithoutNs)) {
    $controller = new $controllerClassWithoutNs();
} else {
    http_response_code(404);
    die("404 - Контроллер $controllerName не найден");
}

if (method_exists($controller, $methodName)) {
    call_user_func_array([$controller, $methodName], $params);
} else {
    http_response_code(404);
    die("404 - Метод $methodName не найден в контроллере $controllerName");
}