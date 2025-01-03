<?php
include("../conn.php");

// Membuat tabel users
$sql_user = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    roles ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Membuat tabel posts
$sql_post = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_path VARCHAR(255) DEFAULT NULL,
    likes INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Membuat tabel post_likes
$sql_likePost = "CREATE TABLE IF NOT EXISTS post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE(post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Membuat tabel diarys (penyesuaian dengan user_id)
$sql_diarys = "CREATE TABLE IF NOT EXISTS diarys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,  -- Menambahkan kolom user_id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  -- Menambahkan constraint foreign key
)";

// Eksekusi query dengan try-catch
try {
    if (mysqli_query($conn, $sql_user)) {
        echo "Tabel users berhasil dibuat.<br>";
    } else {
        echo "Tabel users gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_post)) {
        echo "Tabel posts berhasil dibuat.<br>";
    } else {
        echo "Tabel posts gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_likePost)) {
        echo "Tabel post_likes berhasil dibuat.<br>";
    } else {
        echo "Tabel post_likes gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_diarys)) {
        echo "Tabel diarys berhasil dibuat.<br>";
    } else {
        echo "Tabel diarys gagal dibuat: " . mysqli_error($conn) . "<br>";
    }
} catch (mysqli_sql_exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>
