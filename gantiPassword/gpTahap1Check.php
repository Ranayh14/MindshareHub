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

                // Periksa apakah pengguna dibanned
                if ($user['is_banned']) {
                    // Ambil alasan banned
                    $banReason = $user['ban_reason'];
                    $_SESSION['ban_reason'] = $banReason;  // Simpan alasan dalam session
                    $error = "Akun Anda telah dibanned. Alasan: " . htmlspecialchars($banReason);
                } else {
                    $_SESSION['email'] = $email;
                    header("Location: /gantiPassword/gpTahap2.php");
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
    header("Location: /gantiPassword/gpTahap1.html?error=" . urlencode($error));
}