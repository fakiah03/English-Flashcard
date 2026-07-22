<?php
// config/setup.php

$host = 'localhost';
$port = '3307';
$dbname = 'english_flashcard';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server (without specifying database)
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbname` created or already exists.<br>";

    // Connect to the specific database
    $pdo->exec("USE `$dbname`");

    // Array of table creation queries
    $tables = [
        "users" => "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('user', 'admin') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ",
        "vocabulary" => "
            CREATE TABLE IF NOT EXISTS vocabulary (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                word VARCHAR(100) NOT NULL,
                meaning TEXT NOT NULL,
                example TEXT,
                pronunciation VARCHAR(100),
                category VARCHAR(50) DEFAULT 'General',
                review_level INT DEFAULT 0,
                next_review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
                mastered BOOLEAN DEFAULT FALSE,
                forgot_count INT DEFAULT 0,
                last_forgotten DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ",
        "vocabulary_history" => "
            CREATE TABLE IF NOT EXISTS vocabulary_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                vocab_id INT NOT NULL,
                user_id INT NOT NULL,
                event_type VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (vocab_id) REFERENCES vocabulary(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ",
        "favorites" => "
            CREATE TABLE IF NOT EXISTS favorites (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                vocab_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (vocab_id) REFERENCES vocabulary(id) ON DELETE CASCADE,
                UNIQUE KEY user_vocab (user_id, vocab_id)
            ) ENGINE=InnoDB;
        ",
        "quiz_history" => "
            CREATE TABLE IF NOT EXISTS quiz_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                score INT NOT NULL,
                total INT NOT NULL,
                type VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ",
        "learning_streak" => "
            CREATE TABLE IF NOT EXISTS learning_streak (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                current_streak INT DEFAULT 0,
                max_streak INT DEFAULT 0,
                last_studied_date DATE NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ",
        "notifications" => "
            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                message TEXT NOT NULL,
                is_read BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        "
    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "Table `$name` created or already exists.<br>";
    }

    echo "<br><strong>Database setup completed successfully.</strong>";
    echo "<br><a href='../index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
