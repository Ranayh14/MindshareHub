<?php
session_start();
include '../conn.php'; // Hubungkan ke database Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data password baru dan konfirmasi password dari form
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi: Periksa apakah password dan confirm_password sama
    if ($password === $confirm_password) {
        // Validasi kekuatan password
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password)) {
            echo "Password harus minimal 8 karakter, mengandung huruf besar dan angka.";
            exit();
        }

        // Hash password baru
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $email = $_SESSION['email']; // Email dari sesi

        // Perbarui password di database menggunakan prepared statement
        $query = "UPDATE users SET pass = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $hashed_password, $email);

        if ($stmt->execute()) {
            session_destroy(); // Hancurkan sesi
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
                        <h2 class='text-2xl font-semibold text-green-600'>Password Berhasil Diupdate!</h2>
                        <p class='mt-4 text-gray-600'>Anda akan diarahkan ke halaman login dalam beberapa detik...</p>
                    </div>
                </div>
            </body>
            </html>";
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
