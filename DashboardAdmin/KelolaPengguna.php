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
    $stmt = $conn->prepare("UPDATE users SET is_banned = FALSE, ban_reason = NULL, ban_date = NULL WHERE id = ?");
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
        echo json_encode(['success' => $success]);
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
    <title>Pengaturan Pengguna - Forum Anonim</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        select {
            color: black; /* Warna teks hitam */
        }
    </style>
</head>
<body class="bg-[#2f3136] text-gray-100">
    <div class="min-h-screen flex">
        <?php include('sidebaradmin.php'); ?>
                 
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
                            <th class="px-4 py-2 text-gray-200">Alasan Banned</th>
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
                                        <button class="bg-red-500 text-white px-2 py-1 rounded" onclick="confirmBanUser('<?php echo htmlspecialchars($user['username']); ?>', <?php echo $user['id']; ?>)">Banned</button>
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

    <!-- Modal Konfirmasi Tindakan -->
    <div id="confirmModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-[#202225] p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Konfirmasi Tindakan</h2>
            <p id="modalMessage" class="mb-6">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
            <div class="flex space-x-4">
                <button id="confirmButton" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-400 transition duration-200">Ya</button>
                <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200">Batal</button>
            </div>
        </div>
    </div>

    <script>
        let currentAction = null;
        let currentUserId = null;

        function confirmBanUser(username, userId) {
            const reasonSelect = document.getElementById(`banReason${userId}`);
            const reason = reasonSelect.value;

            if (!reason) {
                alert("Silakan pilih alasan untuk mem-banned pengguna.");
                return;
            }

            currentAction = 'ban';
            currentUserId = userId;
            document.getElementById('modalMessage').textContent = `Apakah Anda yakin ingin mem-banned pengguna ${username} dengan alasan: "${reason}"?`;
            document.getElementById('confirmModal').classList.remove('hidden'); // Tampilkan modal
        }

        document.getElementById('confirmButton').addEventListener('click', function() {
            if (currentAction === 'ban') {
                const reason = document.getElementById(`banReason${currentUserId}`).value;
                banUser(currentUserId, reason);
            } else if (currentAction === 'activate') {
                activateUserRequest(currentUserId);
            }
            closeModal();
        });

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden'); // Sembunyikan modal
            currentAction = null;
            currentUserId = null;
        }

        function activateUser(username, userId) {
            currentAction = 'activate';
            currentUserId = userId;
            document.getElementById('modalMessage').textContent = `Apakah Anda yakin ingin mengaktifkan kembali pengguna ${username}?`;
            document.getElementById('confirmModal').classList.remove('hidden'); // Tampilkan modal
        }

        function banUser(userId, reason) {
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
                    alert('Pengguna berhasil di-banned.');
                    location.reload();
                } else {
                    alert('Gagal mem-banned pengguna.');
                }
            });
        }

        function activateUserRequest(userId) {
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
                    alert('Pengguna berhasil diaktifkan kembali.');
                    location.reload();
                } else {
                    alert('Gagal mengaktifkan kembali pengguna.');
                }
            });
        }
    </script>
</body>
</html>

