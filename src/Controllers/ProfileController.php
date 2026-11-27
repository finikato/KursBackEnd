<?php
use Core\Session;
use Core\Database;
use Models\User;

class ProfileController {
    public function index() {
        $userId = Session::get('user_id');
        if (!$userId) {
            header('Location: /auth/login');
            exit;
        }

        $u = User::getById($userId);
        $viewUserId = isset($_GET['id']) ? (int)$_GET['id'] : $userId;
        $isOwnProfile = ($viewUserId === $userId);

        $profileUser = $viewUserId === $userId ? $u : User::getById($viewUserId);
        
        if (!$profileUser) {
            header('Location: /profile');
            exit;
        }

        $msg = '';
        $error = '';

        if ($isOwnProfile && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $file = $_FILES['avatar'];
       
            if ($file['error'] !== UPLOAD_ERR_OK) {
                switch ($file['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error = 'Файл слишком большой (превышен лимит сервера).';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error = 'Файл был загружен только частично.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error = 'Файл не был загружен.';
                        break;
                    default:
                        $error = 'Ошибка загрузки файла.';
                        break;
                }
            } 
            elseif ($file['size'] > 10 * 1024 * 1024) {
                $error = 'Размер аватара не должен превышать 10 МБ.';
            } 
            else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!in_array($mime, $allowedMimes)) {
                    $error = 'Разрешены только изображения (JPG, PNG, GIF, WEBP).';
                } else {

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (!in_array($ext, $allowedExts)) {
                        $error = 'Недопустимое расширение файла.';
                    } else {
                        $newName = 'avatar_' . $u['id'] . '_' . time() . '.' . $ext;
                        $uploadDir = '/var/www/html/uploads/';
                        
                        if (!is_dir($uploadDir)) {
                            @mkdir($uploadDir, 0777, true);
                        }

                        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                            if (!empty($u['avatar']) && file_exists(__DIR__ . '/../../public/' . $u['avatar'])) {
                                unlink(__DIR__ . '/../../public/' . $u['avatar']);
                            }

                            $avatarPath = 'uploads/' . $newName;
                            $stmt = Database::getConnection()->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                            $stmt->bind_param('si', $avatarPath, $u['id']);
                            $stmt->execute();
                            
                            $u = User::getById($userId);
                            $profileUser = $u;
                            $msg = 'Аватар успешно обновлен!';
                        } else {
                            $error = 'Не удалось сохранить файл на сервере. Проверьте права доступа.';
                        }
                    }
                }
            }
        }

        $currentLevel = $profileUser['level'] ?? 1;
        $currentExp = $profileUser['exp'] ?? 0;
        $expNeeded = round(100 * pow(1.2, $currentLevel - 1));
        $progressPercent = $expNeeded > 0 ? min(100, ($currentExp / $expNeeded) * 100) : 0;

        $inventory = [];
        if ($isOwnProfile) {
            $mailSystem = new \Models\GameMailSystem();
            $inventory = $mailSystem->getUserInventory($userId);
        }

        require __DIR__ . '/../Views/profile/index.php';
    }
}