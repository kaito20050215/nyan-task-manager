-- =========================================
-- データベース：nyan_task
-- にゃんタスク管理アプリ用 SQL
-- =========================================

-- データベース作成
CREATE DATABASE IF NOT EXISTS nyan_task;
USE nyan_task;

-- =========================================
-- users テーブル
-- =========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- デモユーザー
INSERT INTO users (username, password) VALUES
('demo', '$2y$10$examplehashxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

-- =========================================
-- tasks テーブル
-- =========================================
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task VARCHAR(100) NOT NULL,
    task_date DATE,
    is_done TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- サンプルタスク
INSERT INTO tasks (user_id, task, task_date, is_done) VALUES
(1, 'テストタスク', '2026-03-09', 0),
(1, '買い物に行く', '2026-03-10', 0),
(1, 'PHPの勉強', '2026-03-11', 1);