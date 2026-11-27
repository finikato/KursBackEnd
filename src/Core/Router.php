<?php
namespace Core;

class Router {
    public static function dispatch(string $url): void {
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
    }
}