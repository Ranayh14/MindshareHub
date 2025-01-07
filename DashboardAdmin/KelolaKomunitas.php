<?php
// Menghubungkan ke database
include("../conn.php");

// Proses Logout
if (isset($_POST['logout'])) {
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari database
$sql = "SELECT username FROM users";
$result = mysqli_query($conn, $sql);

// Mulai output HTML
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komunitas - Forum Anonim</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

        .user-item {
            transition: background-color 0.3s, transform 0.2s;
        }

        .user-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
        }
        
        .user-item:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body class="bg-[#2f3136] text-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#202225] p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-white">PusatAdmin</h1>
            </div>
            <nav class="space-y-4">
                <a href="DashboardAdmin.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-home w-5 h-5"></i>
                    <span>Beranda</span>
                </a>
                <a href="KelolaPengguna.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-user-friends w-5 h-5"></i>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="KelolaKonten.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-file-alt w-5 h-5"></i>
                    <span>Kelola Konten</span>
                </a>
                <a href="KelolaKomunitas.php" class="flex items-center space-x-2 p-2 rounded-lg bg-[#5865F2] text-white">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span>Kelola Komunitas</span>
                </a>
                <a href="LaporanMasuk.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span>Laporan Masuk</span>
                </a>
                <button id="logoutButton" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </button>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold">Kelola Komunitas</h2>
            </div>

            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold">Kelola Posting</h3>
                <table class="min-w-full table-auto text-left mt-4">
                    <thead class="bg-[#202225]">
                        <tr>
                            <th class="px-4 py-2 text-gray-400">No</th>
                            <th class="px-4 py-2 text-gray-400">Judul Posting</th>
                            <th class="px-4 py-2 text-gray-400">Pengguna</th>
                            <th class="px-4 py-2 text-gray-400">Tanggal</th>
                            <th class="px-4 py-2 text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="postingTable">
                        <tr class="hover:bg-[#35393f]">
                            <td class="px-4 py-2">1</td>
                            <td class="px-4 py-2">Posting Pertama</td>
                            <td class="px-4 py-2">User1234</td>
                            <td class="px-4 py-2">2024-11-01</td>
                            <td class="px-4 py-2">
                                <button class="text-yellow-500 hover:text-yellow-300" onclick="viewPost(1)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-300 ml-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <!-- Tambahkan lebih banyak baris di sini -->
                    </tbody>
                </table>
            </div>

            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg mt-6">
                <h3 class="text-lg font-semibold">Daftar Pengguna</h3>
                <input type="text" id="userSearch" class="mt-2 p-2 w-full bg-[#202225] text-gray-100 rounded" placeholder="Cari pengguna..." onkeyup="filterUsers()">
                <ul id="userList" class="mt-4 text-gray-300">
                    <?php
                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<li class="user-item">' . htmlspecialchars($row['username']) . '</li>';
                            }
                        } else {
                            echo '<li>Tidak ada pengguna ditemukan.</li>';
                        }
                    } else {
                        echo '<li>Kesalahan dalam mengambil data.</li>';
                    }

                    // Menutup koneksi
                    mysqli_close($conn);
                    ?>
                </ul>
            </div>

            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg mt-6">
                <h3 class="text-lg font-semibold">Komunitas</h3>
                <p class="text-gray-300">Daftar postingan dalam komunitas akan ditampilkan di sini.</p>
                <!-- Tambahkan daftar postingan komunitas di sini -->
            </div>
        </main>
    </div>

    <!-- Modal Konfirmasi Logout -->
    <div id="logoutModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-[#202225] p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Anda yakin ingin logout?</h2>
            <p class="mb-6">Semua sesi aktif akan diakhiri.</p>
            <div class="flex space-x-4">
                <form method="POST" action="LogoutAdmin.php">
                    <button type="submit" name="logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-400 transition duration-200">
                        Logout
                    </button>
                </form>
                <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        const logoutButton = document.getElementById('logoutButton');
        const logoutModal = document.getElementById('logoutModal');

        // Menampilkan modal ketika tombol logout diklik
        logoutButton.addEventListener('click', (event) => {
            event.preventDefault(); // Mencegah navigasi langsung
            logoutModal.classList.remove('hidden'); // Tampilkan modal
        });

        // Menutup modal
        function closeModal() {
            logoutModal.classList.add('hidden'); // Sembunyikan modal
        }

        function viewPost(postId) {
            alert(`Menampilkan detail postingan dengan ID: ${postId}`);
            // Alihkan ke halaman detail postingan pengguna di sini
        }

        function filterUsers() {
            const searchInput = document.getElementById('userSearch').value.toLowerCase();
            const users = document.querySelectorAll('.user-item');
            
            users.forEach(user => {
                const userName = user.textContent.toLowerCase();
                user.style.display = userName.includes(searchInput) ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>