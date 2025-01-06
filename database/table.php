<?php
include("../conn.php");

// Kode untuk membuat tabel users
$sql_user = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    roles ENUM('admin', 'user') DEFAULT 'user',
    is_banned BOOLEAN DEFAULT FALSE,
    ban_reason TEXT,
    ban_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255) DEFAULT NULL,
    progress_percentage INT DEFAULT 0
)";

// Kode untuk membuat tabel content
$sql_content = "CREATE TABLE IF NOT EXISTS content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  
    title VARCHAR(100) NOT NULL,
    notes TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Kode untuk membuat tabel posts
$sql_post = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_path VARCHAR(255) DEFAULT NULL,
    likes INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Kode untuk membuat tabel post_likes
$sql_likePost = "CREATE TABLE IF NOT EXISTS post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE(post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Kode untuk membuat tabel notifications
$sql_notifications = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    notes TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Kode untuk membuat tabel diarys
$sql_diarys = "CREATE TABLE IF NOT EXISTS diarys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,  -- Menambahkan kolom user_id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  -- Menambahkan constraint foreign key
)";

$sql_report ="CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    reported_by INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (reported_by) REFERENCES users(id)
)";

$sql_comments = "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Eksekusi query dengan try-catch
try {
    if (mysqli_query($conn, $sql_user)) {
        echo "Tabel users berhasil dibuat.<br>";
    } else {
        echo "Tabel users gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_content)) {
        echo "Tabel content berhasil dibuat.<br>";
    } else {
        echo "Tabel content gagal dibuat: " . mysqli_error($conn) . "<br>";
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

    if (mysqli_query($conn, $sql_notifications)) {
        echo "Tabel notifications berhasil dibuat.<br>";
    } else {
        echo "Tabel notifications gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_diarys)) {
        echo "Tabel diarys berhasil dibuat.<br>";
    } else {
        echo "Tabel diarys gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_report)) {
        echo "Tabel reports berhasil dibuat.<br>";
    } else {
        echo "Tabel reports gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_comments)) {
        echo "Tabel comments berhasil dibuat.<br>";
    } else {
        echo "Tabel comments gagal dibuat: " . mysqli_error($conn) . "<br>";
    }
} catch (mysqli_sql_exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>
