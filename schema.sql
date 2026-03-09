-- =========================================
-- データベース：nyan_task
-- 途中段階でもアプリを動かせる SQL
-- =========================================

-- 1. ユーザー管理テーブル
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- サンプルユーザー
INSERT INTO users (username, password) VALUES
('user1', 'password123'),   -- 実際はハッシュ化推奨
('testuser', 'password123');

-- 2. タスクテーブル
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task VARCHAR(100) NOT NULL,
    task_date DATE,
    is_done TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- サンプルタスク
INSERT INTO tasks (user_id, task, done) VALUES
(1, 'テストタスク', 0),
(1, 'タスクA', 1),
(2, 'サンプルタスク', 0);

-- 3. もしカレンダーや達成率用テーブルが必要なら追加
-- 例：タスク進捗を日付ごとに管理
CREATE TABLE IF NOT EXISTS task_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    progress_date DATE NOT NULL,
    progress_value TINYINT(3) DEFAULT 0,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);