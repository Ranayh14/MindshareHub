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

$sql_post = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    user_username VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
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
?>