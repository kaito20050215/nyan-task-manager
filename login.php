<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
require __DIR__ . "/db.php";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        exit("不正なリクエストです");
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute(['u' => $_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: index.php");
        exit;
    } else {
        $error = "ログイン失敗";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログイン | nyan task</title>

<style>

body{
    margin:0;
    font-family:sans-serif;
    background:linear-gradient(135deg,#74ebd5,#acb6e5);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-box{
    background:white;
    padding:40px;
    width:320px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

h1{
    text-align:center;
    margin-bottom:25px;
}

input{
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:6px;
    font-size:14px;
}

button{
    width:100%;
    padding:10px;
    border:none;
    background:#4CAF50;
    color:white;
    font-size:16px;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#45a049;
}

.error{
    color:red;
    text-align:center;
    margin-top:10px;
}

</style>
</head>

<body>

<div class="login-box">

<h1>nyan task🐱</h1>

<form method="POST">

<input type="hidden" name="csrf_token"
       value="<?= $_SESSION['csrf_token'] ?>">

<input type="text" name="username" placeholder="ユーザー名" required>

<input type="password" name="password" placeholder="パスワード" required>

<button type="submit">ログイン</button>

<a href="register.php">新規登録</a>

</form>

<?php if($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

</div>

</body>
</html>