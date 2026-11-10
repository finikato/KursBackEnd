USE appDB;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    theme ENUM('light','dark','colorblind') DEFAULT 'light',
    language ENUM('ru','en') DEFAULT 'ru',
    level INT DEFAULT 1,
    gold INT DEFAULT 0,
    exp INT DEFAULT 0,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NULL,
    data TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) DEFAULT 'application/pdf',
    content LONGBLOB NOT NULL,
    size INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS game_rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('gold','item','exp') NOT NULL,
    amount INT NOT NULL
);

CREATE TABLE IF NOT EXISTS game_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    is_claimed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS game_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    reward_id INT NOT NULL,
    FOREIGN KEY (message_id) REFERENCES game_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES game_rewards(id) ON DELETE CASCADE
);

INSERT IGNORE INTO game_rewards (id, name, type, amount) VALUES 
(1, 'Сундук с золотом', 'gold', 500),
(2, 'Мешок золота', 'gold', 1500),
(3, 'Опыт', 'exp', 100),
(4, 'Эпический предмет', 'item', 1),
(5, 'Зелье здоровья', 'item', 5),
(6, 'Алмазы', 'gold', 100);