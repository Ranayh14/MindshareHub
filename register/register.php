<?php
// Koneksi ke database
include("../conn.php");

// Fungsi untuk generate username otomatis
function generateUsername($conn) {
    do {
        $number = rand(100, 999999);
        $username = "user" . $number;

        // Cek apakah username unik
        $query = "SELECT 1 FROM users WHERE username = '$username'";
        $result = $conn->query($query);
    } while ($result->num_rows > 0); // Ulangi jika username sudah ada

    return $username;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try{
        // Validasi checkbox
        if (!isset($_POST['terms']) || $_POST['terms'] !== "accepted") {
            echo "<script>alert('Anda harus menyetujui syarat dan ketentuan.'); window.history.back();</script>";
            exit;
        }

        // Ambil data dari form
        $email = $conn->real_escape_string(trim($_POST['email']));
        $password = trim($_POST['password']);
        $conpassword = trim($_POST['conpassword']);

        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Email tidak valid.'); window.history.back();</script>";
            exit;
        }

        // Validasi password
        if (strlen($password) < 8) {
            echo "<script>alert('Password harus memiliki panjang minimal 8 karakter.'); window.history.back();</script>";
            exit;
        }

        // Validasi konfirmasi password
        if ($password !== $conpassword) {
            echo "<script>alert('Password dan konfirmasi password tidak cocok.'); window.history.back();</script>";
            exit;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Tentukan role berdasarkan email
        $role = (strpos($email, '@admin.mindsharehub.ac.id') !== false) ? 'admin' : 'user';

        // Generate username
        if ($role === 'user') {
            // Untuk user biasa, generate username otomatis
            $username = generateUsername($conn);
        } else {
            // Untuk admin, ambil bagian sebelum "@"
            $username = substr($email, 0, strpos($email, '@'));
        }

        // Simpan data ke database
        $query = "INSERT INTO users (username, email, pass, roles) VALUES ('$username', '$email', '$hashed_password', '$role')";
        if ($conn->query($query) === TRUE) {
            // Tampilkan modal pemberitahuan
            echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Password Terupdate</title>
                <script src='https://cdn.tailwindcss.com'></script>
                <script>
                    // Alihkan pengguna setelah beberapa detik
                    setTimeout(() => {
                        window.location.href = '/login/login.html'; 
                    }, 3000); // 3 detik
                </script>
                <style>
                    @tailwind base;
                    @tailwind components;
                    @tailwind utilities;

                    @layer utilities {
                    .bg-customPurple {
                        background-color: rgba(43, 27, 84, 1);
                    }
                    }

                    .modal {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(0, 0, 0, 0.6);
                    }
                </style>
            </head>
            <body class='bg-customPurple h-screen flex items-center justify-center'>
                <div class='modal flex items-center'>
                    <div class='bg-white p-6 rounded-lg shadow-lg text-center'>
                        <div class='w-32 h-32 mx-auto mb-3 flex items-center justify-center'>
                            <img src='/Asset/Logo MindsahreHub.png' alt='Logo' class='w-32 h-32'>
                        </div>
                        <h2 class='text-2xl font-semibold text-green-600'>Registrasi berhasil! Username Anda: $username</h2>
                        <p class='mt-4 text-gray-600'>Anda akan diarahkan ke halaman login dalam beberapa detik...</p>
                    </div>
                </div>
            </body>
            </html>";
            exit();
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $conn->error . "'); window.history.back();</script>";
        }
    } catch (Exception $e) {
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            echo "<script>alert('Email sudah terdaftar. Gunakan email lain atau lakukan login.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
    }
    
}

// Tutup koneksi database
$conn->close();
?>
