<?php
/**
 * データベース接続テスト
 * ブラウザで http://localhost/dashboard/task_mane/test_db.php にアクセス
 */

require __DIR__ . "/db.php";

echo "<h2>🔧 データベース接続テスト</h2>";

// 1️⃣ 接続確認
echo "<h3>1️⃣ 接続確認</h3>";
if ($pdo) {
    echo "✅ データベースに接続しました！<br>";
} else {
    echo "❌ 接続失敗<br>";
    exit;
}

// 2️⃣ データベース名確認
echo "<h3>2️⃣ データベース確認</h3>";
$stmt = $pdo->query("SELECT DATABASE() as db_name");
$result = $stmt->fetch();
echo "✅ 現在のデータベース: <strong>" . $result['db_name'] . "</strong><br>";

// 3️⃣ テーブル確認
echo "<h3>3️⃣ テーブル一覧</h3>";
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "テーブル: " . implode(", ", $tables) . "<br>";

// 4️⃣ tasks テーブルの構造確認
echo "<h3>4️⃣ tasks テーブルの構造</h3>";
$stmt = $pdo->query("DESCRIBE tasks");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr style='background: #ccc;'>";
echo "<th style='padding: 8px;'>Field</th>";
echo "<th style='padding: 8px;'>Type</th>";
echo "<th style='padding: 8px;'>Null</th>";
echo "<th style='padding: 8px;'>Key</th>";
echo "<th style='padding: 8px;'>Default</th>";
echo "<th style='padding: 8px;'>Extra</th>";
echo "</tr>";

foreach ($columns as $col) {
    echo "<tr>";
    echo "<td style='padding: 8px;'><strong>" . $col['Field'] . "</strong></td>";
    echo "<td style='padding: 8px;'>" . $col['Type'] . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Null'] === 'YES' ? '✅' : '❌') . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Key'] ?? '-') . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Default'] ?? '-') . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Extra'] ?? '-') . "</td>";
    echo "</tr>";
}

echo "</table>";

// 5️⃣ users テーブル確認
echo "<h3>5️⃣ users テーブルの構造</h3>";
$stmt = $pdo->query("DESCRIBE users");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr style='background: #ccc;'>";
echo "<th style='padding: 8px;'>Field</th>";
echo "<th style='padding: 8px;'>Type</th>";
echo "<th style='padding: 8px;'>Null</th>";
echo "<th style='padding: 8px;'>Key</th>";
echo "<th style='padding: 8px;'>Default</th>";
echo "</tr>";

foreach ($columns as $col) {
    echo "<tr>";
    echo "<td style='padding: 8px;'><strong>" . $col['Field'] . "</strong></td>";
    echo "<td style='padding: 8px;'>" . $col['Type'] . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Null'] === 'YES' ? '✅' : '❌') . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Key'] ?? '-') . "</td>";
    echo "<td style='padding: 8px;'>" . ($col['Default'] ?? '-') . "</td>";
    echo "</tr>";
}

echo "</table>";

// 6️⃣ データ確認
echo "<h3>6️⃣ データ確認</h3>";

$userCount = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
echo "✅ users テーブルのレコード数: <strong>" . $userCount['count'] . "</strong><br>";

$taskCount = $pdo->query("SELECT COUNT(*) as count FROM tasks")->fetch();
echo "✅ tasks テーブルのレコード数: <strong>" . $taskCount['count'] . "</strong><br>";

echo "<h3>✅ すべてのテストが完了しました！</h3>";
?>