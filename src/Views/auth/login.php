<?php 
$pageTitle = 'Вход';
ob_start(); 
?>

<div class="form-container auth-wrap">
    <legend>Вход</legend>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="/auth/login">
        <div class="form-section">
            <label>Email</label>
            <input name="email" type="email" placeholder="example@mail.ru" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Пароль</label>
            <input name="password" type="password" placeholder="Введите пароль" required>
        </div>
        <button type="submit">Войти</button>
    </form>
    <p style="margin-top:16px;">Нет аккаунта? <a href="/auth/register" style="color:#9cc1ff;">Зарегистрироваться</a></p>
</div>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/../layout.php'; 
?>