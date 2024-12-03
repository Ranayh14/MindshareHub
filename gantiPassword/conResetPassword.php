<?php
session_start();
include '../conn.php'; // Hubungkan ke database Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data password baru dan konfirmasi password dari form
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi: Periksa apakah password dan confirm_password sama
    if ($password === $confirm_password) {
        // Hash password baru
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Ambil email pengguna dari sesi
        $email = $_SESSION['email']; // Pastikan sesi email sudah disimpan sebelumnya

        // Perbarui password di database
        $query = "UPDATE users SET pass = '$hashed_password' WHERE email = '$email'";
        if (mysqli_query($conn, $query)) {
            session_destroy();
            header('Location: /login/login.html');
            exit();
        } else {
            echo "Terjadi kesalahan saat memperbarui password. Silakan coba lagi.";
        }
    } else {
        // Jika password tidak cocok
        echo "Password baru dan konfirmasi password tidak cocok.";
    }
}
?>
