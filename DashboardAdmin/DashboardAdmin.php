<?php
// Include the database connection
include("../conn.php");

// Calculate total users
$total_pengguna_query = "SELECT COUNT(*) as total FROM users";
$result = mysqli_query($conn, $total_pengguna_query);
$row = mysqli_fetch_assoc($result);
$total_pengguna = $row['total'];

// Calculate total new users today
$total_pengguna_baru_query = "SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()";
$result_baru = mysqli_query($conn, $total_pengguna_baru_query);
$row_baru = mysqli_fetch_assoc($result_baru);
$total_pengguna_baru = $row_baru['total'];

// Calculate waiting reports
$waiting_reports_query = "SELECT COUNT(*) as total FROM reports WHERE status = 'Menunggu'";
$result_reports = mysqli_query($conn, $waiting_reports_query);
$row_reports = mysqli_fetch_assoc($result_reports);
$total_waiting_reports = $row_reports['total'];

// Calculate solved issues
$solved_issues_query = "SELECT COUNT(*) as total FROM reports WHERE status = 'selesai'";
$result_issues = mysqli_query($conn, $solved_issues_query);
$row_issues = mysqli_fetch_assoc($result_issues);
$total_solved_issues = $row_issues['total'];

// Fetch monthly activity data for posts
$monthly_activity_query = "SELECT MONTH(created_at) as month, COUNT(*) as posts FROM posts GROUP BY month ORDER BY month";
$result_activity = mysqli_query($conn, $monthly_activity_query);
$monthly_data = [];
for ($i = 1; $i <= 12; $i++) {
    $monthly_data[$i] = 0; // Default values
}
while ($row_activity = mysqli_fetch_assoc($result_activity)) {
    $monthly_data[$row_activity['month']] = $row_activity['posts'];
}

// Fetch monthly activity data for users
$monthly_users_query = "SELECT MONTH(created_at) as month, COUNT(*) as total FROM users GROUP BY month ORDER BY month";
$result_users = mysqli_query($conn, $monthly_users_query);
$monthly_users_data = [];
for ($i = 1; $i <= 12; $i++) {
    $monthly_users_data[$i] = 0; // Default values
}
while ($row_users = mysqli_fetch_assoc($result_users)) {
    $monthly_users_data[$row_users['month']] = $row_users['total'];
}

// Fetch the latest posts
$latest_posts_query = "SELECT p.id, p.content, p.created_at, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5";
$result_latest_posts = mysqli_query($conn, $latest_posts_query);
$latest_posts = [];
while ($row_latest = mysqli_fetch_assoc($result_latest_posts)) {
    $latest_posts[] = $row_latest;
}

// Close connection
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
                <a href="LaporanMasuk.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span>Laporan Masuk</span>
                </a>
                <a href="#" id="logoutButton" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
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
                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#5865F2] to-[#3b4c8f] p-6 rounded-lg shadow-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Total Pengguna</h3>
                        <span class="text-white">
                            <i class="fas fa-users w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white"><?php echo $total_pengguna; ?></p>
                </div>

                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#3b82f6] to-[#1e40af] p-6 rounded-lg shadow-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Total Pengguna Baru</h3>
                        <span class="text-white">
                            <i class="fas fa-user-plus w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white"><?php echo $total_pengguna_baru; ?></p>
                </div>

                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#f04747] to-[#d32f2f] p-6 rounded-lg shadow-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Laporan Menunggu</h3>
                        <span class="text-white">
                            <i class="fas fa-exclamation-circle w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white"><?php echo $total_waiting_reports; ?></p>
                </div>

                <div class="transform transition-transform duration-200 hover:scale-105 bg-gradient-to-r from-[#34D399] to-[#059669] p-6 rounded-lg shadow-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <h3 class="text-gray-200">Masalah Terselesaikan</h3>
                        <span class="text-white">
                            <i class="fas fa-check-circle w-6 h-6"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-bold mt-2 text-white"><?php echo $total_solved_issues; ?></p>
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
                            <th class="pb-3">Judul Postingan</th>
                            <th class="pb-3">Username</th>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latest_posts as $post): ?>
                            <tr class="border-b border-gray-600 hover:bg-gray-700 transition-colors duration-200">
                                <td class="py-3"><?php echo htmlspecialchars($post['content']); ?></td>
                                <td class="py-3"><?php echo htmlspecialchars($post['username']); ?></td>
                                <td class="py-3"><?php echo date('Y-m-d', strtotime($post['created_at'])); ?></td>
                                <td class="py-3">
                                    <a href="view_post.php?id=<?php echo $post['id']; ?>" class="text-blue-500 hover:underline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Konfirmasi Logout -->
    <div id="logoutModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-[#202225] p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Anda yakin ingin logout?</h2>
            <p class="mb-6">Semua sesi aktif akan diakhiri.</p>
            <div class="flex space-x-4">
                <a href="LogoutAdmin.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-400 transition duration-200">
                    Logout
                </a>
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

        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Postingan Bulanan',
                        data: [<?php echo implode(',', $monthly_data); ?>],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                    },
                    {
                        label: 'Total Pengguna Bulanan',
                        data: [<?php echo implode(',', $monthly_users_data); ?>],
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