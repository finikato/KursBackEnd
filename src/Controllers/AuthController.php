<?php
use Core\Session;
use Core\Database;
use Models\User;

class AuthController {
    public function login() {
        if (Session::get('user_id')) {
            header('Location: /mail');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $u = User::authenticate($email, $password);
            if ($u) {
                Session::set('user_id', (int)$u['id']);
                session_regenerate_id(true);
                header('Location: /mail');
                exit;
            }
            $error = 'Неверный email или пароль';
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function register() {
        if (Session::get('user_id')) {
            header('Location: /mail');
            exit;
        }

        $error = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            if (!$name || !$email || !$pass) {
                $error = 'Заполните все поля';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Некорректный email';
            } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $pass)) {
                $error = 'Пароль может содержать только английские буквы и цифры';
            } else {
                try {
                    User::create($name, $email, $pass);
                    $success = true;
                } catch (\mysqli_sql_exception $e) {
                    if ($e->getCode() === 1062) {
                        $error = 'Этот email уже зарегистрирован';
                    } else {
                        $error = 'Ошибка при регистрации';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/auth/register.php';
    }

    public function logout() {
        Session::destroy();
        header('Location: /auth/login');
        exit;
    }
}