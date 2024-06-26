CREATE TABLE IF NOT EXISTS folders
(
    id         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id    INT UNSIGNED DEFAULT NULL,
    title      VARCHAR(255) NOT NULL,
    created_at DATETIME     DEFAULT NOW(),
    updated_at DATETIME     DEFAULT NOW(),

    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    );