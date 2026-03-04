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

<form method="POST">
    <input type="hidden" name="csrf_token"
           value="<?= $_SESSION['csrf_token'] ?>">

    <input type="text" name="username" required>
    <input type="password" name="password" required>

    <button type="submit">ログイン</button>
</form>

<p style="color:red;"><?= htmlspecialchars($error) ?></p>