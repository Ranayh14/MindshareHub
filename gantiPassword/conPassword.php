<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['new_password'] = $_POST['password']; // Simpan password baru di sesi
    header('Location: conResetPassword.html'); // Arahkan ke halaman konfirmasi
    exit();
}
?>
