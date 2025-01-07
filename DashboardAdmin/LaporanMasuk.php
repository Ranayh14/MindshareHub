<?php
include("../conn.php");

// Pastikan request adalah POST dan ID serta status ada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $reportId = $_POST['id'];
    $status = $_POST['status'];

    // Perbarui status laporan di database
    $sql = "UPDATE reports SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $reportId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);  // Kirim respons sukses
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status laporan.']);
    }

    $stmt->close();
    exit;  // Pastikan script berhenti di sini setelah memberikan respons
}

// Menghapus laporan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $reportId = $_POST['delete_id'];

    // Hapus laporan dari database
    $sql = "DELETE FROM reports WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reportId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);  // Kirim respons sukses
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus laporan.']);
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masuk - Forum Anonim</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    </style>
</head>
<body class="bg-[#36393F] text-gray-100">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-[#202225] p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-white">Pusat Admin</h1>
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
                <a href="KelolaKomunitas.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span>Kelola Komunitas</span>
                </a>
                <a href="LaporanMasuk.php" class="flex items-center space-x-2 p-2 rounded-lg bg-[#5865F2] text-white">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span>Laporan Masuk</span>
                </a>
                <a href="#" id="logoutButton" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8 bg-[#36393F]">
            <div class="bg-[#2F3136] p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4 text-white">Lihat Laporan</h2>

                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4 text-white">Laporan Terbaru</h3>
                    
                    <div class="flex mb-4">
                        <input type="text" id="searchInput" placeholder="Cari..." class="w-full p-2 rounded-lg bg-[#202225] text-gray-300 border border-gray-600 focus:outline-none focus:border-[#5865F2]">
                        <select id="filterSelect" class="ml-2 p-2 rounded-lg bg-[#202225] text-gray-300 border border-gray-600 focus:outline-none focus:border-[#5865F2]">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-left text-gray-100 border-collapse">
                            <thead class="bg-[#202225]">
                                <tr>
                                    <th class="px-4 py-2 text-gray-400">No</th>
                                    <th class="px-4 py-2 text-gray-400">Konten Postingan</th>
                                    <th class="px-4 py-2 text-gray-400">Deskripsi</th>
                                    <th class="px-4 py-2 text-gray-400">Tanggal</th>
                                    <th class="px-4 py-2 text-gray-400">Status</th>
                                    <th class="px-4 py-2 text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <?php
                                include("../conn.php");

                                // Ambil laporan dari database dengan join untuk mendapatkan informasi postingan
                                $sql = "
                                    SELECT r.*, p.content AS post_content 
                                    FROM reports r 
                                    JOIN posts p ON r.post_id = p.id 
                                    ORDER BY r.created_at DESC
                                ";
                                $result = mysqli_query($conn, $sql);
                                $no = 1;

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status = isset($row['status']) ? $row['status'] : 'Menunggu';
                                    echo '<tr class="hover:bg-[#35393f]">';
                                    echo '<td class="px-4 py-2">' . $no++ . '</td>';
                                    echo '<td class="px-4 py-2">' . htmlspecialchars($row['post_content']) . '</td>'; 
                                    echo '<td class="px-4 py-2">' . htmlspecialchars($row['description']) . '</td>';
                                    echo '<td class="px-4 py-2">' . htmlspecialchars($row['created_at']) . '</td>';
                                    echo '<td class="px-4 py-2 ' . ($status == 'Selesai' ? 'text-green-400' : 'text-yellow-400') . '">' . htmlspecialchars($status) . '</td>';
                                    echo '<td class="px-4 py-2">';
                                    echo '<a href="DetailPostingan.php?id=' . $row['post_id'] . '" class="text-blue-500 hover:text-blue-700" title="Lihat Postingan"><i class="fas fa-eye"></i></a>'; 
                                    echo ' <button onclick="updateStatus(' . $row['id'] . ', this)" class="bg-yellow-500 text-white px-2 py-1 rounded">Selesaikan</button>';
                                    echo ' <button onclick="deleteReport(' . $row['id'] . ', this)" class="bg-red-500 text-white px-2 py-1 rounded ml-2"><i class="fas fa-trash"></i></button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                mysqli_close($conn);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const filterSelect = document.getElementById('filterSelect');
        const reportTableBody = document.getElementById('reportTableBody');

        // Filter function
        function filterReports() {
            const searchTerm = searchInput.value.toLowerCase();
            const filterStatus = filterSelect.value.toLowerCase();
            const rows = reportTableBody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const cells = row.getElementsByTagName('td');
                const issue = cells[1].textContent.toLowerCase();
                const status = cells[4].textContent.toLowerCase();

                const matchesSearch = issue.includes(searchTerm);
                const matchesFilter = filterStatus === '' || status.includes(filterStatus);

                row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterReports);
        filterSelect.addEventListener('change', filterReports);

        // Update status function
function updateStatus(reportId, button) {
    if (confirm('Apakah Anda yakin ingin menyelesaikan laporan ini?')) {
        fetch('LaporanMasuk.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id': reportId,
                'status': 'Selesai',
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Laporan berhasil diselesaikan!');

                // Update the row status without refreshing
                const row = button.closest('tr');
                const statusCell = row.getElementsByTagName('td')[4];

                statusCell.textContent = 'Selesai';
                statusCell.classList.remove('text-yellow-400');
                statusCell.classList.add('text-green-400');

                button.textContent = 'Terselesaikan';
                button.classList.remove('bg-yellow-500');
                button.classList.add('bg-green-500');
                button.disabled = true; // Disable the button after completion
            } else {
                alert('Gagal menyelesaikan laporan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Terjadi kesalahan:', error);
            alert('Terjadi kesalahan, coba lagi nanti.');
        });
    }
}

        // Delete report function
        function deleteReport(reportId, button) {
            if (confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
                fetch('LaporanMasuk.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'delete_id': reportId,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Laporan berhasil dihapus!');
                        button.closest('tr').remove(); // Remove row from table
                    } else {
                        alert('Gagal menghapus laporan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Terjadi kesalahan:', error);
                    alert('Terjadi kesalahan, coba lagi nanti.');
                });
            }
        }
    </script>

</body>
</html>
