<?php
require_once __DIR__ . '/../src/core.php';
$p = contentParams();
$svc = $_GET['service'] ?? 'drawer';

if ($svc === 'drawer') {
    $n = max(0, min(7, (int)($_GET['num'] ?? 0)));
    $shape = ($n >> 2) & 1; $color = ($n >> 1) & 1; $size = $n & 1;
    $fill = $color ? '#0066ff' : '#ff0000';
    $r = $size ? 60 : 30;
    $svg = $shape
        ? "<rect x='".(100-$r)."' y='".(100-$r)."' width='".($r*2)."' height='".($r*2)."' fill='$fill'/>"
        : "<circle cx='100' cy='100' r='$r' fill='$fill'/>";
    $body = "
        <legend>Рисовалка</legend>
        <p>Код фигуры: <strong>$n</strong></p>
        <div class='svg-wrap'>
            <svg width='200' height='200' xmlns='http://www.w3.org/2000/svg'>$svg</svg>
        </div>
        <form method='GET'>
            <input type='hidden' name='service' value='drawer'>
            <div class='form-section'>
                <label>Введите число 0–7</label>
                <input type='number' name='num' min='0' max='7' value='$n'>
            </div>
            <button>Показать</button>
        </form>";
}
elseif ($svc === 'sort') {
    $in = $_GET['array'] ?? '5,3,8,1,9,2';
    $a = array_map('intval', explode(',', $in));
    $orig = $a;
    for ($i=0; $i<count($a)-1; $i++) {
        $m = $i;
        for ($j=$i+1; $j<count($a); $j++) if ($a[$j] < $a[$m]) $m = $j;
        [$a[$i], $a[$m]] = [$a[$m], $a[$i]];
    }
    $body = "
        <legend>Сортировка выбором</legend>
        <p><b>Исходный:</b> ".implode(', ', $orig)."</p>
        <p><b>Отсортированный:</b> ".implode(', ', $a)."</p>
        <form method='GET'>
            <input type='hidden' name='service' value='sort'>
            <div class='form-section'>
                <label>Массив через запятую</label>
                <input type='text' name='array' value='".htmlspecialchars($in)."'>
            </div>
            <button>Сортировать</button>
        </form>";
}
else {
    $body = "
        <legend>Информация о сервере</legend>
        <p><b>PHP:</b> ".phpversion()."</p>
        <p><b>whoami:</b> ".trim(shell_exec('whoami'))."</p>
        <p><b>hostname:</b> ".trim(shell_exec('hostname'))."</p>
        <pre>".htmlspecialchars(shell_exec('ls -la /var/www/html') ?? '')."</pre>";
}

$pageTitle = 'Сервис';
require_once __DIR__ . '/head.php';
?>
<header class="topbar">
    <a href="index.php" class="brand"><span class="brand-badge">M</span><span>Marketplace</span></a>
    <nav>
        <a href="index.php">Главная</a>
        <?php if (currentUser()): ?>
            <a href="settings.php">Настройки</a>
            <a href="upload.php">Загрузить</a>
            <a href="files.php">Файлы</a>
            <a href="logout.php" class="logout">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
            <a href="register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
    <div class="form-container auth-wrap">
        <?= $body ?>
    </div>
</main>
</body>
</html>