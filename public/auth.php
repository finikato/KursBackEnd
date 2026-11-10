<?php require_once __DIR__ . '/../src/core.php';
$action = $_GET['action'] ?? 'login';

if ($action === 'logout') { logout(); header('Location: index.php'); exit; }

if ($action === 'register') {
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        $s = getDB()->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
        $h = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $s->bind_param('sss', $_POST['name'], $_POST['email'], $h);
        $s->execute();
        header('Location: auth.php?action=login'); exit;
    }
    renderForm('Регистрация', ['name','email','password']);
    exit;
}

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD']==='POST' && login($_POST['email'], $_POST['password'])) {
        header('Location: index.php'); exit;
    }
    renderForm('Вход', ['email','password']);
    exit;
}

if ($action === 'settings') {
    $u = requireAuth();
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        $s = getDB()->prepare("UPDATE users SET theme=?,language=? WHERE id=?");
        $s->bind_param('ssi', $_POST['theme'], $_POST['language'], $u['id']);
        $s->execute();
        setcookie('theme', $_POST['theme'], time()+2592000, '/', '', false, true);
        header('Location: auth.php?action=settings'); exit;
    }
    // ... форма настроек ...
    exit;
}

function renderForm(string $title, array $fields) { ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title><?=$title?></title></head>
<body><form method="POST">
<h2><?=$title?></h2>
<?php foreach ($fields as $f): ?>
<input name="<?=$f?>" type="<?=$f==='password'?'password':'text'?>" placeholder="<?=$f?>" required><br>
<?php endforeach; ?>
<button>OK</button></form></body></html>
<?php }