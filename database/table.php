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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Kode untuk membuat tabel reports
$sql_report = "CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    reported_by INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('aktif', 'selesai', 'ditolak') DEFAULT 'aktif',
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (reported_by) REFERENCES users(id)
)";

// Kode untuk membuat tabel comments
$sql_comments = "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT NULL,
    comment TEXT NOT NULL,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
)";

// Kode untuk membuat tabel comment_likes
$sql_commentlikes = "CREATE TABLE IF NOT EXISTS comment_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (comment_id, user_id)
)";

$sql_reportComments = " CREATE TABLE IF NOT EXISTS comment_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        comment_id INT NOT NULL,    
        user_id INT NOT NULL,
        reason VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'Menunggu',  -- Menambahkan kolom status dengan nilai default 'Menunggu'
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
";


// Kode untuk membuat tabel audio_notes
$sql_audioNotes = "CREATE TABLE IF NOT EXISTS audio_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

// Perbarui semua profile_picture yang NULL menjadi 'pp1.png'
$sql_default_pp = "UPDATE users SET profile_picture = 'pp1.png' WHERE profile_picture IS NULL";


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

    if (mysqli_query($conn, $sql_commentlikes)) {
        echo "Tabel comment_likes berhasil dibuat.<br>";
    } else {
        echo "Tabel comment_likes gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_reportComments)) {
        echo "Tabel comment_reports berhasil dibuat.<br>";
    } else {
        echo "Tabel comment_reports gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_audioNotes)) {
        echo "Tabel audio_notes berhasil dibuat.<br>";
    } else {
        echo "Tabel audio_notes gagal dibuat: " . mysqli_error($conn) . "<br>";
    }

    if (mysqli_query($conn, $sql_default_pp)) {
        echo "Profile pictures yang NULL berhasil diupdate ke pp1.png.<br>";
    } else {
        echo "Gagal memperbarui profile pictures: " . mysqli_error($conn) . "<br>";
    }
} catch (mysqli_sql_exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>
