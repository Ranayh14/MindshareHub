<?php
include("../conn.php");

$sql_user = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    roles ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_post = "CREATE OR REPLACE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_path VARCHAR(255) DEFAULT NULL,
    likes INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

$sql_likePost = "CREATE TABLE IF NOT EXISTS post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE(post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

$sql_diarys = "CREATE TABLE IF NOT EXISTS diarys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {   
    if(mysqli_query($conn, $sql_user)) {
        echo "tabel users berhasil dibuat <br>";
    }
}
catch (mysqli_sql_exception) {
    echo "Tabel users gagal dibuat";
}

try {   
    if(mysqli_query($conn, $sql_post)) {
        echo "tabel post berhasil dibuat <br>";
    }
}
catch (mysqli_sql_exception) {
    echo "Tabel post gagal dibuat";
}

try {   
    if(mysqli_query($conn, $sql_diarys)) {
        echo "tabel Diary berhasil dibuat <br>";
    }
}
catch (mysqli_sql_exception) {
    echo "Tabel Diary gagal dibuat";
}

try {   
    if(mysqli_query($conn, $sql_likePost)) {
        echo "tabel like post berhasil dibuat <br>";
    }
}
catch (mysqli_sql_exception) {
    echo "Tabel Diary gagal dibuat";
}
?>