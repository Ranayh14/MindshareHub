<?php
session_start();
include '../conn.php'; // Hubungkan ke database Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_SESSION['new_password'] ?? null; // Ambil password baru dari sesi
    $confirm_password = $_POST['confirm_password'];

    // Validasi: Pastikan password baru sama dengan konfirmasi
    if ($new_password && $new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hash password baru
        $email = $_SESSION['email']; // Email pengguna dari sesi

        // Update password di database
        $query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if (mysqli_query($conn, $query)) {
            // Setelah berhasil, arahkan ke halaman login
            session_destroy(); // Hapus sesi setelah selesai
            header('Location: login.html'); // Redirect ke halaman login
            exit(); // Pastikan script berhenti
        } else {
            echo "Terjadi kesalahan. Silakan coba lagi.";
        }
    } else {
        echo "Password tidak cocok. Silakan ulangi proses.";
    }
}
?>
