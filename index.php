<?php
/* ========= 接続 ========= */

$pdo = new PDO(
    "mysql:host=localhost;dbname=nyan_task;charset=utf8",
    "root",
    ""
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


/* ========= CRUD ========= */

// 追加
if (isset($_POST['add'])) {

    $task = trim($_POST['task']);
    $date = $_POST['task_date'];

    if ($task && $date) {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (task, task_date, is_done)
            VALUES (:t, :d, 0)
        ");
        $stmt->execute([
            't' => $task,
            'd' => $date
        ]);
    }

    header("Location: index.php");
    exit;
}


// 削除
if (isset($_POST['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM tasks
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => $_POST['delete']
    ]);

    header("Location: index.php");
    exit;
}


// 完了切替
if (isset($_POST['toggle'])) {

    $stmt = $pdo->prepare("
        UPDATE tasks
        SET is_done = NOT is_done
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => $_POST['toggle']
    ]);

    header("Location: index.php");
    exit;
}


// 更新
if (isset($_POST['update'])) {

    $stmt = $pdo->prepare("
        UPDATE tasks
        SET task = :t,
            task_date = :d
        WHERE id = :id
    ");

    $stmt->execute([
        't'  => $_POST['edit_task'],
        'd'  => $_POST['edit_date'],
        'id' => $_POST['update']
    ]);

    header("Location: index.php");
    exit;
}


/* ========= データ取得 ========= */

$today = date('Y-m-d');

// 今日
$stmt = $pdo->prepare("
    SELECT *
    FROM tasks
    WHERE task_date = :today
    ORDER BY is_done ASC
");
$stmt->execute(['today' => $today]);
$todayTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 今日以外
$stmt = $pdo->prepare("
    SELECT *
    FROM tasks
    WHERE task_date <> :today
    ORDER BY task_date ASC, is_done ASC
");
$stmt->execute(['today' => $today]);
$otherTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 表示用統合
$tasks = array_merge($todayTasks, $otherTasks);


/* ========= 今日の達成率 ========= */

$total = count($todayTasks);
$done  = 0;

foreach ($todayTasks as $t) {
    if ($t['is_done']) {
        $done++;
    }
}

$rate = $total ? round(($done / $total) * 100) : 0;


/* ========= カレンダー ========= */

$year  = $_GET['year']  ?? date('Y');
$month = $_GET['month'] ?? date('m');

$firstDay    = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDay);
$startDay    = date('w', $firstDay);

$stmt = $pdo->prepare("
    SELECT *
    FROM tasks
    WHERE YEAR(task_date) = :y
      AND MONTH(task_date) = :m
");
$stmt->execute([
    'y' => $year,
    'm' => $month
]);

$calTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$taskMap = [];

foreach ($calTasks as $t) {
    $taskMap[$t['task_date']][] = $t;
}

$edit_id = $_GET['edit'] ?? null;


/* ========= 月移動計算 ========= */

$prevMonth = $month - 1;
$prevYear  = $year;
$nextMonth = $month + 1;
$nextYear  = $year;

if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>にゃんタスク管理</title>

<style>
body{
    background:#fff6f9;
    font-family:"Hiragino Maru Gothic Pro",sans-serif;
}

.wrapper{
    display:flex;
    gap:30px;
    padding:30px;
}

.left{
    width:45%;
    background:#fff;
    padding:20px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(255,182,193,0.3);
}

.right{
    width:55%;
}

h1{
    color:#ff7aa2;
    text-align:center;
}

input{
    padding:6px;
    border-radius:15px;
    border:2px solid #ffc1d3;
    margin:3px;
}

button{
    padding:6px 10px;
    border:none;
    border-radius:15px;
    cursor:pointer;
}

.add-btn{background:#ff9eb5;color:white;}
.toggle-btn{background:#a8e6cf;}
.delete-btn{background:#ccc;}
.edit-btn{background:#ffd166;}
.save-btn{background:#06d6a0;color:white;}

ul{
    list-style:none;
    padding:0;
}

li{
    background:#ffe3ec;
    margin:8px 0;
    padding:10px;
    border-radius:15px;
    display:flex;
    justify-content:space-between;
}

.done{
    text-decoration:line-through;
    color:#888;
    background:#f0f0f0;
}

.date{
    font-size:12px;
    color:#666;
}

.today-task{
    border:3px solid #ff7aa2;
    box-shadow:0 0 15px rgba(255,122,162,0.4);
}

.calendar{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:5px;
}

.day,.week{
    background:#ffe3ec;
    padding:8px;
    border-radius:15px;
    min-height:90px;
    font-size:12px;
}

.week{
    background:#ff9eb5;
    color:white;
    font-weight:bold;
}

.today{
    background:#ffd166 !important;
}

.task{
    margin-top:4px;
    font-size:11px;
}

.progress-bar{
    background:#ffd6e0;
    height:15px;
    border-radius:20px;
    overflow:hidden;
    margin-bottom:10px;
}

.progress-inner{
    background:#ff7aa2;
    height:100%;
    transition:0.3s;
}
</style>
</head>

<body>
<div class="wrapper">

<!-- 左：タスク -->
<div class="left">

<h1>🐾 今日のタスク</h1>

<p>達成率：<?= $rate ?>%</p>

<div class="progress-bar">
    <div class="progress-inner" style="width:<?= $rate ?>%;"></div>
</div>

<form method="POST">
    <input type="text" name="task" required>
    <input type="date" name="task_date" value="<?= $today ?>" required><br>
    <button type="submit" name="add" class="add-btn">追加</button>
</form>

<ul>
<?php foreach ($tasks as $task): ?>

<?php $isToday = ($task['task_date'] === $today); ?>

<li class="<?= $task['is_done'] ? 'done' : '' ?> <?= $isToday ? 'today-task' : '' ?>">

    <div>
        <?php if ($edit_id == $task['id']): ?>

            <form method="POST">
                <input type="text" name="edit_task"
                       value="<?= htmlspecialchars($task['task']) ?>">
                <input type="date" name="edit_date"
                       value="<?= $task['task_date'] ?>">
                <button name="update"
                        value="<?= $task['id'] ?>"
                        class="save-btn">保存</button>
            </form>

        <?php else: ?>

            🐱 <?= htmlspecialchars($task['task']) ?>
            <div class="date">
                📅 <?= $task['task_date'] ?>
            </div>

        <?php endif; ?>
    </div>

    <div>
        <form method="POST" style="display:inline;">
            <button name="toggle"
                    value="<?= $task['id'] ?>"
                    class="toggle-btn">✔</button>
        </form>

        <a href="?edit=<?= $task['id'] ?>">
            <button type="button" class="edit-btn">✏</button>
        </a>

        <form method="POST" style="display:inline;">
            <button name="delete"
                    value="<?= $task['id'] ?>"
                    class="delete-btn">🗑</button>
        </form>
    </div>

</li>

<?php endforeach; ?>
</ul>

</div>


<!-- 右：カレンダー -->
<div class="right">

<h1 style="display:flex;justify-content:center;align-items:center;gap:15px;">
    <a href="?year=<?= $prevYear ?>&month=<?= sprintf('%02d',$prevMonth) ?>">◀</a>
    <?= $year ?>年 <?= $month ?>月
    <a href="?year=<?= $nextYear ?>&month=<?= sprintf('%02d',$nextMonth) ?>">▶</a>
</h1>

<div class="calendar">

<?php
$week = ['日','月','火','水','木','金','土'];

foreach ($week as $w) {
    echo "<div class='week'>{$w}</div>";
}

for ($i = 0; $i < $startDay; $i++) {
    echo "<div class='day'></div>";
}

for ($d = 1; $d <= $daysInMonth; $d++) {

    $date = sprintf("%04d-%02d-%02d", $year, $month, $d);
    $todayClass = ($date == $today) ? 'today' : '';

    echo "<div class='day {$todayClass}'>";
    echo "<b>{$d}</b>";

    if (isset($taskMap[$date])) {
        foreach ($taskMap[$date] as $t) {
            $doneClass = $t['is_done'] ? 'done' : '';
            echo "<div class='task {$doneClass}'>🐱"
                . htmlspecialchars($t['task'])
                . "</div>";
        }
    }

    echo "</div>";
}
?>

</div>
</div>

</div>
</body>
</html>