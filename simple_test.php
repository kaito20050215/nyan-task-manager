<?php
echo "<h2>🔧 接続テスト</h2>";

// 直接接続（デバッグ用）
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=nyan_task;charset=utf8mb4",
        "root",
        "",  // ← パスワードなし
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    
    echo "✅ 接続成功！<br>";
    
    // データベース確認
    $result = $pdo->query("SELECT DATABASE() as db")->fetch();
    echo "📊 データベース: " . $result['db'] . "<br>";
    
    // tasks テーブル確認
    $result = $pdo->query("DESCRIBE tasks")->fetchAll();
    echo "📋 tasks テーブルのカラム数: " . count($result) . "<br>";
    
    // user_id があるか確認
    $has_user_id = false;
    foreach ($result as $col) {
        if ($col['Field'] === 'user_id') {
            $has_user_id = true;
            break;
        }
    }
    
    if ($has_user_id) {
        echo "✅ user_id カラムがあります！<br>";
    } else {
        echo "❌ user_id カラムがありません！<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ エラー: " . $e->getMessage() . "<br>";
}
?>