<?php
require_once __DIR__ . '/../../src/core.php';

$u = currentUser();
if (!$u) { header('Location: ../login.php'); exit; }

$viewUserId = isset($_GET['id']) ? (int)$_GET['id'] : (int)$u['id'];
$isOwnProfile = ($viewUserId === (int)$u['id']);

$stmt = getDB()->prepare("SELECT id, name, email, level, gold, exp, avatar FROM users WHERE id = ?");
$stmt->bind_param('i', $viewUserId);
$stmt->execute();
$profileUser = $stmt->get_result()->fetch_assoc();

if (!$profileUser) {
    header('Location: profile.php');
    exit;
}

$msg = '';
$error = '';

if ($isOwnProfile && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($file['type'], $allowed)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newName = 'avatar_' . $u['id'] . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/';
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $avatarPath = 'uploads/' . $newName;
                $stmt = getDB()->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->bind_param('si', $avatarPath, $u['id']);
                $stmt->execute();
                $u = currentUser();
                $profileUser = $u;
                $msg = 'Аватар успешно обновлен!';
            } else {
                $error = 'Ошибка при сохранении файла.';
            }
        } else {
            $error = 'Разрешены только JPG, PNG и GIF.';
        }
    } else {
        $error = 'Ошибка загрузки файла.';
    }
}

$currentLevel = $profileUser['level'] ?? 1;
$currentExp = $profileUser['exp'] ?? 0;
$expNeeded = round(100 * pow(1.2, $currentLevel - 1));
$progressPercent = $expNeeded > 0 ? min(100, ($currentExp / $expNeeded) * 100) : 0;

$pageTitle = $isOwnProfile ? 'Мой профиль' : 'Профиль игрока';
require_once __DIR__ . '/head.php';
?>

<header class="topbar">
    <a href="../index.php" class="brand">
        <span class="brand-badge">M</span>
        <span>Marketplace</span>
    </a>
    <nav>
        <a href="../index.php">Главная</a>
        <a href="index.php">Почта</a>
        <a href="profile.php" class="active">Профиль</a>
        <a href="../settings.php">Настройки</a>
        <a href="../files.php">Файлы</a>
        <a href="../logout.php" class="logout">Выйти</a>
    </nav>
</header>

<div class="container">
    <div class="mail-layout" style="display: block; max-width: 600px; margin: 0 auto; height: auto;">
        
        <div class="mail-content">
            <div class="mail-content-header" style="text-align: center; border: none; padding-bottom: 10px; margin-bottom: 10px;">
                
                <div style="position: relative; width: 120px; height: 120px; margin: 0 auto 15px;">
                    <?php if (!empty($profileUser['avatar']) && file_exists(__DIR__ . '/../' . $profileUser['avatar'])): ?>
                        <img src="../<?= htmlspecialchars($profileUser['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid #ffd700; box-shadow: 0 0 15px rgba(255,215,0,0.4);">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #6a11cb, #2575fc); border-radius: 50%; border: 3px solid rgba(255,255,255,0.2);"></div>
                    <?php endif; ?>
                </div>

                <h2 style="font-size: 1.8rem; color: #fff; margin-bottom: 5px;"><?= htmlspecialchars($profileUser['name']) ?></h2>
                
                <div style="margin-bottom: 10px;">
                    <p style="color: #ffd700; font-size: 1.1rem; font-weight: 600; margin-bottom: 8px;">
                        Уровень <?= $currentLevel ?>
                    </p>
                    
                    <div style="width: 300px; height: 12px; background: rgba(0,0,0,0.4); border-radius: 6px; margin: 0 auto; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="height: 100%; width: <?= $progressPercent ?>%; background: linear-gradient(90deg, #ffd700, #ffaa00); border-radius: 6px; transition: width 0.3s ease;"></div>
                    </div>
                    <div style="font-size: 0.85rem; color: #cfc8dc; margin-top: 5px;">
                        <?= $currentExp ?> / <?= $expNeeded ?> XP
                    </div>
                </div>

                <?php if ($isOwnProfile): ?>
                <form method="POST" enctype="multipart/form-data" style="margin-bottom: 10px;">
                    <?php if ($msg): ?><div class="alert alert-success" style="padding: 8px 12px; font-size: 0.9rem;"><?= $msg ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-error" style="padding: 8px 12px; font-size: 0.9rem;"><?= $error ?></div><?php endif; ?>
                    
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;" onchange="this.form.submit()">
                        <label for="avatarInput" class="btn" style="padding: 8px 16px; font-size: 0.85rem; cursor: pointer; margin: 0; background: rgba(255,255,255,0.1); box-shadow: none; border: 1px solid rgba(255,255,255,0.2);">
                            Сменить аватар
                        </label>
                    </div>
                </form>
                <?php endif; ?>
            </div>

            <div style="margin-top: 10px; display: flex; flex-direction: column; gap: 10px;">
                
                <div style="background: rgba(0,0,0,0.3); padding: 15px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="color: #cfc8dc; font-size: 0.85rem; margin-bottom: 5px;">Уникальный ID</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: #fff;">#<?= $profileUser['id'] ?></div>
                    <?php if ($isOwnProfile): ?>
                        <div style="font-size: 0.8rem; color: #8ef3b5; margin-top: 5px;">По этому ID можно отправлять вам письма</div>
                    <?php endif; ?>
                </div>

                <div style="background: rgba(0,0,0,0.3); padding: 15px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="color: #cfc8dc; font-size: 0.85rem; margin-bottom: 5px;">Email</div>
                    <div style="font-size: 1.1rem; font-weight: 600; color: #fff;"><?= htmlspecialchars($profileUser['email']) ?></div>
                </div>

                <div style="background: rgba(0,0,0,0.3); padding: 15px 20px; border-radius: 10px; border: 1px solid rgba(255, 215, 0, 0.3);">
                    <div style="color: #cfc8dc; font-size: 0.85rem; margin-bottom: 5px;">Баланс</div>
                    <div style="font-size: 1.4rem; font-weight: 700; color: #ffd700;"><?= $profileUser['gold'] ?? 0 ?> Золота</div>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>