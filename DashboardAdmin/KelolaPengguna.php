<?php
include("../conn.php"); // Pastikan Anda sudah memiliki koneksi ke database

// Fungsi untuk mem-banned pengguna
function banUser($userId, $reason) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET is_banned = TRUE, ban_reason = ?, ban_date = NOW() WHERE id = ?");
    $stmt->bind_param("si", $reason, $userId);
    return $stmt->execute();
}

// Fungsi untuk mengaktifkan kembali pengguna
function activateUser($userId) {
    global $conn;
    // Hanya menghapus status banned dan tanggal banned
    $stmt = $conn->prepare("UPDATE users SET is_banned = FALSE, ban_date = NULL WHERE id = ?");
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
}

// Fungsi untuk mengambil semua pengguna (hanya yang bukan admin)
function getAllUsers() {
    global $conn;
    $result = $conn->query("SELECT id, username, email, is_banned, ban_reason FROM users WHERE roles = 'user'");

    if (!$result) {
        die("Query Error: " . $conn->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Menangani permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'ban') {
        $userId = $data['userId'];
        $reason = $data['reason'];
        $success = banUser($userId, $reason);
        echo json_encode(['success' => $success]);
    } elseif ($data['action'] === 'activate') {
        $userId = $data['userId'];
        $success = activateUser($userId);
        
        // Ambil alasan banned dari database
        $stmt = $conn->prepare("SELECT ban_reason FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $ban_reason = $result->fetch_assoc()['ban_reason'];
        
        echo json_encode(['success' => $success, 'ban_reason' => $ban_reason]);
    }
    exit;
}

// Ambil semua pengguna
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Forum Anonim</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        /* Gaya untuk dropdown */
        select {
            color: black; /* Warna teks hitam */
        }
    </style>
</head>
<body class="bg-[#2f3136] text-gray-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-[#202225] p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-white">PusatAdmin</h1>
            </div>
            <nav class="space-y-4">
                <a href="DashboardAdmin.html" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-home w-5 h-5"></i>
                    <span>Beranda</span>
                </a>
                <a href="KelolaPengguna.php" class="flex items-center space-x-2 p-2 rounded-lg bg-[#5865F2] text-white">
                    <i class="fas fa-user-friends w-5 h-5"></i>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="KelolaKonten.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-file-alt w-5 h-5"></i>
                    <span>Kelola Konten</span>
                </a>
                <a href="KelolaKomunitas.html" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span>Kelola Komunitas</span>
                </a>
                <a href="LaporanMasuk.html" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span>Laporan Masuk</span>
                </a>
                
                <a href="#" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold">Kelola Pengguna</h2>
            </div>

            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg">
                <table class="min-w-full table-auto text-left">
                    <thead class="bg-[#202225]">
                        <tr>
                            <th class="px-4 py-2 text-gray-200">No</th>
                            <th class="px-4 py-2 text-gray-200">Nama Pengguna</th>
                            <th class="px-4 py-2 text-gray-200">Email</th>
                            <th class="px-4 py-2 text-gray-200">Status</th>
                            <th class="px-4 py-2 text-gray-200">Aksi</th>
                            <th class="px-4 py-2 text-gray-200">Alasan Banned</th> <!-- Kolom untuk alasan banned -->
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        <?php foreach ($users as $index => $user): ?>
                            <tr class="hover:bg-[#35393f]" id="user<?php echo $user['id']; ?>">
                                <td class="px-4 py-2 text-gray-300"><?php echo $index + 1; ?></td>
                                <td class="px-4 py-2 text-gray-300"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="px-4 py-2 text-gray-300"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-4 py-2 <?php echo $user['is_banned'] ? 'text-red-400' : 'text-green-400'; ?>">
                                    <?php echo $user['is_banned'] ? 'Nonaktif' : 'Aktif'; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if ($user['is_banned']): ?>
                                        <button class="bg-green-500 text-white px-2 py-1 rounded" onclick="activateUser('<?php echo htmlspecialchars($user['username']); ?>', <?php echo $user['id']; ?>)">Aktifkan</button>
                                    <?php else: ?>
                                        <select id="banReason<?php echo $user['id']; ?>" class="border rounded mb-2 text-black">
                                            <option value="">Pilih Alasan</option>
                                            <option value="Pelanggaran Aturan">Pelanggaran Aturan</option>
                                            <option value="Spam">Spam</option>
                                            <option value="Penyalahgunaan">Penyalahgunaan</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                        <button class="bg-red-500 text-white px-2 py-1 rounded" onclick="banUser('<?php echo htmlspecialchars($user['username']); ?>', <?php echo $user['id']; ?>)">Banned</button>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-gray-300" id="reason<?php echo $user['id']; ?>">
                                    <?php echo $user['ban_reason'] ? htmlspecialchars($user['ban_reason']) : 'Tidak ada alasan'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function banUser(username, userId) {
            const reasonSelect = document.getElementById(`banReason${userId}`);
            const reason = reasonSelect.value;

            if (!reason) {
                alert("Silakan pilih alasan untuk mem-banned pengguna.");
                return;
            }

            if (confirm(`Apakah Anda yakin ingin mem-banned pengguna ${username} dengan alasan: "${reason}"?`)) {
                fetch('KelolaPengguna.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'ban', userId: userId, reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const userRow = document.getElementById(`user${userId}`);
                        const statusCell = userRow.querySelector('td:nth-child(4)');
                        statusCell.textContent = 'Nonaktif';
                        statusCell.classList.remove('text-green-400');
                        statusCell.classList.add('text-red-400');

                        const actionCell = userRow.querySelector('td:nth-child(5)');
                        actionCell.innerHTML = '<button class="bg-green-500 text-white px-2 py-1 rounded" onclick="activateUser(\'' + username + '\', ' + userId + ')">Aktifkan</button>';

                        const reasonCell = userRow.querySelector('td:nth-child(6)');
                        reasonCell.textContent = reason; // Update alasan banned

                        alert(`Pengguna ${username} telah dibanned dengan alasan: "${reason}".`);
                    } else {
                        alert('Gagal mem-banned pengguna.');
                    }
                });
            }
        }

        function activateUser(username, userId) {
            if (confirm(`Apakah Anda yakin ingin mengaktifkan kembali pengguna ${username}?`)) {
                fetch('KelolaPengguna.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'activate', userId: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const userRow = document.getElementById(`user${userId}`);
                        const statusCell = userRow.querySelector('td:nth-child(4)');
                        statusCell.textContent = 'Aktif';
                        statusCell.classList.remove('text-red-400');
                        statusCell.classList.add('text-green-400');

                        const actionCell = userRow.querySelector('td:nth-child(5)');
                        actionCell.innerHTML = '<button class="bg-red-500 text-white px-2 py-1 rounded" onclick="banUser(\'' + username + '\', ' + userId + ')">Banned</button>';

                        // Update alasan banned saat diaktifkan kembali
                        const reasonCell = userRow.querySelector('td:nth-child(6)');
                        reasonCell.textContent = data.ban_reason ? data.ban_reason : 'Tidak ada alasan';

                        alert(`Pengguna ${username} telah diaktifkan kembali.`);
                    } else {
                        alert('Gagal mengaktifkan pengguna.');
                    }
                });
            }
        }
    </script>
</body>
</html>