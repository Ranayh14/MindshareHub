<?php
// Koneksi ke database
include("../conn.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Ambil data dari form
        $email = $conn->real_escape_string(trim($_POST['email']));
        $password = trim($_POST['password']);

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
                    // Verifikasi password
                    if (password_verify($password, $user['pass'])) {
                        // Simpan data pengguna ke dalam session
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['roles'] = $user['roles'];
                        $_SESSION['user_id'] = $user['id'];

                        // Redirect berdasarkan role
                        if ($user['roles'] === 'admin') {
                            header("Location: /DashboardAdmin/DashboardAdmin.html");
                        } elseif ($user['roles'] === 'user') {
                            header("Location: /Dashboard/Dashboard.php");
                        } else {
                            // Jika role tidak valid
                            $error = "Peran tidak dikenali.";
                        }
                        exit();
                    } else {
                        $error = "Password salah!";
                    }
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
    header("Location: /login/login.html?error=" . urlencode($error));
    exit();
}