<?php
session_start();
include("../conn.php");

// Cek apakah form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        $title = $_POST['contentTitle'];
        $notes = $_POST['contentNotes'];

        // Simpan konten ke database
        $sql = "INSERT INTO content (title, notes) VALUES ('$title', '$notes')";
        if (mysqli_query($conn, $sql)) {
            // Ambil ID pengguna dari session
            $user_id = $_SESSION['user_id']; // Pastikan session user_id sudah ada

            // Simpan notifikasi ke database
            $sql_notifications = "INSERT INTO notifications (user_id, title, notes) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql_notifications);
            $stmt->bind_param("iss", $user_id, $title, $notes);
            $stmt->execute();
            $stmt->close();

            // Redirect setelah menambah konten
            header("Location: KelolaKonten.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($action == 'delete') {
        $contentId = $_POST['contentId'];
        $sql = "DELETE FROM content WHERE id = $contentId";
        mysqli_query($conn, $sql);

        // Redirect setelah menghapus konten
        header("Location: KelolaKonten.php");
        exit();
    }
}

// Ambil semua konten dari database
$result = mysqli_query($conn, "SELECT * FROM content ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konten - Forum Anonim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#2f3136] text-gray-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-[#202225] p-6">
            <h1 class="text-2xl font-semibold text-white mb-6">Pusat Admin</h1>
            <nav class="space-y-4">
                <a href="DashboardAdmin.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-home w-5 h-5"></i>
                    <span>Beranda</span>
                </a>
                <a href="KelolaPengguna.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-user-friends w-5 h-5"></i>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="KelolaKonten.php" class="flex items-center space-x-2 p-2 rounded-lg bg-[#5865F2] text-white">
                    <i class="fas fa-file-alt w-5 h-5"></i>
                    <span>Kelola Konten</span>
                </a>
                <a href="KelolaKomunitas.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span>Kelola Komunitas</span>
                </a>
                <a href="laporanMasuk.html" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span>Laporan Masuk</span>
                </a>
                <a href="LogoutAdmin.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold">Kelola Konten</h2>
                <button class="bg-[#5865F2] text-white px-4 py-2 rounded-lg hover:bg-[#4752C4] transition duration-200" onclick="toggleForm()">
                    Tambah Konten
                </button>
                <div id="contentForm" class="hidden mt-4 bg-[#2f3136] p-4 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold">Tambah Konten Baru</h3>
                    <form method="POST" id="addContentForm">
                        <input type="hidden" name="action" value="add">
                        <div class="mt-2">
                            <label class="block text-gray-300">Judul Konten:</label>
                            <input type="text" name="contentTitle" class="mt-1 p-2 w-full bg-[#202225] text-gray-100 rounded" required>
                        </div>
                        <div class="mt-2">
                            <label class="block text-gray-300">Catatan:</label>
                            <textarea name="contentNotes" class="mt-1 p-2 w-full bg-[#202225] text-gray-100 rounded" rows="4" required></textarea>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-400 transition duration-200">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg">
                <table class="min-w-full table-auto text-left">
                    <thead class="bg-[#202225]">
                        <tr>
                            <th class="px-4 py-2 text-gray-400">No</th>
                            <th class="px-4 py-2 text-gray-400">Judul Konten</th>
                            <th class="px-4 py-2 text-gray-400">Tanggal</th>
                            <th class="px-4 py-2 text-gray-400">Status</th>
                            <th class="px-4 py-2 text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="contentTable">
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr class="hover:bg-[#35393f]">';
                            echo '<td class="px-4 py-2">' . $no++ . '</td>';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($row['title']) . '</td>';
                            echo '<td class="px-4 py-2">' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                            echo '<td class="px-4 py-2 text-green-400">Dipublikasikan</td>';
                            echo '<td class="px-4 py-2">
                                    <button class="text-yellow-500 hover:text-yellow-300" onclick="editContent(' . $row['id'] . ', \'' . htmlspecialchars($row['title']) . '\', \'' . htmlspecialchars($row['notes']) . '\')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus konten ini?\');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="contentId" value="' . $row['id'] . '">
                                        <button type="submit" class="text-red-500 hover:text-red-300 ml-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                  </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('contentForm');
            form.classList.toggle('hidden');
            document.getElementById('addContentForm').reset(); // Reset form saat menambah konten
        }

        function editContent(id, title, notes) {
            const form = document.getElementById('contentForm');
            form.classList.remove('hidden');
            document.querySelector('input[name="action"]').value = 'edit';
            document.querySelector('input[name="contentId"]').value = id;
            document.querySelector('input[name="contentTitle"]').value = title;
            document.querySelector('textarea[name="contentNotes"]').value = notes;
        }
    </script>
</body>
</html>

<?php
// Menutup koneksi
mysqli_close($conn);
?>