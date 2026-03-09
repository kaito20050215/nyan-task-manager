<?php
session_start();
require __DIR__ . "/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (!$username || !$password || !$password_confirm) {

        $error = "未入力の項目があります";

    } elseif ($password !== $password_confirm) {

        $error = "パスワードが一致しません";

    } else {

        // 既に存在するか確認
        $stmt = $pdo->prepare("
            SELECT id FROM users WHERE username = :u
        ");
        $stmt->execute(['u' => $username]);

        if ($stmt->fetch()) {

            $error = "そのユーザー名は既に使われています";

        } else {

            $stmt = $pdo->prepare("
                INSERT INTO users (username, password)
                VALUES (:u, :p)
            ");

            $stmt->execute([
                'u' => $username,
                'p' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            header("Location: login.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>新規登録</title>
<style>
body{
    background:#fff6f9;
    font-family:sans-serif;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(255,182,193,0.3);
    width:300px;
}
h2{text-align:center;color:#ff7aa2;}
input{
    width:100%;
    padding:8px;
    margin:8px 0;
    border-radius:10px;
    border:2px solid #ffc1d3;
}
button{
    width:100%;
    padding:8px;
    border:none;
    border-radius:10px;
    background:#ff9eb5;
    color:white;
    cursor:pointer;
}
.error{
    color:red;
    font-size:14px;
    text-align:center;
}
</style>
</head>
<body>

<div class="box">
<h2>🐱 新規登録</h2>

<?php if($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
<input type="text" name="username" placeholder="ユーザー名" required>
<input type="password" name="password" placeholder="パスワード" required>
<input type="password" name="password_confirm" placeholder="パスワード確認" required>
<button type="submit">登録</button>
</form>

<p style="text-align:center;margin-top:10px;">
<a href="login.php">ログインはこちら</a>
</p>

</div>

</body>
</html>