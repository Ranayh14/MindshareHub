<?php
// Koneksi ke database
include("../conn.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Ambil data dari form
        $email = $conn->real_escape_string(trim($_POST['email']));
        // Query untuk memeriksa pengguna berdasarkan email
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Jika pengguna ditemukan
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                $email = $_POST['email'];
                $otp = rand(100000, 999999); // Generate 6-digit OTP
                $_SESSION['otp'] = $otp;
                $_SESSION['email'] = $email;

                $subject = "Kode OTP untuk Reset Password";
                $message = "Kode OTP Anda adalah: $otp. Jangan bagikan kode ini dengan siapa pun.";
                $headers = "From: no-reply@yourwebsite.com";

                if (mail($email, $subject, $message, $headers)) {
                    header('Location: verify_otp.html');
                } else {
                    echo "Gagal mengirim email. Coba lagi.";
                }
            } else {
                $error = "Email tidak ditemukan!";
            }
            $stmt->close();
        } else {
            $error = "Terjadi kesalahan pada server. Silakan coba lagi.";
        }
    } catch (Exception $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Tampilkan pesan error jika ada
if (isset($error)) {
    echo "<script>alert('$error'); window.history.back();</script>";
}
?>
