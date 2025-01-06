<?php
session_start();
include('conn.php'); // Pastikan path sesuai

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'];
$notes = $data['notes'];

// Masukkan konten ke dalam tabel
$insert_content_sql = "INSERT INTO content (title, notes) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $insert_content_sql);
mysqli_stmt_bind_param($stmt, "ss", $title, $notes);
mysqli_stmt_execute($stmt);

// Dapatkan semua pengguna untuk notifikasi
$sql = "SELECT id FROM users";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $user_id = $row['id'];

        // Masukkan notifikasi ke dalam tabel
        $insert_notification_sql = "INSERT INTO notifications (user_id, title, notes) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_notification_sql);
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $notes);
        mysqli_stmt_execute($stmt);
    }
    http_response_code(200); // Berhasil
} else {
    http_response_code(500); // Gagal
}

// Menutup koneksi
mysqli_close($conn);
?>