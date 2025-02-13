<?php
session_start();
include("../conn.php");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil roles pengguna
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT roles FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    die("Pengguna tidak ditemukan.");
}

$user = $result_user->fetch_assoc();
$is_admin = ($user['roles'] === 'admin'); // Memperbaiki pengecekan

// Cek apakah pengguna adalah admin
if (!$is_admin) {
    die("Akses ditolak. Hanya admin yang dapat mengakses halaman ini.");
}

// Cek apakah form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        $title = $_POST['contentTitle'];
        $notes = $_POST['contentNotes'];

        // Validasi input
        if (!empty($title) && !empty($notes)) {
            // Simpan konten ke database dengan user_id admin
            $admin_user_id = $user_id; // Menggunakan ID admin dari sesi
            $sql = "INSERT INTO content (user_id, title, notes) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $admin_user_id, $title, $notes);

            if ($stmt->execute()) {
                // Simpan notifikasi ke database untuk semua pengguna biasa
                $sql_users = "SELECT id FROM users WHERE roles = 'user'"; // Ambil semua pengguna biasa
                $result_users = $conn->query($sql_users);

                while ($row_user = $result_users->fetch_assoc()) {
                    $user_to_notify_id = $row_user['id'];
                    $sql_notifications = "INSERT INTO notifications (user_id, title, notes) VALUES (?, ?, ?)";
                    $stmt_notifications = $conn->prepare($sql_notifications);
                    $stmt_notifications->bind_param("iss", $user_to_notify_id, $title, $notes);
                    $stmt_notifications->execute();
                    $stmt_notifications->close();
                }

                // Redirect setelah menambah konten
                header("Location: KelolaKonten.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Judul dan catatan tidak boleh kosong.";
        }
    } elseif ($action == 'delete') {
        $contentId = $_POST['contentId'];
        $sql = "DELETE FROM content WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $contentId);
        $stmt->execute();
        $stmt->close();

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
    <title>Manajemen Konten - Forum Anonim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#2f3136] text-gray-100">
    <div class="min-h-screen flex">

    
            <?php include('sidebaradmin.php'); ?>
          

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
        function toggleForm() {
            const form = document.getElementById('contentForm');
            form.classList.toggle('hidden');
            document.getElementById('addContentForm').reset(); // Reset form saat menambah konten
        }

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
    </script>
</body>
</html>

<?php
// Menutup koneksi
mysqli_close($conn);
?>