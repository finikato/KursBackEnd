<?php 
$pageTitle = 'Регистрация';
ob_start(); 
?>

<div class="form-container auth-wrap">
    <legend>Регистрация</legend>
    
    <?php if ($success): ?>
        <div class="alert alert-success">Аккаунт создан. <a href="/auth/login" style="color:#8ef3b5;">Войти -></a></div>
    <?php else: ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/auth/register">
            <div class="form-section">
                <label>Имя</label>
                <input name="name" type="text" placeholder="Иван" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                <label>Email</label>
                <input name="email" type="email" placeholder="you@mail.ru" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <label>Пароль</label>
                <input name="password" type="password" placeholder="Только буквы и цифры" required pattern="^[a-zA-Z0-9]+$" title="Пароль должен содержать только английские буквы и цифры">
            </div>
            <button type="submit">Создать аккаунт</button>
        </form>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/../layout.php'; 
?>