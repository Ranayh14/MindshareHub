<?php
// Mengimpor koneksi database
include("../conn.php");

// Menghitung total pengguna
$total_pengguna_query = "SELECT COUNT(*) as total FROM users";
$result = mysqli_query($conn, $total_pengguna_query);
$row = mysqli_fetch_assoc($result);
$total_pengguna = $row['total'];

// Menghitung total pengguna baru
$total_pengguna_baru_query = "SELECT COUNT(*) as total FROM users WHERE created_at >= CURDATE()";
$result_baru = mysqli_query($conn, $total_pengguna_baru_query);
$row_baru = mysqli_fetch_assoc($result_baru);
$total_pengguna_baru = $row_baru['total'];

// Menutup koneksi
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Forum Anonim</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
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
                <a href="DashboardAdmin.php" class="flex items-center space-x-2 p-2 rounded-lg bg-[#5865F2] text-white">
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
                <a href="KelolaKomunitas.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span>Kelola Komunitas</span>
                </a>
                <a href="LaporanMasuk.html" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span>Laporan Masuk</span>
                </a>
                <a href="LogoutAdmin.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold">Hallo! Raditya</h2>
            </div>

            <!-- Stats Cards -->
            <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#5865F2] to-[#3b4c8f] p-6 rounded-lg shadow-lg cursor-pointer" onclick="showModal('totalPenggunaBaru')">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Total Pengguna</h3>
                        <span class="text-white">
                            <i class="fas fa-users w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white"><?php echo $total_pengguna; ?></p>
                </div>

                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#3b82f6] to-[#1e40af] p-6 rounded-lg shadow-lg cursor-pointer" onclick="showModal('totalPenggunaBaru')">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Total Pengguna Baru</h3>
                        <span class="text-white">
                            <i class="fas fa-user-plus w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white"><?php echo $total_pengguna_baru; ?></p>
                </div>

                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#f04747] to-[#d32f2f] p-6 rounded-lg shadow-lg cursor-pointer" onclick="showModal('laporanAktif')">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Laporan Aktif</h3>
                        <span class="text-white">
                            <i class="fas fa-exclamation-circle w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white">12</p>
                </div>

                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#34D399] to-[#059669] p-6 rounded-lg shadow-lg cursor-pointer" onclick="showModal('masalahTerselesaikan')">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Masalah Terselesaikan</h3>
                        <span class="text-white">
                            <i class="fas fa-check-circle w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white">5</p>
                </div>
            </div>

            <!-- Container for Chart -->
            <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-8">
                <h3 class="text-xl font-bold text-gray-200 mb-4">Grafik Aktivitas</h3>
                <canvas id="activityChart" width="30" height="5"></canvas>
            </div>

            <!-- Recent Posts Section -->
            <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-gray-200 mb-4">Postingan Terbaru</h3>
                <table class="w-full text-left text-gray-100 border-collapse">
                    <thead>
                        <tr class="border-b border-gray-600">
                            <th class="pb-3">ID Pengguna</th>
                            <th class="pb-3">Judul Postingan</th>
                            <th class="pb-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-600 hover:bg-gray-700 transition-colors duration-200">
                            <td class="py-3">User1234</td>
                            <td class="py-3">Postingan Pertama</td>
                            <td class="py-3">2024-12-20</td>
                        </tr>
                        <tr class="border-b border-gray-600 hover:bg-gray-700 transition-colors duration-200">
                            <td class="py-3">User5678</td>
                            <td class="py-3">Postingan Kedua</td>
                            <td class="py-3">2024-12-19</td>
                        </tr>
                        <tr class="border-b border-gray-600 hover:bg-gray-700 transition-colors duration-200">
                            <td class="py-3">User9101</td>
                            <td class="py-3">Postingan Ketiga</td>
                            <td class="py-3">2024-12-18</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white rounded-lg p-4 max-w-md w-full">
            <h3 id="modalTitle" class="text-lg font-bold mb-2"></h3>
            <p id="modalContent" class="text-gray-700 mb-4"></p>
            <button onclick="closeModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Tutup</button>
        </div>
    </div>

    <script>
        function showModal(type) {
            const modal = document.getElementById('modal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('modalContent');

            switch(type) {
                case 'totalPenggunaBaru':
                    title.textContent = 'Total Pengguna Baru Hari Ini';
                    content.textContent = 'Ada <?php echo $total_pengguna_baru; ?> pengguna baru yang bergabung hari ini.';
                    break;
                case 'laporanAktif':
                    title.textContent = 'Laporan Aktif Hari Ini';
                    content.textContent = 'Saat ini ada 12 laporan aktif yang perlu ditangani.';
                    break;
                case 'masalahTerselesaikan':
                    title.textContent = 'Masalah Terselesaikan Hari Ini';
                    content.textContent = 'Sebanyak 5 masalah telah diselesaikan hari ini.';
                    break;
                default:
                    title.textContent = '';
                    content.textContent = '';
            }
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.add('hidden');
        }

        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                datasets: [
                    {
                        label: 'Postingan Bulanan',
                        data: [12, 19, 3, 5, 2, 3, 7],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                    },
                    {
                        label: 'Total Pengguna',
                        data: [100, 120, 130, 140, 150, 150, 150],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>