<?php
/**
 * .env ファイルから設定を読込
 */

// .env ファイルが存在するか確認
if (!file_exists(__DIR__ . '/.env')) {
    die('.env ファイルが見つかりません');
}

// .env ファイルの内容を読込
$env = [];
$lines = file(__DIR__ . '/.env');

foreach ($lines as $line) {
    $line = trim($line);
    
    // 空行やコメント（#）をスキップ
    if (!$line || strpos($line, '#') === 0) {
        continue;
    }
    
    // 「KEY=VALUE」の形で分割
    if (strpos($line, '=') !== false) {
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

// 必要な設定があるか確認
if (empty($env['DB_HOST']) || empty($env['DB_NAME']) || empty($env['DB_USER'])) {
    die('.env に DB_HOST, DB_NAME, DB_USER が必要です');
}

// DSN を組み立てる（.env から取ってくる）
$dsn = "mysql:host=" . $env['DB_HOST'] . 
       ";dbname=" . $env['DB_NAME'] . 
       ";charset=" . ($env['DB_CHARSET'] ?? 'utf8mb4');

$user = $env['DB_USER'];
$pass = $env['DB_PASS'] ?? '';  // DB_PASS がなかったら空文字

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // 接続成功
    // echo "データベース接続成功！";
    
} catch (PDOException $e) {
    echo "❌ エラー: " . $e->getMessage();
    exit;
}
?>