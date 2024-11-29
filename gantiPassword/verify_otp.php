<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        echo "Verifikasi berhasil. Silakan reset password Anda.";
        header('Location: gantiPassword.html');
    } else {
        echo "Kode OTP salah. Coba lagi.";
    }
}
?>
